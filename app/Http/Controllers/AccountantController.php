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
use Illuminate\Support\Facades\Log;

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
        $search = $request->get('search', '');

        $query = User::query();

        if ($department !== 'all') {
            $query->where('department', $department);
        }

        // Thêm tìm kiếm nếu có
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhere('full_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('cccd', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('department', 'LIKE', '%' . $search . '%');
            });
        }

        $employees = $query->orderBy('full_name', 'asc')->get();

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

    public function getEmployee($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy nhân viên!']);
            }

            // Lấy role từ bảng user_roles
            $userRole = UserRole::where('user_id', $id)->first();

            $userData = $user->toArray();
            $userData['role'] = $userRole ? $userRole->user_type : null;

            return response()->json(['success' => true, 'employee' => $userData]);

        } catch (\Exception $e) {
            Log::error('Error getting employee: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function updateEmployee(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy nhân viên!']);
            }

            $request->validate([
                'full_name' => 'required|string|max:100',
                'department' => 'required|in:marketing,sales,nhân sự,kinh doanh',
                'position' => 'required|in:nhân viên',
                'phone' => 'required|string|size:10|unique:users,phone,' . $id,
                'cccd' => 'required|string|size:12|unique:users,cccd,' . $id,
                'role' => 'required|in:ke-toan,nhan-vien,truong-phong',
                'gender' => 'nullable|in:Nam,Nữ',
                'address' => 'nullable|string|max:255',
                'dependent' => 'nullable|integer|min:0',
                'password' => 'nullable|string|min:6',
            ]);

            // Chuẩn bị dữ liệu để cập nhật
            $updateData = [
                'full_name' => $request->full_name,
                'department' => $request->department,
                'position' => $request->position,
                'phone' => $request->phone,
                'cccd' => $request->cccd,
                'gender' => $request->gender ?? $user->gender,
                'address' => $request->address,
                'dependent' => $request->dependent,
            ];

            // Cập nhật mật khẩu nếu có
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                Log::info('Password updated for user: ' . $id);
            }

            // Cập nhật thông tin user
            $user->update($updateData);

            Log::info('User updated successfully: ' . $id, $updateData);

            // Cập nhật role nếu thay đổi
            $currentRole = $user->userRoles()->first();
            if ($currentRole && $currentRole->user_type !== $request->role) {
                $currentRole->delete();
                UserRole::create([
                    'user_id' => $user->id,
                    'user_type' => $request->role,
                ]);
            } elseif (!$currentRole) {
                UserRole::create([
                    'user_id' => $user->id,
                    'user_type' => $request->role,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin nhân viên thành công!' . ($request->filled('password') ? ' Mật khẩu đã được thay đổi.' : '')
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()], 500);
        }
    }

    public function createAccount(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|string|max:10|unique:users,id',
                'full_name' => 'required|string|max:100',
                'password' => 'required|string|min:6',
                'department' => 'required|in:marketing,sales,nhân sự,kinh doanh',
                'position' => 'required|in:nhân viên',
                'phone' => 'required|string|size:10|unique:users,phone',
                'cccd' => 'required|string|size:12|unique:users,cccd',
                'role' => 'required|in:ke-toan,nhan-vien,truong-phong',
                'gender' => 'sometimes|in:Nam,Nữ',
                'address' => 'sometimes|string|max:255'
            ]);

            DB::beginTransaction();

            // Tạo tài khoản user
            $user = User::create([
                'id' => $request->id,
                'full_name' => $request->full_name,
                'password' => Hash::make($request->password),
                'department' => $request->department,
                'position' => $request->position,
                'phone' => $request->phone,
                'cccd' => $request->cccd,
                'gender' => $request->gender ?? 'Nam',
                'address' => $request->address
            ]);

            // Tạo role cho user
            UserRole::create([
                'user_id' => $user->id,
                'user_type' => $request->role,
            ]);

            DB::commit();

            Log::info("Account created for user {$user->id} by accountant");

            return response()->json([
                'success' => true,
                'message' => "Tạo tài khoản thành công cho nhân viên {$user->full_name}!"
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo tài khoản: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAccounts(Request $request)
    {
        try {
            $role = $request->get('role', 'all');
            $search = $request->get('search', '');

            $query = User::leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id');

            if ($role !== 'all') {
                $query->where('user_roles.user_type', $role);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.id', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.full_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.department', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.phone', 'LIKE', '%' . $search . '%');
                });
            }

            $accounts = $query->select(
                'users.*',
                'user_roles.user_type as role'
            )->orderBy('users.full_name', 'asc')->get();

            return response()->json($accounts);
        } catch (\Exception $e) {
            Log::error('Error getting accounts: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi tải danh sách tài khoản!'], 500);
        }
    }

    public function getDeductions(Request $request)
    {
        $year = $request->get('year');
        $deductions = Deduction::where('year', $year)->orderBy('month')->get();
        return response()->json($deductions);
    }

    public function setupDeductions(Request $request)
    {
        try {
            $deductions = $request->input('deductions');

            if (!$deductions || !is_array($deductions)) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ!'], 400);
            }

            foreach ($deductions as $deduction) {
                // Validate required fields
                if (!isset($deduction['month']) || !isset($deduction['year'])) {
                    continue;
                }

                Deduction::updateOrCreate(
                    [
                        'month' => (int) $deduction['month'],
                        'year' => (int) $deduction['year']
                    ],
                    [
                        'self_deduction' => (float) ($deduction['self_deduction'] ?? 11000000),
                        'dependent_deduction' => (float) ($deduction['dependent_deduction'] ?? 4400000),
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Thiết lập giảm trừ thành công!']);

        } catch (\Exception $e) {
            Log::error('Error saving deductions: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi lưu thiết lập: ' . $e->getMessage()], 500);
        }
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

        $query = User::leftJoin('month_taxes', function ($join) use ($month, $year) {
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
        try {
            $salaries = $request->input('salaries');

            if (!$salaries || !is_array($salaries)) {
                return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ!'], 400);
            }

            foreach ($salaries as $salaryData) {
                // Validate required fields
                if (!isset($salaryData['id']) || !isset($salaryData['month']) || !isset($salaryData['year'])) {
                    continue;
                }

                $userId = $salaryData['id'];
                $month = (int) $salaryData['month'];
                $year = (int) $salaryData['year'];
                $salary = (float) ($salaryData['salary'] ?? 0);
                $tax = (float) ($salaryData['tax'] ?? 0);
                $netSalary = (float) ($salaryData['net_salary'] ?? 0);

                // Use raw SQL to handle composite primary key upsert
                DB::statement('
                    INSERT INTO month_taxes (user_id, month, year, salary, tax, net_salary, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE
                    salary = VALUES(salary),
                    tax = VALUES(tax),
                    net_salary = VALUES(net_salary),
                    updated_at = NOW()
                ', [$userId, $month, $year, $salary, $tax, $netSalary]);
            }

            return response()->json(['success' => true, 'message' => 'Dữ liệu lương đã được lưu thành công!']);

        } catch (\Exception $e) {
            Log::error('Error saving salaries: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi lưu dữ liệu: ' . $e->getMessage()], 500);
        }
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
        $query = User::leftJoin('year_taxes', function ($join) use ($year) {
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

    public function resetPassword($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tài khoản!'
                ], 404);
            }

            $user->password = Hash::make('123456');
            $user->save();

            Log::info("Password reset for user {$id} by accountant");

            return response()->json([
                'success' => true,
                'message' => "Đặt lại mật khẩu thành công cho {$user->full_name}"
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt lại mật khẩu!'
            ], 500);
        }
    }
}
