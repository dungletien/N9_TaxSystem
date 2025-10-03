<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Deduction;
use App\Models\MonthTax;
use App\Models\YearTax;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AccountantController extends Controller
{
    public function dashboard()
    {
        if (!Session::has('user') || Session::get('user.user_type') !== 'ke-toan') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập!');
        }

        return view('accountant.dashboard');
    }

    public function getEmployees(Request $request)
    {
        $department = $request->get('department', 'all');

        $query = User::query();

        if ($department !== 'all') {
            $query->where('department', $department);
        }

        $employees = $query->get();

        return response()->json($employees);
    }

    public function deleteEmployee($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['success' => true, 'message' => 'Xóa nhân viên thành công!']);
        }

        return response()->json(['success' => false, 'message' => 'Không tìm thấy nhân viên!']);
    }

    public function createAccount(Request $request)
    {
        $request->validate([
            'id' => 'required|string|size:10|unique:users,id',
            'full_name' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'department' => 'required|in:marketing,sales,nhân sự,kinh doanh',
            'position' => 'required|in:nhân viên',
            'phone' => 'required|string|size:10|unique:users,phone',
            'cccd' => 'required|string|size:12|unique:users,cccd',
            'role' => 'required|in:ke-toan,nhan-vien,truong-phong',
        ]);

        // Tạo tài khoản user
        $user = User::create([
            'id' => $request->id,
            'full_name' => $request->full_name,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'position' => $request->position,
            'phone' => $request->phone,
            'cccd' => $request->cccd,
            'gender' => 'Nam', // Default
        ]);

        // Tạo role cho user
        UserRole::create([
            'user_id' => $user->id,
            'user_type' => $request->role,
        ]);

        return response()->json(['success' => true, 'message' => 'Tạo tài khoản thành công!']);
    }

    public function getAccounts()
    {
        $accounts = User::select('id', 'full_name', 'department', 'position', 'cccd')->get();
        return response()->json($accounts);
    }

    public function getDeductions(Request $request)
    {
        $year = $request->get('year');
        $deductions = Deduction::where('year', $year)->orderBy('month')->get();
        return response()->json($deductions);
    }

    public function setupDeductions(Request $request)
    {
        $deductions = $request->all();

        foreach ($deductions as $deduction) {
            Deduction::updateOrCreate(
                ['month' => $deduction['month'], 'year' => $deduction['year']],
                [
                    'self_deduction' => $deduction['self_deduction'],
                    'dependent_deduction' => $deduction['dependent_deduction'],
                ]
            );
        }

        return response()->json(['message' => 'Thiết lập giảm trừ thành công!']);
    }

    public function getSalaries(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');
        $department = $request->get('department', 'all');

        if (!$month || !$year) {
            return response()->json(['error' => 'Thiếu thông tin tháng hoặc năm!']);
        }

        // Lấy mức giảm trừ
        $deduction = Deduction::where('month', $month)->where('year', $year)->first();

        if (!$deduction) {
            return response()->json(['error' => 'Chưa thiết lập mức giảm trừ cho tháng và năm này.']);
        }

        $query = User::leftJoin('month_taxes', function($join) use ($month, $year) {
            $join->on('users.id', '=', 'month_taxes.user_id')
                 ->where('month_taxes.month', $month)
                 ->where('month_taxes.year', $year);
        })->select('users.*', 'month_taxes.salary', 'month_taxes.tax', 'month_taxes.net_salary');

        if ($department !== 'all') {
            $query->where('users.department', $department);
        }

        $employees = $query->get();

        return response()->json([
            'employees' => $employees,
            'deduction' => $deduction,
        ]);
    }

    public function saveSalaries(Request $request)
    {
        $salaries = $request->get('salaries');

        foreach ($salaries as $employee) {
            MonthTax::updateOrCreate(
                [
                    'user_id' => $employee['id'],
                    'month' => $employee['month'],
                    'year' => $employee['year'],
                ],
                [
                    'salary' => $employee['salary'],
                    'tax' => $employee['tax'],
                    'net_salary' => $employee['net_salary'],
                ]
            );
        }

        return response()->json(['message' => 'Dữ liệu lương đã được lưu thành công!']);
    }

    public function getAnnualTax(Request $request)
    {
        $year = $request->get('year');
        $department = $request->get('department', 'all');
        $currentYear = date('Y');

        if (!$year || $year >= $currentYear) {
            return response()->json(['error' => 'Năm không hợp lệ hoặc chưa kết thúc!']);
        }

        // Lấy mức giảm trừ của năm
        $deduction = Deduction::where('year', $year)->first();

        if (!$deduction) {
            return response()->json(['error' => 'Chưa thiết lập mức giảm trừ cho năm này.']);
        }

        // Lấy danh sách nhân viên với dữ liệu quyết toán (nếu có)
        $query = User::leftJoin('year_taxes', function($join) use ($year) {
            $join->on('users.id', '=', 'year_taxes.user_id')
                 ->where('year_taxes.year', $year);
        })->select('users.*', 'year_taxes.total_salary', 'year_taxes.total_tax', 'year_taxes.net_salary');

        if ($department !== 'all') {
            $query->where('users.department', $department);
        }

        $employees = $query->get();

        // Thêm dữ liệu monthly summary cho mỗi nhân viên
        foreach ($employees as $employee) {
            $monthlyData = MonthTax::where('user_id', $employee->id)
                                   ->where('year', $year)
                                   ->selectRaw('SUM(salary) as monthly_total_salary, SUM(tax) as monthly_total_tax, SUM(net_salary) as monthly_net_salary')
                                   ->first();
            
            // Ưu tiên dữ liệu đã quyết toán, nếu không có thì dùng tổng monthly
            if (!$employee->total_salary && $monthlyData) {
                $employee->total_salary = $monthlyData->monthly_total_salary ?? 0;
                $employee->total_tax = $monthlyData->monthly_total_tax ?? 0;
                $employee->net_salary = $monthlyData->monthly_net_salary ?? 0;
            }
        }

        return response()->json([
            'employees' => $employees,
            'deduction' => $deduction,
        ]);
    }

    public function saveAnnualTax(Request $request)
    {
        $taxes = $request->get('taxes');

        foreach ($taxes as $employee) {
            YearTax::updateOrCreate(
                [
                    'user_id' => $employee['id'],
                    'year' => $employee['year'],
                ],
                [
                    'total_salary' => $employee['total_salary'],
                    'total_tax' => $employee['total_tax'],
                    'net_salary' => $employee['net_salary'],
                ]
            );
        }

        return response()->json(['message' => 'Dữ liệu quyết toán thuế đã được lưu thành công!']);
    }

    public function calculateAnnualTax(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $year = $request->get('year');

        // Tính tổng lương và thuế từ dữ liệu hàng tháng
        $monthlyData = MonthTax::where('user_id', $employeeId)
                              ->where('year', $year)
                              ->get();

        $totalSalary = $monthlyData->sum('salary');
        $totalTax = $monthlyData->sum('tax');
        $netSalary = $totalSalary - $totalTax;

        // Lấy thông tin nhân viên để tính lại thuế nếu cần
        $user = User::find($employeeId);
        $deduction = Deduction::where('year', $year)->first();

        if ($user && $deduction && $totalSalary > 0) {
            // Tính lại thuế theo quy định quyết toán thuế hàng năm
            $selfDeduction = $deduction->self_deduction * 12; // Giảm trừ cả năm
            $dependentDeduction = $deduction->dependent_deduction * 12 * ($user->dependent ?? 0);
            
            $annualTaxableIncome = $totalSalary - $selfDeduction - $dependentDeduction;
            $annualTax = $this->calculateAnnualPersonalIncomeTax($annualTaxableIncome);
            
            // So sánh với thuế đã tạm nộp để xác định thuế còn thiếu hoặc hoàn trả
            $taxDifference = $annualTax - $totalTax;
            
            return response()->json([
                'total_salary' => $totalSalary,
                'total_tax' => $annualTax,
                'net_salary' => $totalSalary - $annualTax,
                'tax_paid' => $totalTax,
                'tax_difference' => $taxDifference,
                'status' => $taxDifference > 0 ? 'Còn thiếu' : ($taxDifference < 0 ? 'Được hoàn' : 'Đủ')
            ]);
        }

        return response()->json([
            'total_salary' => $totalSalary,
            'total_tax' => $totalTax,
            'net_salary' => $netSalary,
            'tax_difference' => 0,
            'status' => 'Chưa có dữ liệu đầy đủ'
        ]);
    }

    private function calculateAnnualPersonalIncomeTax($annualTaxableIncome)
    {
        $taxAmount = 0;

        if ($annualTaxableIncome > 0) {
            // Bậc thuế theo năm (x12 so với tháng)
            if ($annualTaxableIncome <= 60000000) { // 5tr x 12
                $taxAmount = $annualTaxableIncome * 0.05;
            } elseif ($annualTaxableIncome <= 120000000) { // 10tr x 12
                $taxAmount = $annualTaxableIncome * 0.10 - 3000000;
            } elseif ($annualTaxableIncome <= 216000000) { // 18tr x 12
                $taxAmount = $annualTaxableIncome * 0.15 - 9000000;
            } elseif ($annualTaxableIncome <= 384000000) { // 32tr x 12
                $taxAmount = $annualTaxableIncome * 0.20 - 19800000;
            } elseif ($annualTaxableIncome <= 624000000) { // 52tr x 12
                $taxAmount = $annualTaxableIncome * 0.25 - 39000000;
            } elseif ($annualTaxableIncome <= 960000000) { // 80tr x 12
                $taxAmount = $annualTaxableIncome * 0.30 - 70200000;
            } else {
                $taxAmount = $annualTaxableIncome * 0.35 - 118200000;
            }
        }

        return max(0, $taxAmount);
    }
}
