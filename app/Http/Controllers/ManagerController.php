<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MonthTax;
use Illuminate\Support\Facades\Session;

class ManagerController extends Controller
{
    public function dashboard()
    {
        if (!Session::has('user') || Session::get('user.user_type') !== 'truong-phong') {
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập!');
        }

        $user = User::find(Session::get('user.id'));
        return view('manager.dashboard', compact('user'));
    }

    public function getEmployees()
    {
        $department = Session::get('user.department');
        $employees = User::where('department', $department)->get();
        return response()->json($employees);
    }

    public function getDepartment()
    {
        $department = Session::get('user.department');
        return response()->json(['department' => $department]);
    }

    public function getSalaries(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');
        $department = Session::get('user.department');

        if (!$month || !$year) {
            return response('<tr><td colspan="5">Thiếu thông tin tháng hoặc năm!</td></tr>');
        }

        $employees = User::leftJoin('month_taxes', function($join) use ($month, $year) {
            $join->on('users.id', '=', 'month_taxes.user_id')
                 ->where('month_taxes.month', $month)
                 ->where('month_taxes.year', $year);
        })->where('users.department', $department)
          ->select('users.id', 'users.full_name', 'month_taxes.salary', 'month_taxes.tax', 'month_taxes.net_salary')
          ->get();

        $html = '<tbody id="salary-tax-table-body">';

        if ($employees->count() > 0) {
            foreach ($employees as $employee) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($employee->id) . '</td>
                            <td>' . htmlspecialchars($employee->full_name) . '</td>
                            <td>' . number_format($employee->salary ?? 0, 2) . '</td>
                            <td>' . number_format($employee->tax ?? 0, 2) . '</td>
                            <td>' . number_format($employee->net_salary ?? 0, 2) . '</td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="5">Không có dữ liệu nhân viên!</td></tr>';
        }

        $html .= '</tbody>';

        return response($html);
    }
}
