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
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-button {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
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
            background: rgba(255, 255, 255, 0.2);
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

        /* Search functionality styles */
        #search-employee {
            border-radius: 25px 0 0 25px;
            border-right: none;
            outline: none !important;
            box-shadow: none !important;
        }

        #search-employee:focus {
            border-color: #ced4da;
            box-shadow: none !important;
            outline: none !important;
        }

        .input-group .btn-outline-secondary {
            border-radius: 0 25px 25px 0;
            border-left: none;
            outline: none !important;
            box-shadow: none !important;
        }

        .input-group .btn-outline-secondary:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            outline: none !important;
            box-shadow: none !important;
        }

        .input-group .btn-outline-secondary:active,
        .input-group .btn-outline-secondary:focus {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
            color: white !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .table tbody tr:hover {
            background-color: #f1f3f4;
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
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-employee"
                                            placeholder="Tìm kiếm theo tên, mã NV, CCCD..." onkeyup="searchEmployees()"
                                            onkeypress="handleEnterSearch(event)">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"
                                            title="Xóa tìm kiếm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success" onclick="loadEmployees()">
                                        <i class="fas fa-sync-alt"></i> Làm mới
                                    </button>
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
                                            <input type="text" class="form-control" name="id" required>
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
                                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>Tháng {{ $i }}
                                            </option>
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

    <!-- Modal chỉnh sửa nhân viên -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">
                        <i class="fas fa-edit"></i> Chỉnh sửa thông tin nhân viên
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-employee-form">
                        <input type="hidden" id="edit-employee-id" name="id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Mã nhân viên</label>
                                    <input type="text" class="form-control" id="edit-id" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="edit-full-name" name="full_name" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phòng ban</label>
                                    <select class="form-control" id="edit-department" name="department" required>
                                        <option value="">Chọn phòng ban</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="sales">Sales</option>
                                        <option value="nhân sự">Nhân sự</option>
                                        <option value="kinh doanh">Kinh doanh</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Chức vụ</label>
                                    <select class="form-control" id="edit-position" name="position" required>
                                        <option value="nhân viên">Nhân viên</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="edit-phone" name="phone" maxlength="10"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">CCCD</label>
                                    <input type="text" class="form-control" id="edit-cccd" name="cccd" maxlength="12"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select class="form-control" id="edit-gender" name="gender">
                                        <option value="Nam">Nam</option>
                                        <option value="Nữ">Nữ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số người phụ thuộc</label>
                                    <input type="number" class="form-control" id="edit-dependent" name="dependent" min="0"
                                        value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Vai trò</label>
                                    <select class="form-control" id="edit-role" name="role" required>
                                        <option value="">Chọn vai trò</option>
                                        <option value="nhan-vien">Nhân viên</option>
                                        <option value="truong-phong">Trưởng phòng</option>
                                        <option value="ke-toan">Kế toán</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Địa chỉ</label>
                                    <input type="text" class="form-control" id="edit-address" name="address">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="button" class="btn btn-success" onclick="updateEmployee()">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
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
            const search = $('#search-employee').val();

            $.ajax({
                url: '{{ route("accountant.employees") }}',
                method: 'GET',
                data: {
                    department: department,
                    search: search
                },
                success: function (data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(function (employee) {
                            html += `<tr>
                                    <td>${employee.id}</td>
                                    <td>${employee.full_name}</td>
                                    <td>${employee.department}</td>
                                    <td>${employee.phone || ''}</td>
                                    <td>${employee.cccd}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-1" onclick="editEmployee('${employee.id}')">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
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
                error: function () {
                    alert('Có lỗi xảy ra khi tải dữ liệu!');
                }
            });
        }

        function deleteEmployee(id) {
            if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
                $.ajax({
                    url: `/accountant/employees/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        if (data.success) {
                            alert(data.message);
                            loadEmployees();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Có lỗi xảy ra khi xóa nhân viên!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                    }
                });
            }
        }

        // Biến để lưu timeout cho debounce
        let searchTimeout;

        // Hàm tìm kiếm nhân viên với debounce
        function searchEmployees() {
            // Clear timeout trước đó
            clearTimeout(searchTimeout);

            // Set timeout mới
            searchTimeout = setTimeout(function () {
                const searchTerm = $('#search-employee').val().toLowerCase();
                const department = $('#department-filter').val();

                $.ajax({
                    url: '{{ route("accountant.employees") }}',
                    method: 'GET',
                    data: {
                        department: department,
                        search: searchTerm
                    },
                    success: function (data) {
                        let html = '';

                        if (data.length > 0) {
                            data.forEach(function (employee) {
                                html += `<tr>
                                        <td>${employee.id}</td>
                                        <td>${employee.full_name}</td>
                                        <td>${employee.department}</td>
                                        <td>${employee.phone || ''}</td>
                                        <td>${employee.cccd}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary me-1" onclick="editEmployee('${employee.id}')">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteEmployee('${employee.id}')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>`;
                            });
                        } else {
                            const message = searchTerm ? 'Không tìm thấy nhân viên nào phù hợp' : 'Không có dữ liệu';
                            html = `<tr><td colspan="6" class="text-center">${message}</td></tr>`;
                        }
                        $('#employees-table-body').html(html);
                    },
                    error: function () {
                        alert('Có lỗi xảy ra khi tìm kiếm!');
                    }
                });
            }, 500); // Debounce 500ms
        }

        // Hàm xóa tìm kiếm
        function clearSearch() {
            $('#search-employee').val('');
            loadEmployees();
        }

        // Hàm xử lý phím Enter trong ô tìm kiếm
        function handleEnterSearch(event) {
            if (event.key === 'Enter') {
                // Clear timeout để thực hiện tìm kiếm ngay lập tức
                clearTimeout(searchTimeout);
                searchEmployees();
            }
        }

        function editEmployee(id) {
            console.log('Edit employee called with ID:', id);

            // Lấy thông tin nhân viên để hiển thị trong modal
            $.ajax({
                url: `/accountant/employees/${id}`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    console.log('Sending request to get employee data...');
                },
                success: function (response) {
                    console.log('Response received:', response);

                    if (response.success) {
                        const employee = response.employee;
                        console.log('Employee data:', employee);

                        // Điền thông tin vào form
                        $('#edit-employee-id').val(employee.id);
                        $('#edit-id').val(employee.id);
                        $('#edit-full-name').val(employee.full_name);
                        $('#edit-department').val(employee.department);
                        $('#edit-position').val(employee.position);
                        $('#edit-phone').val(employee.phone);
                        $('#edit-cccd').val(employee.cccd);
                        $('#edit-gender').val(employee.gender || 'Nam');
                        $('#edit-dependent').val(employee.dependent || 0);
                        $('#edit-role').val(employee.role);
                        $('#edit-address').val(employee.address || '');

                        console.log('Form fields filled, showing modal...');

                        // Hiển thị modal
                        const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                        modal.show();
                    } else {
                        console.error('Error from server:', response.message);
                        alert(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    alert('Có lỗi xảy ra khi tải thông tin nhân viên! Kiểm tra console để xem chi tiết.');
                }
            });
        }

        function updateEmployee() {
            const id = $('#edit-employee-id').val();
            const formData = $('#edit-employee-form').serialize();

            // Show loading
            const updateBtn = $('button:contains("Cập nhật")');
            const originalText = updateBtn.html();
            updateBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang cập nhật...');

            $.ajax({
                url: `/accountant/employees/${id}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData + '&_token={{ csrf_token() }}',
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        $('#editEmployeeModal').modal('hide');
                        loadEmployees(); // Reload danh sách nhân viên
                    } else {
                        alert(response.message);
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Có lỗi xảy ra khi cập nhật:\n';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        for (let field in xhr.responseJSON.errors) {
                            errorMessage += xhr.responseJSON.errors[field][0] + '\n';
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    alert(errorMessage);
                },
                complete: function () {
                    // Restore button
                    updateBtn.prop('disabled', false).html(originalText);
                }
            });
        }

        $('#create-account-form').submit(function (e) {
            e.preventDefault();

            // Show loading
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang tạo...');

            $.ajax({
                url: '{{ route("accountant.accounts.create") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(this).serialize() + '&_token={{ csrf_token() }}',
                success: function (data) {
                    if (data.success) {
                        alert(data.message);
                        $('#create-account-form')[0].reset();
                    } else {
                        alert(data.message);
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Có lỗi xảy ra:\n';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        for (let field in xhr.responseJSON.errors) {
                            errorMessage += xhr.responseJSON.errors[field][0] + '\n';
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    alert(errorMessage);
                },
                complete: function () {
                    // Restore button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        function loadDeductions() {
            const year = $('#deduction-year').val();

            $.ajax({
                url: '{{ route("accountant.deductions") }}',
                method: 'GET',
                data: { year: year },
                success: function (data) {
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
                error: function () {
                    alert('Có lỗi xảy ra khi tải dữ liệu giảm trừ!');
                }
            });
        }

        function saveDeductions() {
            const year = $('#deduction-year').val();
            let deductions = [];

            $('#deductions-table-body tr').each(function () {
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

            // Show loading
            const saveBtn = $('button:contains("Lưu thiết lập")');
            const originalText = saveBtn.html();
            saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

            $.ajax({
                url: '{{ route("accountant.deductions.setup") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    deductions: deductions,
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    alert(data.message);
                },
                error: function (xhr) {
                    let errorMessage = 'Có lỗi xảy ra khi lưu thiết lập!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                },
                complete: function () {
                    // Restore button
                    saveBtn.prop('disabled', false).html(originalText);
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
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                        return;
                    }

                    let html = '';
                    if (response.employees && response.employees.length > 0) {
                        response.employees.forEach(function (employee) {
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
                error: function () {
                    alert('Có lỗi xảy ra khi tải dữ liệu lương!');
                }
            });
        }

        function calculateAllTaxes() {
            const month = $('#salary-month').val();
            const year = $('#salary-year').val();

            $('#salaries-table-body tr').each(function () {
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
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    salary: salary,
                    dependent: dependent,
                    _token: '{{ csrf_token() }}'
                },
                success: function (data) {
                    row.find('.tax-amount').text(formatCurrency(data.tax));
                    row.find('.net-salary').text(formatCurrency(data.netSalary));
                },
                error: function () {
                    console.error('Lỗi tính thuế cho nhân viên ' + employeeId);
                }
            });
        }

        function saveSalaries() {
            const month = $('#salary-month').val();
            const year = $('#salary-year').val();
            let salaries = [];

            $('#salaries-table-body tr').each(function () {
                const row = $(this);
                const employeeId = row.data('employee-id');
                const salary = parseFloat(row.find('.salary-input').val()) || 0;

                if (salary > 0 && employeeId) {
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

            // Show loading
            const saveBtn = $('button:contains("Lưu dữ liệu")');
            const originalText = saveBtn.html();
            saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

            $.ajax({
                url: '{{ route("accountant.salaries.save") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    salaries: salaries,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        // Optionally reload the table to show saved data
                        loadSalaries();
                    } else {
                        alert(response.message || 'Có lỗi xảy ra khi lưu dữ liệu!');
                    }
                },
                error: function (xhr) {
                    console.error('Error saving salaries:', xhr);
                    let errorMessage = 'Có lỗi xảy ra khi lưu dữ liệu!';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        // Validation errors
                        errorMessage = 'Dữ liệu không hợp lệ:\n';
                        for (let field in xhr.responseJSON.errors) {
                            errorMessage += xhr.responseJSON.errors[field][0] + '\n';
                        }
                    }

                    alert(errorMessage);
                },
                complete: function () {
                    // Restore button
                    saveBtn.prop('disabled', false).html(originalText);
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
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                        return;
                    }

                    let html = '';
                    if (response.employees && response.employees.length > 0) {
                        response.employees.forEach(function (employee) {
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
                error: function () {
                    alert('Có lỗi xảy ra khi tải dữ liệu quyết toán!');
                }
            });
        }

        function calculateAnnualTax() {
            const year = $('#annual-year').val();

            // Calculate annual tax from monthly data
            $('#annual-tax-table-body tr').each(function () {
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
                success: function (data) {
                    row.find('.total-salary').text(formatCurrency(data.total_salary));
                    row.find('.total-tax').text(formatCurrency(data.total_tax));
                    row.find('.annual-net-salary').text(formatCurrency(data.net_salary));
                    row.find('.badge').removeClass('bg-warning').addClass('bg-success').text('Đã tính toán');
                },
                error: function () {
                    console.error('Lỗi tính quyết toán cho nhân viên ' + employeeId);
                }
            });
        }

        function saveAnnualTax() {
            const year = $('#annual-year').val();
            let taxes = [];

            $('#annual-tax-table-body tr').each(function () {
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
                success: function (data) {
                    alert(data.message);
                },
                error: function () {
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
        $(document).ready(function () {
            loadEmployees();
            loadDeductions();
        });
    </script>
@endpush
