@extends('layouts.app')

@section('title', 'Kế toán - Dashboard')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }

    .sidebar {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    .profile-section {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }

    .nav-button {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 5px;
        width: 100%;
        text-align: left;
        transition: all 0.3s ease;
    }

    .nav-button:hover,
    .nav-button.active {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .content-area {
        padding: 2rem;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 25px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <div class="profile-section">
                <div class="profile-pic">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h5>Kế toán</h5>
                <p class="mb-0">Quản lý toàn bộ hệ thống</p>
            </div>

            <button class="nav-button active" onclick="switchTab('employee-management-tab')">
                <i class="fas fa-users"></i> Quản lý nhân viên
            </button>
            <button class="nav-button" onclick="switchTab('account-management-tab')">
                <i class="fas fa-user-plus"></i> Quản lý tài khoản
            </button>
            <button class="nav-button" onclick="switchTab('deduction-setup-tab')">
                <i class="fas fa-cogs"></i> Thiết lập giảm trừ
            </button>
            <button class="nav-button" onclick="switchTab('salary-tax-tab')">
                <i class="fas fa-money-bill-wave"></i> Lương và Thuế
            </button>
            <button class="nav-button" onclick="switchTab('annual-tax-tab')">
                <i class="fas fa-file-invoice"></i> Quyết toán thuế
            </button>

            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="nav-button">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 content-area">

            <!-- Tab Quản lý nhân viên -->
            <div id="employee-management-tab" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-users"></i> Quản lý nhân viên</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <select class="form-control" id="department-filter" onchange="loadEmployees()">
                                    <option value="all">Tất cả phòng ban</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="sales">Sales</option>
                                    <option value="nhân sự">Nhân sự</option>
                                    <option value="kinh doanh">Kinh doanh</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="employees-table">
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ tên</th>
                                        <th>Phòng ban</th>
                                        <th>Điện thoại</th>
                                        <th>CCCD</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="employees-table-body">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Quản lý tài khoản -->
            <div id="account-management-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-user-plus"></i> Tạo tài khoản mới</h4>
                    </div>
                    <div class="card-body">
                        <form id="create-account-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mã nhân viên</label>
                                        <input type="text" class="form-control" name="id" maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control" name="full_name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mật khẩu</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phòng ban</label>
                                        <select class="form-control" name="department" required>
                                            <option value="">Chọn phòng ban</option>
                                            <option value="marketing">Marketing</option>
                                            <option value="sales">Sales</option>
                                            <option value="nhân sự">Nhân sự</option>
                                            <option value="kinh doanh">Kinh doanh</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" name="phone" maxlength="10" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">CCCD</label>
                                        <input type="text" class="form-control" name="cccd" maxlength="12" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Chức vụ</label>
                                        <select class="form-control" name="position" required>
                                            <option value="nhân viên">Nhân viên</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Vai trò</label>
                                        <select class="form-control" name="role" required>
                                            <option value="">Chọn vai trò</option>
                                            <option value="nhan-vien">Nhân viên</option>
                                            <option value="truong-phong">Trưởng phòng</option>
                                            <option value="ke-toan">Kế toán</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Tạo tài khoản
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Thiết lập giảm trừ -->
            <div id="deduction-setup-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-cogs"></i> Thiết lập giảm trừ</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Năm</label>
                                <input type="number" class="form-control" id="deduction-year" value="{{ date('Y') }}">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success mt-4" onclick="loadDeductions()">
                                    <i class="fas fa-search"></i> Tải dữ liệu
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Giảm trừ bản thân</th>
                                        <th>Giảm trừ người phụ thuộc</th>
                                    </tr>
                                </thead>
                                <tbody id="deductions-table-body">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-success" onclick="saveDeductions()">
                            <i class="fas fa-save"></i> Lưu thiết lập
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tab Lương và Thuế -->
            <div id="salary-tax-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> Lương và Thuế</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Tháng</label>
                                <select class="form-control" id="salary-month">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>Tháng {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Năm</label>
                                <input type="number" class="form-control" id="salary-year" value="{{ date('Y') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Phòng ban</label>
                                <select class="form-control" id="salary-department">
                                    <option value="all">Tất cả phòng ban</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="sales">Sales</option>
                                    <option value="nhân sự">Nhân sự</option>
                                    <option value="kinh doanh">Kinh doanh</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success mt-4" onclick="loadSalaries()">
                                    <i class="fas fa-search"></i> Tải dữ liệu
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="salaries-table">
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ tên</th>
                                        <th>Phòng ban</th>
                                        <th>Lương (VNĐ)</th>
                                        <th>Số người phụ thuộc</th>
                                        <th>Thuế (VNĐ)</th>
                                        <th>Lương thực nhận (VNĐ)</th>
                                    </tr>
                                </thead>
                                <tbody id="salaries-table-body">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button class="btn btn-success" onclick="calculateAllTaxes()">
                                    <i class="fas fa-calculator"></i> Tính thuế tất cả
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-primary" onclick="saveSalaries()">
                                    <i class="fas fa-save"></i> Lưu dữ liệu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Quyết toán thuế -->
            <div id="annual-tax-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-file-invoice"></i> Quyết toán thuế hàng năm</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Năm</label>
                                <input type="number" class="form-control" id="annual-year" value="{{ date('Y') - 1 }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Phòng ban</label>
                                <select class="form-control" id="annual-department">
                                    <option value="all">Tất cả phòng ban</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="sales">Sales</option>
                                    <option value="nhân sự">Nhân sự</option>
                                    <option value="kinh doanh">Kinh doanh</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success mt-4" onclick="loadAnnualTax()">
                                    <i class="fas fa-search"></i> Tải dữ liệu
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-warning mt-4" onclick="exportAnnualTax()">
                                    <i class="fas fa-download"></i> Xuất báo cáo
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="annual-tax-table">
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ tên</th>
                                        <th>Phòng ban</th>
                                        <th>Tổng lương năm</th>
                                        <th>Tổng thuế năm</th>
                                        <th>Lương thực nhận</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="annual-tax-table-body">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <button class="btn btn-success" onclick="calculateAnnualTax()">
                                    <i class="fas fa-calculator"></i> Tính quyết toán
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-primary" onclick="saveAnnualTax()">
                                    <i class="fas fa-save"></i> Lưu quyết toán
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabId) {
        // Hide all tabs
        $('.tab-content').removeClass('active');
        $('.nav-button').removeClass('active');

        // Show selected tab
        $('#' + tabId).addClass('active');
        event.target.classList.add('active');
    }

    function loadEmployees() {
        const department = $('#department-filter').val();

        $.ajax({
            url: '{{ route("accountant.employees") }}',
            method: 'GET',
            data: { department: department },
            success: function(data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(function(employee) {
                        html += `<tr>
                            <td>${employee.id}</td>
                            <td>${employee.full_name}</td>
                            <td>${employee.department}</td>
                            <td>${employee.phone || ''}</td>
                            <td>${employee.cccd}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="deleteEmployee('${employee.id}')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="6" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#employees-table-body').html(html);
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu!');
            }
        });
    }

    function deleteEmployee(id) {
        if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
            $.ajax({
                url: `/accountant/employees/${id}`,
                method: 'DELETE',
                success: function(data) {
                    if (data.success) {
                        alert(data.message);
                        loadEmployees();
                    } else {
                        alert(data.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi xóa nhân viên!');
                }
            });
        }
    }

    $('#create-account-form').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("accountant.accounts.create") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                if (data.success) {
                    alert(data.message);
                    $('#create-account-form')[0].reset();
                } else {
                    alert(data.message);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = 'Có lỗi xảy ra:\n';
                for (let field in errors) {
                    errorMessage += errors[field][0] + '\n';
                }
                alert(errorMessage);
            }
        });
    });

    function loadDeductions() {
        const year = $('#deduction-year').val();

        $.ajax({
            url: '{{ route("accountant.deductions") }}',
            method: 'GET',
            data: { year: year },
            success: function(data) {
                let html = '';
                for (let month = 1; month <= 12; month++) {
                    let deduction = data.find(d => d.month == month) || {};
                    html += `<tr>
                        <td>${month}</td>
                        <td>
                            <input type="number" class="form-control"
                                   data-month="${month}" data-field="self_deduction"
                                   value="${deduction.self_deduction || 11000000}">
                        </td>
                        <td>
                            <input type="number" class="form-control"
                                   data-month="${month}" data-field="dependent_deduction"
                                   value="${deduction.dependent_deduction || 4400000}">
                        </td>
                    </tr>`;
                }
                $('#deductions-table-body').html(html);
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu giảm trừ!');
            }
        });
    }

    function saveDeductions() {
        const year = $('#deduction-year').val();
        let deductions = [];

        $('#deductions-table-body tr').each(function() {
            const month = $(this).find('[data-field="self_deduction"]').data('month');
            const selfDeduction = $(this).find('[data-field="self_deduction"]').val();
            const dependentDeduction = $(this).find('[data-field="dependent_deduction"]').val();

            deductions.push({
                month: month,
                year: year,
                self_deduction: selfDeduction,
                dependent_deduction: dependentDeduction
            });
        });

        $.ajax({
            url: '{{ route("accountant.deductions.setup") }}',
            method: 'POST',
            data: deductions,
            success: function(data) {
                alert(data.message);
            },
            error: function() {
                alert('Có lỗi xảy ra khi lưu thiết lập!');
            }
        });
    }

    // Salary and Tax functions
    function loadSalaries() {
        const month = $('#salary-month').val();
        const year = $('#salary-year').val();
        const department = $('#salary-department').val();

        $.ajax({
            url: '{{ route("accountant.salaries") }}',
            method: 'GET',
            data: { 
                month: month, 
                year: year, 
                department: department 
            },
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    return;
                }
                
                let html = '';
                if (response.employees && response.employees.length > 0) {
                    response.employees.forEach(function(employee) {
                        html += `<tr data-employee-id="${employee.id}">
                            <td>${employee.id}</td>
                            <td>${employee.full_name}</td>
                            <td>${employee.department}</td>
                            <td>
                                <input type="number" class="form-control salary-input" 
                                       value="${employee.salary || 0}" 
                                       data-employee-id="${employee.id}"
                                       placeholder="Nhập lương">
                            </td>
                            <td>
                                <input type="number" class="form-control dependent-input" 
                                       value="${employee.dependent || 0}" 
                                       data-employee-id="${employee.id}"
                                       min="0">
                            </td>
                            <td class="tax-amount">${formatCurrency(employee.tax || 0)}</td>
                            <td class="net-salary">${formatCurrency(employee.net_salary || 0)}</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center">Không có dữ liệu nhân viên</td></tr>';
                }
                $('#salaries-table-body').html(html);
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu lương!');
            }
        });
    }

    function calculateAllTaxes() {
        const month = $('#salary-month').val();
        const year = $('#salary-year').val();
        
        $('#salaries-table-body tr').each(function() {
            const row = $(this);
            const employeeId = row.find('.salary-input').data('employee-id');
            const salary = parseFloat(row.find('.salary-input').val()) || 0;
            const dependent = parseInt(row.find('.dependent-input').val()) || 0;
            
            if (salary > 0) {
                calculateTaxForEmployee(employeeId, salary, dependent, month, year, row);
            }
        });
    }

    function calculateTaxForEmployee(employeeId, salary, dependent, month, year, row) {
        $.ajax({
            url: '{{ route("employee.calculate.tax") }}',
            method: 'POST',
            data: {
                salary: salary,
                dependent: dependent
            },
            success: function(data) {
                row.find('.tax-amount').text(formatCurrency(data.tax));
                row.find('.net-salary').text(formatCurrency(data.netSalary));
            },
            error: function() {
                console.error('Lỗi tính thuế cho nhân viên ' + employeeId);
            }
        });
    }

    function saveSalaries() {
        const month = $('#salary-month').val();
        const year = $('#salary-year').val();
        let salaries = [];

        $('#salaries-table-body tr').each(function() {
            const row = $(this);
            const employeeId = row.data('employee-id');
            const salary = parseFloat(row.find('.salary-input').val()) || 0;
            
            if (salary > 0) {
                const taxText = row.find('.tax-amount').text().replace(/[^\d]/g, '');
                const netSalaryText = row.find('.net-salary').text().replace(/[^\d]/g, '');
                
                salaries.push({
                    id: employeeId,
                    month: month,
                    year: year,
                    salary: salary,
                    tax: parseFloat(taxText) || 0,
                    net_salary: parseFloat(netSalaryText) || 0
                });
            }
        });

        if (salaries.length === 0) {
            alert('Không có dữ liệu để lưu!');
            return;
        }

        $.ajax({
            url: '{{ route("accountant.salaries.save") }}',
            method: 'POST',
            data: { salaries: salaries },
            success: function(data) {
                alert(data.message);
            },
            error: function() {
                alert('Có lỗi xảy ra khi lưu dữ liệu!');
            }
        });
    }

    // Annual Tax functions
    function loadAnnualTax() {
        const year = $('#annual-year').val();
        const department = $('#annual-department').val();

        $.ajax({
            url: '{{ route("accountant.annual.tax") }}',
            method: 'GET',
            data: { 
                year: year, 
                department: department 
            },
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    return;
                }
                
                let html = '';
                if (response.employees && response.employees.length > 0) {
                    response.employees.forEach(function(employee) {
                        const hasData = employee.total_salary && employee.total_salary > 0;
                        html += `<tr data-employee-id="${employee.id}">
                            <td>${employee.id}</td>
                            <td>${employee.full_name}</td>
                            <td>${employee.department}</td>
                            <td class="total-salary">${formatCurrency(employee.total_salary || 0)}</td>
                            <td class="total-tax">${formatCurrency(employee.total_tax || 0)}</td>
                            <td class="annual-net-salary">${formatCurrency(employee.net_salary || 0)}</td>
                            <td>
                                <span class="badge ${hasData ? 'bg-success' : 'bg-warning'}">
                                    ${hasData ? 'Đã có dữ liệu' : 'Chưa có dữ liệu'}
                                </span>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="7" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#annual-tax-table-body').html(html);
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu quyết toán!');
            }
        });
    }

    function calculateAnnualTax() {
        const year = $('#annual-year').val();
        
        // Calculate annual tax from monthly data
        $('#annual-tax-table-body tr').each(function() {
            const row = $(this);
            const employeeId = row.data('employee-id');
            
            if (employeeId) {
                calculateAnnualTaxForEmployee(employeeId, year, row);
            }
        });
    }

    function calculateAnnualTaxForEmployee(employeeId, year, row) {
        // This would typically aggregate monthly data
        // For now, we'll use a simplified calculation
        $.ajax({
            url: '/accountant/calculate-annual-tax',
            method: 'POST',
            data: {
                employee_id: employeeId,
                year: year
            },
            success: function(data) {
                row.find('.total-salary').text(formatCurrency(data.total_salary));
                row.find('.total-tax').text(formatCurrency(data.total_tax));
                row.find('.annual-net-salary').text(formatCurrency(data.net_salary));
                row.find('.badge').removeClass('bg-warning').addClass('bg-success').text('Đã tính toán');
            },
            error: function() {
                console.error('Lỗi tính quyết toán cho nhân viên ' + employeeId);
            }
        });
    }

    function saveAnnualTax() {
        const year = $('#annual-year').val();
        let taxes = [];

        $('#annual-tax-table-body tr').each(function() {
            const row = $(this);
            const employeeId = row.data('employee-id');
            
            if (employeeId) {
                const totalSalaryText = row.find('.total-salary').text().replace(/[^\d]/g, '');
                const totalTaxText = row.find('.total-tax').text().replace(/[^\d]/g, '');
                const netSalaryText = row.find('.annual-net-salary').text().replace(/[^\d]/g, '');
                
                if (totalSalaryText && parseFloat(totalSalaryText) > 0) {
                    taxes.push({
                        id: employeeId,
                        year: year,
                        total_salary: parseFloat(totalSalaryText) || 0,
                        total_tax: parseFloat(totalTaxText) || 0,
                        net_salary: parseFloat(netSalaryText) || 0
                    });
                }
            }
        });

        if (taxes.length === 0) {
            alert('Không có dữ liệu để lưu!');
            return;
        }

        $.ajax({
            url: '{{ route("accountant.annual.tax.save") }}',
            method: 'POST',
            data: { taxes: taxes },
            success: function(data) {
                alert(data.message);
            },
            error: function() {
                alert('Có lỗi xảy ra khi lưu quyết toán!');
            }
        });
    }

    function exportAnnualTax() {
        const year = $('#annual-year').val();
        const department = $('#annual-department').val();
        
        // This would typically generate and download a PDF/Excel file
        alert('Chức năng xuất báo cáo đang được phát triển!');
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    // Load data on page load
    $(document).ready(function() {
        loadEmployees();
        loadDeductions();
    });
</script>
@endpush
