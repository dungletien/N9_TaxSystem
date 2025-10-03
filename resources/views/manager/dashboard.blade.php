@extends('layouts.app')

@section('title', 'Trưởng phòng - Dashboard')

@push('styles')
<style>
    body {
        background-color: #f8f9fa;
    }

    .sidebar {
        background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
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
        background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    .btn-warning {
        background: linear-gradient(135deg, #fd7e14 0%, #e83e8c 100%);
        border: none;
        border-radius: 25px;
        color: white;
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
                <h5>{{ isset($user) ? $user->full_name : 'Trưởng phòng' }}</h5>
                <p class="mb-0" id="department-name">Trưởng phòng</p>
            </div>

            <button class="nav-button active" onclick="switchTab('employee-management-tab')">
                <i class="fas fa-users"></i> Quản lý nhân viên
            </button>
            <button class="nav-button" onclick="switchTab('salary-view-tab')">
                <i class="fas fa-money-bill-wave"></i> Xem lương và thuế
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
                        <h4 class="mb-0"><i class="fas fa-users"></i> Quản lý nhân viên phòng ban</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="employees-table">
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ tên</th>
                                        <th>Phòng ban</th>
                                        <th>Điện thoại</th>
                                        <th>CCCD</th>
                                        <th>Địa chỉ</th>
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

            <!-- Tab Xem lương và thuế -->
            <div id="salary-view-tab" class="tab-content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> Lương và thuế phòng ban</h4>
                        <div>
                            <input type="number" id="search-month" class="form-control d-inline-block"
                                   style="width: 80px;" placeholder="Tháng" min="1" max="12" value="{{ date('n') }}">
                            <input type="number" id="search-year" class="form-control d-inline-block"
                                   style="width: 100px;" placeholder="Năm" value="{{ date('Y') }}">
                            <button class="btn btn-warning" onclick="searchSalaries()">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="salary-table">
                                <thead>
                                    <tr>
                                        <th>Mã NV</th>
                                        <th>Họ tên</th>
                                        <th>Lương</th>
                                        <th>Thuế</th>
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
        $.ajax({
            url: '{{ route("manager.employees") }}',
            method: 'GET',
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
                            <td>${employee.address || ''}</td>
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

    function loadDepartment() {
        $.ajax({
            url: '{{ route("manager.department") }}',
            method: 'GET',
            success: function(data) {
                $('#department-name').text('Phòng ' + data.department);
            },
            error: function() {
                console.log('Không thể tải thông tin phòng ban');
            }
        });
    }

    function searchSalaries() {
        const month = $('#search-month').val();
        const year = $('#search-year').val();

        if (!month || !year) {
            alert('Vui lòng nhập đầy đủ tháng và năm!');
            return;
        }

        $.ajax({
            url: '{{ route("manager.salaries") }}',
            method: 'GET',
            data: { month: month, year: year },
            success: function(data) {
                $('#salary-table-body').html(data);
            },
            error: function() {
                alert('Có lỗi xảy ra khi tải dữ liệu lương!');
            }
        });
    }

    // Load data on page load
    $(document).ready(function() {
        loadEmployees();
        loadDepartment();
        searchSalaries();
    });
</script>
@endpush
