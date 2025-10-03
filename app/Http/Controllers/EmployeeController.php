<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MonthTax;
use App\Models\YearTax;
use App\Models\Deduction;
use Illuminate\Support\Facades\Session;

class EmployeeController extends Controller
{
    private function checkAuth()
    {
        if (!Session::has('user') || Session::get('user.user_type') !== 'nhan-vien') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập!');
        }
        return null;
    }

    public function dashboard()
    {
        if (!Session::has('user') || Session::get('user.user_type') !== 'nhan-vien') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập!');
        }

        $user = User::find(Session::get('user.id'));
        return view('employee.dashboard', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'required|in:Nam,Nữ',
            'address' => 'required|string|max:255',
            'dependent' => 'required|integer|min:0',
            'phone' => 'required|string|size:10|unique:users,phone,' . Session::get('user.id'),
        ]);

        $user = User::find(Session::get('user.id'));
        $user->update($request->all());

        return back()->with('success', 'Thông tin đã được cập nhật thành công!');
    }

    public function getSalaries(Request $request)
    {
        $year = $request->get('year');
        $userId = Session::get('user.id');

        $salaries = MonthTax::where('user_id', $userId)
                           ->where('year', $year)
                           ->orderBy('month')
                           ->get();

        return response()->json($salaries);
    }

    public function calculateTax(Request $request)
    {
        $request->validate([
            'salary' => 'required|numeric|min:0',
            'dependent' => 'required|integer|min:0',
        ]);

        $salary = $request->salary;
        $dependent = $request->dependent;

        // Lấy mức giảm trừ hiện tại (có thể lấy từ tháng hiện tại)
        $currentMonth = date('n');
        $currentYear = date('Y');

        $deduction = Deduction::where('month', $currentMonth)
                             ->where('year', $currentYear)
                             ->first();

        if (!$deduction) {
            // Nếu không có, lấy mức giảm trừ mặc định
            $selfDeduction = 11000000; // 11 triệu
            $dependentDeduction = 4400000; // 4.4 triệu
        } else {
            $selfDeduction = $deduction->self_deduction;
            $dependentDeduction = $deduction->dependent_deduction;
        }

        $taxResult = $this->calculatePersonalIncomeTax($salary, $selfDeduction, $dependentDeduction, $dependent);

        return response()->json($taxResult);
    }

    private function calculatePersonalIncomeTax($salary, $selfDeduction, $dependentDeduction, $dependent)
    {
        $deductionsForDependents = $dependentDeduction * $dependent;
        $taxableIncome = $salary - $selfDeduction - $deductionsForDependents;

        $taxAmount = 0;

        if ($taxableIncome > 0) {
            if ($taxableIncome <= 5000000) {
                $taxAmount = $taxableIncome * 0.05;
            } elseif ($taxableIncome <= 10000000) {
                $taxAmount = $taxableIncome * 0.10 - 250000;
            } elseif ($taxableIncome <= 18000000) {
                $taxAmount = $taxableIncome * 0.15 - 750000;
            } elseif ($taxableIncome <= 32000000) {
                $taxAmount = $taxableIncome * 0.20 - 1650000;
            } elseif ($taxableIncome <= 52000000) {
                $taxAmount = $taxableIncome * 0.25 - 3250000;
            } elseif ($taxableIncome <= 80000000) {
                $taxAmount = $taxableIncome * 0.30 - 5850000;
            } else {
                $taxAmount = $taxableIncome * 0.35 - 9850000;
            }
        }

        $netSalary = $salary - $taxAmount;

        return [
            'tax' => $taxAmount,
            'netSalary' => $netSalary,
            'taxableIncome' => $taxableIncome,
        ];
    }
}
