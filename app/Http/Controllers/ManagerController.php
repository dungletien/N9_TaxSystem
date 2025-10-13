<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MonthTax;
use App\Models\UserRole;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        try {
            if (!Session::has('user')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userDepartment = Session::get('user.department');

            if (!$userDepartment) {
                return response()->json(['error' => 'Department not found'], 400);
            }

            // Lấy tất cả nhân viên trong cùng phòng ban (trừ trưởng phòng)
            $employees = User::where('department', $userDepartment)
                ->where('id', '!=', Session::get('user.id')) // Loại trừ chính trưởng phòng
                ->orderBy('full_name', 'asc')
                ->get();

            return response()->json($employees);
        } catch (\Exception $e) {
            Log::error('Error getting employees for manager: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function deleteEmployee(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user.user_type') !== 'truong-phong') {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa nhân viên!'], 403);
            }

            $employeeId = $request->input('employee_id');
            $managerDepartment = Session::get('user.department');

            if (!$employeeId) {
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin nhân viên!'], 400);
            }

            // Tìm nhân viên cần xóa
            $employee = User::find($employeeId);

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy nhân viên!'], 404);
            }

            // Kiểm tra nhân viên có thuộc phòng ban của trưởng phòng không
            if ($employee->department !== $managerDepartment) {
                return response()->json(['success' => false, 'message' => 'Bạn chỉ có thể xóa nhân viên trong phòng ban của mình!'], 403);
            }

            // Kiểm tra không được xóa chính mình
            if ($employee->id == Session::get('user.id')) {
                return response()->json(['success' => false, 'message' => 'Bạn không thể xóa chính mình!'], 403);
            }

            // Kiểm tra nhân viên có phải là trưởng phòng không
            $userRole = UserRole::where('user_id', $employee->id)->first();
            if ($userRole && $userRole->role === 'truong-phong') {
                return response()->json(['success' => false, 'message' => 'Không thể xóa trưởng phòng!'], 403);
            }

            // Bắt đầu transaction để đảm bảo tính nhất quán dữ liệu
            DB::beginTransaction();

            try {
                // Xóa dữ liệu liên quan
                // 1. Xóa dữ liệu thuế hàng tháng
                MonthTax::where('user_id', $employee->id)->delete();

                // 2. Xóa user role
                UserRole::where('user_id', $employee->id)->delete();

                // 3. Xóa user
                $employee->delete();

                DB::commit();

                Log::info("Manager {" . Session::get('user.id') . "} deleted employee {$employee->id} - {$employee->full_name}");

                return response()->json([
                    'success' => true, 
                    'message' => "Đã xóa thành công nhân viên {$employee->full_name}"
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error in delete transaction: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa dữ liệu!'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error deleting employee by manager: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    public function getDepartment()
    {
        try {
            if (!Session::has('user')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $department = Session::get('user.department');
            return response()->json(['department' => $department]);
        } catch (\Exception $e) {
            Log::error('Error getting department: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getSalaries(Request $request)
    {
        try {
            if (!Session::has('user')) {
                return response('<tr><td colspan="5">Unauthorized</td></tr>');
            }

            $month = $request->get('month');
            $year = $request->get('year');
            $department = Session::get('user.department');

            if (!$month || !$year) {
                return response('<tr><td colspan="5" class="text-center">Thiếu thông tin tháng hoặc năm!</td></tr>');
            }

            if (!$department) {
                return response('<tr><td colspan="5" class="text-center">Không tìm thấy thông tin phòng ban!</td></tr>');
            }

            $employees = User::leftJoin('month_taxes', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'month_taxes.user_id')
                    ->where('month_taxes.month', $month)
                    ->where('month_taxes.year', $year);
            })->where('users.department', $department)
                ->where('users.id', '!=', Session::get('user.id')) // Loại trừ trưởng phòng
                ->select('users.id', 'users.full_name', 'month_taxes.salary', 'month_taxes.tax', 'month_taxes.net_salary')
                ->orderBy('users.full_name', 'asc')
                ->get();

            $html = '';

            if ($employees->count() > 0) {
                foreach ($employees as $employee) {
                    $salary = $employee->salary ?? 0;
                    $tax = $employee->tax ?? 0;
                    $netSalary = $employee->net_salary ?? 0;

                    $html .= '<tr>
                                <td>' . htmlspecialchars($employee->id) . '</td>
                                <td>' . htmlspecialchars($employee->full_name) . '</td>
                                <td>' . number_format($salary, 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($tax, 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($netSalary, 0, ',', '.') . ' VNĐ</td>
                              </tr>';
                }
            } else {
                $html .= '<tr><td colspan="5" class="text-center">Không có dữ liệu lương cho tháng ' . $month . '/' . $year . '</td></tr>';
            }

            return response($html);
        } catch (\Exception $e) {
            Log::error('Error getting salaries for manager: ' . $e->getMessage());
            return response('<tr><td colspan="5" class="text-center">Có lỗi xảy ra khi tải dữ liệu!</td></tr>');
        }
    }

    public function getMonthlyTax(Request $request)
    {
        try {
            if (!Session::has('user')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }

            $month = $request->get('month');
            $year = $request->get('year');
            $department = Session::get('user.department');

            if (!$month || !$year) {
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin tháng hoặc năm!']);
            }

            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin phòng ban!']);
            }

            $employees = User::leftJoin('month_taxes', function ($join) use ($month, $year) {
                $join->on('users.id', '=', 'month_taxes.user_id')
                    ->where('month_taxes.month', $month)
                    ->where('month_taxes.year', $year);
            })->where('users.department', $department)
                ->where('users.id', '!=', Session::get('user.id'))
                ->select('users.id', 'users.full_name', 'month_taxes.salary', 'month_taxes.tax', 'month_taxes.net_salary')
                ->orderBy('users.full_name', 'asc')
                ->get();

            $html = '';
            $totalTax = 0;
            $employeeCount = 0;

            if ($employees->count() > 0) {
                foreach ($employees as $employee) {
                    $salary = $employee->salary ?? 0;
                    $tax = $employee->tax ?? 0;
                    $netSalary = $employee->net_salary ?? 0;

                    if ($salary > 0) {
                        $totalTax += $tax;
                        $employeeCount++;
                    }

                    $html .= '<tr>
                                <td>' . htmlspecialchars($employee->id) . '</td>
                                <td>' . htmlspecialchars($employee->full_name) . '</td>
                                <td>' . number_format($salary, 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($tax, 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($netSalary, 0, ',', '.') . ' VNĐ</td>
                              </tr>';
                }
            } else {
                $html = '<tr><td colspan="5" class="text-center">Không có dữ liệu thuế cho tháng ' . $month . '/' . $year . '</td></tr>';
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'total_tax' => number_format($totalTax, 0, ',', '.') . ' VNĐ',
                'employee_count' => $employeeCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting monthly tax for manager: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi tải dữ liệu!']);
        }
    }

    public function getAnnualTax(Request $request)
    {
        try {
            if (!Session::has('user')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }

            $year = $request->get('year');
            $department = Session::get('user.department');

            if (!$year) {
                return response()->json(['success' => false, 'message' => 'Thiếu thông tin năm!']);
            }

            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin phòng ban!']);
            }

            // Get all employees in department first
            $departmentEmployees = User::where('department', $department)
                ->where('id', '!=', Session::get('user.id'))
                ->orderBy('full_name', 'asc')
                ->get();

            $html = '';
            $totalSalary = 0;
            $totalTax = 0;
            $totalNet = 0;
            $employeeCount = 0;

            if ($departmentEmployees->count() > 0) {
                foreach ($departmentEmployees as $employee) {
                    // Get tax data for this employee for the year
                    $taxData = MonthTax::where('user_id', $employee->id)
                        ->where('year', $year)
                        ->selectRaw('
                            COALESCE(SUM(salary), 0) as total_salary,
                            COALESCE(SUM(tax), 0) as total_tax,
                            COALESCE(SUM(net_salary), 0) as total_net,
                            COUNT(*) as months_worked
                        ')
                        ->first();

                    $salary = $taxData ? $taxData->total_salary : 0;
                    $tax = $taxData ? $taxData->total_tax : 0;
                    $net = $taxData ? $taxData->total_net : 0;

                    // Only count employees who have salary data for statistics
                    if ($salary > 0) {
                        $totalSalary += $salary;
                        $totalTax += $tax;
                        $totalNet += $net;
                        $employeeCount++;
                    }

                    // Always show employee in table, even if no tax data
                    $html .= '<tr>
                                <td>' . htmlspecialchars($employee->id) . '</td>
                                <td>' . htmlspecialchars($employee->full_name) . '</td>
                                <td>' . number_format($salary, 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($tax, 0, ',', '.') . ' VNĐ</td>
                                <td>' . number_format($net, 0, ',', '.') . ' VNĐ</td>
                              </tr>';
                }
            } else {
                $html = '<tr><td colspan="5" class="text-center">Không có nhân viên nào trong phòng ban</td></tr>';
            }

            // Get monthly chart data for department
            $monthlyData = collect();
            for ($month = 1; $month <= 12; $month++) {
                $monthTax = MonthTax::join('users', 'month_taxes.user_id', '=', 'users.id')
                    ->where('users.department', $department)
                    ->where('users.id', '!=', Session::get('user.id'))
                    ->where('month_taxes.year', $year)
                    ->where('month_taxes.month', $month)
                    ->sum('month_taxes.tax');

                $monthlyData->push([
                    'month' => $month,
                    'tax' => $monthTax ?? 0
                ]);
            }

            $chartData = [
                'months' => [],
                'taxes' => []
            ];

            foreach ($monthlyData as $data) {
                $chartData['months'][] = 'Tháng ' . $data['month'];
                $chartData['taxes'][] = floatval($data['tax']);
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'total_salary' => number_format($totalSalary, 0, ',', '.') . ' VNĐ',
                'total_tax' => number_format($totalTax, 0, ',', '.') . ' VNĐ',
                'total_net' => number_format($totalNet, 0, ',', '.') . ' VNĐ',
                'employee_count' => $employeeCount,
                'chart_data' => $chartData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting annual tax for manager: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi tải báo cáo thuế hàng năm: ' . $e->getMessage()]);
        }
    }
}
