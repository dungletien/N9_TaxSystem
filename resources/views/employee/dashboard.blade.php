@extends('layouts.app')

@section('title', 'Nhân viên - Dashboard')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }

    .sidebar {
        background: linear-gradient(135deg, #004e92 0%, #000428 100%);
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

    .profile-pic {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        margin: 0 auto 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
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
    }

    .card-header {
        background: linear-gradient(135deg, #004e92 0%, #000428 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    .btn-primary {
        background: linear-gradient(135deg, #004e92 0%, #000428 100%);
        border: none;
        border-radius: 25px;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
    }

    .form-control.is-valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.04-.04L4.2 4.8 6.06 6.66a.5.5 0 0 0 .7-.7L4.9 4.1 6.76 2.24a.5.5 0 0 0-.7-.7L4.2 3.4 2.34 1.54a.5.5 0 0 0-.7.7L3.5 4.1 1.64 5.96a.5.5 0 1 0 .7.7Z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(.375em + .1875rem) center;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4M7.2 4.6l-1.4 1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(.375em + .1875rem) center;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem);
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #dc3545;
    }

    #password-form-section {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }

    #toggle-password-form {
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    #toggle-password-form:hover {
        background-color: #004e92;
        border-color: #004e92;
        color: white;
        transform: translateY(-1px);
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
                    <i class="fas fa-user"></i>
                </div>
                <h5>{{ isset($user) ? $user->full_name : 'Nhân viên' }}</h5>
                <p class="mb-0">Nhân viên</p>
            </div>

            <button class="nav-button active" onclick="switchTab('info-tab')">
                <i class="fas fa-user-edit"></i> Nhập thông tin
            </button>
            <button class="nav-button" onclick="switchTab('salary-tab')">
                <i class="fas fa-money-bill-wave"></i> Xem lương và thuế cá nhân
            </button>
            <button class="nav-button" onclick="switchTab('calculator-tab')">
                <i class="fas fa-calculator"></i> Tính thử thuế
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

            <!-- Tab Nhập thông tin -->
            <div id="info-tab" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-user-edit"></i> Thông tin cá nhân</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('employee.update.profile') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Họ và tên</label>
                                        <input type="text" class="form-control" name="full_name"
                                               value="{{ $user->full_name ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mã số nhân viên</label>
                                        <input type="text" class="form-control" value="{{ $user->id ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Địa chỉ hiện tại</label>
                                        <input type="text" class="form-control" name="address"
                                               value="{{ $user->address ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" name="phone"
                                               value="{{ $user->phone ?? '' }}" maxlength="10" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ngày tháng năm sinh</label>
                                        <input type="date" class="form-control" name="dob"
                                               value="{{ (isset($user) && is_object($user) && $user->dob) ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Số người phụ thuộc</label>
                                        <input type="number" class="form-control" name="dependent"
                                               value="{{ $user->dependent ?? 0 }}" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Giới tính</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" value="Nam"
                                               {{ (($user->gender ?? '') == 'Nam') ? 'checked' : '' }} required>
                                        <label class="form-check-label">Nam</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" value="Nữ"
                                               {{ (($user->gender ?? '') == 'Nữ') ? 'checked' : '' }} required>
                                        <label class="form-check-label">Nữ</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút thay đổi mật khẩu -->
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary" id="toggle-password-form">
                                    <i class="fas fa-key"></i> Thay đổi mật khẩu
                                </button>
                            </div>

                            <!-- Form mật khẩu (ẩn ban đầu) -->
                            <div id="password-form-section" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Mật khẩu mới</label>
                                            <input type="password" class="form-control" name="password"
                                                   placeholder="Nhập mật khẩu mới" minlength="6">
                                            <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Xác nhận mật khẩu</label>
                                            <input type="password" class="form-control" name="password_confirmation"
                                                   placeholder="Nhập lại mật khẩu mới" minlength="6">
                                            <div class="form-text">Nhập lại mật khẩu để xác nhận</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-secondary btn-sm" id="cancel-password-change">
                                        <i class="fas fa-times"></i> Hủy thay đổi mật khẩu
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Xem lương và thuế -->
            <div id="salary-tab" class="tab-content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> Lương và thuế cá nhân</h4>
                        <div>
                            <input type="number" id="search-year" class="form-control d-inline-block"
                                   style="width: 100px;" placeholder="Năm" value="{{ date('Y') }}">
                            <button class="btn btn-primary" onclick="searchSalary()">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="salary-table">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Lương</th>
                                        <th>Thuế cần nộp</th>
                                        <th>Lương thực nhận</th>
                                    </tr>
                                </thead>
                                <tbody id="salary-table-body">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Tính thử thuế -->
            <div id="calculator-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-calculator"></i> Tính thử thuế</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nhập số lương</label>
                                    <input type="number" class="form-control" id="salary-input"
                                           placeholder="Nhập số lương" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nhập số người phụ thuộc</label>
                                    <input type="number" class="form-control" id="dependent-input"
                                           value="0" min="0">
                                </div>
                                <button class="btn btn-primary" onclick="calculateTax()">
                                    <i class="fas fa-calculator"></i> Tính thuế
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div id="tax-result" class="mt-3">
                                    <h5>Kết quả tính thuế:</h5>
                                    <div class="alert alert-info">
                                        <p><strong>Số thuế phải nộp:</strong> <span id="tax-amount">0 VNĐ</span></p>
                                        <p><strong>Lương thực nhận:</strong> <span id="net-salary">0 VNĐ</span></p>
                                        <p><strong>Thu nhập chịu thuế:</strong> <span id="taxable-income">0 VNĐ</span></p>
                                    </div>
                                </div>
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

    function searchSalary() {
        const year = $('#search-year').val();
        if (!year) {
            alert('Vui lòng nhập năm!');
            return;
        }

        $.ajax({
            url: '{{ route("employee.salaries") }}',
            method: 'GET',
            data: { year: year },
            success: function(data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(function(item) {
                        html += `<tr>
                            <td>${item.month}</td>
                            <td>${new Intl.NumberFormat('vi-VN').format(item.salary)} VNĐ</td>
                            <td>${new Intl.NumberFormat('vi-VN').format(item.tax)} VNĐ</td>
                            <td>${new Intl.NumberFormat('vi-VN').format(item.net_salary)} VNĐ</td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center">Không có dữ liệu</td></tr>';
                }
                $('#salary-table-body').html(html);
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu!');
            }
        });
    }

    function calculateTax() {
        const salary = $('#salary-input').val();
        const dependent = $('#dependent-input').val();

        if (!salary || salary <= 0) {
            alert('Vui lòng nhập số lương hợp lệ!');
            return;
        }

        $.ajax({
            url: '{{ route("employee.calculate.tax") }}',
            method: 'POST',
            data: {
                salary: salary,
                dependent: dependent
            },
            success: function(data) {
                $('#tax-amount').text(new Intl.NumberFormat('vi-VN').format(data.tax) + ' VNĐ');
                $('#net-salary').text(new Intl.NumberFormat('vi-VN').format(data.netSalary) + ' VNĐ');
                $('#taxable-income').text(new Intl.NumberFormat('vi-VN').format(data.taxableIncome) + ' VNĐ');
            },
            error: function() {
                alert('Có lỗi xảy ra khi tính thuế!');
            }
        });
    }

    // Load salary data on page load
    $(document).ready(function() {
        searchSalary();

        // Toggle password form
        $('#toggle-password-form').on('click', function() {
            $('#password-form-section').slideDown();
            $(this).hide();
        });

        // Cancel password change
        $('#cancel-password-change').on('click', function() {
            $('#password-form-section').slideUp();
            $('#toggle-password-form').show();
            // Clear password fields
            $('input[name="password"]').val('').removeClass('is-valid is-invalid');
            $('input[name="password_confirmation"]').val('').removeClass('is-valid is-invalid');
            $('.invalid-feedback').remove();
        });

        // Password confirmation validation
        $('input[name="password_confirmation"]').on('keyup', function() {
            const password = $('input[name="password"]').val();
            const confirmPassword = $(this).val();
            
            if (password !== '' && confirmPassword !== '') {
                if (password !== confirmPassword) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Mật khẩu không khớp</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).next('.invalid-feedback').remove();
                }
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Password input validation
        $('input[name="password"]').on('keyup', function() {
            const password = $(this).val();
            const confirmPassword = $('input[name="password_confirmation"]').val();
            
            // Validate password length
            if (password !== '' && password.length < 6) {
                $(this).addClass('is-invalid').removeClass('is-valid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Mật khẩu phải có ít nhất 6 ký tự</div>');
                }
            } else if (password !== '') {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $(this).next('.invalid-feedback').remove();
            } else {
                $(this).removeClass('is-invalid is-valid');
                $(this).next('.invalid-feedback').remove();
            }
            
            // Re-validate confirmation if it has value
            if (confirmPassword !== '') {
                $('input[name="password_confirmation"]').trigger('keyup');
            }
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            const password = $('input[name="password"]').val();
            const confirmPassword = $('input[name="password_confirmation"]').val();
            
            if (password !== '' && password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu và xác nhận mật khẩu không khớp!');
                $('input[name="password_confirmation"]').focus();
                return false;
            }
            
            if (password !== '' && password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                $('input[name="password"]').focus();
                return false;
            }
        });
    });
</script>
@endpush
