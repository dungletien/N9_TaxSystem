@extends('layouts.app')

@section('title', 'Trưởng phòng - Dashboard')

@push('styles')
    <style>
        body {
            background-color: #f4f6f9;
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
            background: linear-gradient(135deg, #004e92 0%, #000428 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }

        .btn-warning {
            background: #004e92;
            border: none;
            border-radius: 25px;
            color: white;
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
            background-color: #004e92;
            border-color: #004e92;
            color: white;
            outline: none !important;
            box-shadow: none !important;
        }

        .input-group .btn-outline-secondary:active,
        .input-group .btn-outline-secondary:focus {
            background-color: #000428 !important;
            border-color: #000428 !important;
            color: white !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 78, 146, 0.1);
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .bg-stat-secondary {
            background: #00498b !important;
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
                    <h5>{{ isset($user) && is_object($user) ? $user->full_name : 'Trưởng phòng' }}</h5>
                    <p class="mb-0" id="department-name">Trưởng phòng</p>
                </div>

                <button class="nav-button active" onclick="switchTab('employee-management-tab')">
                    <i class="fas fa-users"></i> Quản lý nhân viên
                </button>
                <button class="nav-button" onclick="switchTab('monthly-tax-tab')">
                    <i class="fas fa-calendar-alt"></i> Xem thuế hàng tháng
                </button>
                <button class="nav-button" onclick="switchTab('annual-tax-tab')">
                    <i class="fas fa-chart-bar"></i> Quyết toán thuế hàng năm
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-employee"
                                            placeholder="Tìm kiếm theo tên, mã NV, CCCD..." onkeyup="searchEmployees()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"
                                            title="Xóa tìm kiếm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button class="btn btn-warning" onclick="loadEmployees()">
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
                                            <th>Địa chỉ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employees-table-body">
                                        <tr>
                                            <td colspan="6" class="text-center">Đang tải dữ liệu...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Xem thuế hàng tháng -->
                <div id="monthly-tax-tab" class="tab-content">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-calendar-alt"></i> Thuế hàng tháng phòng ban</h4>
                            <div>
                                <input type="number" id="monthly-search-month" class="form-control d-inline-block"
                                    style="width: 80px;" placeholder="Tháng" min="1" max="12" value="{{ date('n') }}">
                                <input type="number" id="monthly-search-year" class="form-control d-inline-block"
                                    style="width: 100px;" placeholder="Năm" value="{{ date('Y') }}">
                                <button class="btn btn-warning" onclick="searchMonthlyTax()">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card bg-stat-secondary text-white">
                                        <div class="card-body text-center">
                                            <h5>Tổng thuế phòng ban</h5>
                                            <h3 id="total-monthly-tax">0 VNĐ</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-stat-secondary text-white">
                                        <div class="card-body text-center">
                                            <h5>Số nhân viên</h5>
                                            <h3 id="total-employees-count">0</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="monthly-tax-table">
                                    <thead>
                                        <tr>
                                            <th>Mã NV</th>
                                            <th>Họ tên</th>
                                            <th>Lương</th>
                                            <th>Thuế TNCN</th>
                                            <th>Lương thực nhận</th>
                                        </tr>
                                    </thead>
                                    <tbody id="monthly-tax-table-body">
                                        <tr>
                                            <td colspan="5" class="text-center">Chọn tháng/năm để xem dữ liệu</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Quyết toán thuế hàng năm -->
                <div id="annual-tax-tab" class="tab-content">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Quyết toán thuế hàng năm</h4>
                            <div>
                                <input type="number" id="annual-search-year" class="form-control d-inline-block"
                                    style="width: 120px;" placeholder="Năm" value="{{ date('Y') }}">
                                <button class="btn btn-warning" onclick="searchAnnualTax()">
                                    <i class="fas fa-search"></i> Xem báo cáo
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Thống kê tổng quan -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-stat-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6>Tổng lương năm</h6>
                                            <h4 id="total-annual-salary">0 VNĐ</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-stat-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6>Tổng thuế năm</h6>
                                            <h4 id="total-annual-tax">0 VNĐ</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-stat-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6>Thực nhận năm</h6>
                                            <h4 id="total-annual-net">0 VNĐ</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-stat-secondary text-white">
                                        <div class="card-body text-center">
                                            <h6>Số nhân viên</h6>
                                            <h4 id="annual-employees-count">0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bảng chi tiết từng nhân viên -->
                            <div class="table-responsive">
                                <table class="table table-striped" id="annual-tax-table">
                                    <thead>
                                        <tr>
                                            <th>Mã NV</th>
                                            <th>Họ tên</th>
                                            <th>Tổng lương năm</th>
                                            <th>Tổng thuế năm</th>
                                            <th>Thực nhận năm</th>
                                        </tr>
                                    </thead>
                                    <tbody id="annual-tax-table-body">
                                        <tr>
                                            <td colspan="5" class="text-center">Chọn năm để xem báo cáo</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Chart cho quyết toán thuế -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-chart-line"></i> Biểu đồ thuế theo tháng</h5>
                                        </div>
                                        <div class="card-body">
                                            <div style="position: relative; height: 400px;">
                                                <canvas id="monthly-tax-chart"></canvas>
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
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            // Show loading state
            $('#employees-table-body').html('<tr><td colspan="6" class="text-center">Đang tải dữ liệu...</td></tr>');

            $.ajax({
                url: '{{ route("manager.employees") }}',
                method: 'GET',
                success: function (data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(function (employee) {
                            html += `<tr>
                                        <td>${employee.id}</td>
                                        <td>${employee.full_name}</td>
                                        <td>${employee.department}</td>
                                        <td>${employee.phone || 'Chưa cập nhật'}</td>
                                        <td>${employee.cccd}</td>
                                        <td>${employee.address || 'Chưa cập nhật'}</td>
                                    </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="6" class="text-center">Không có nhân viên nào trong phòng ban</td></tr>';
                    }
                    $('#employees-table-body').html(html);
                },
                error: function (xhr, status, error) {
                    console.error('Error loading employees:', error);
                    let errorMessage = 'Có lỗi xảy ra khi tải dữ liệu!';
                    if (xhr.status === 401) {
                        errorMessage = 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại!';
                        // Redirect to login after 2 seconds
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 2000);
                    }
                    $('#employees-table-body').html(`<tr><td colspan="6" class="text-center text-danger">${errorMessage}</td></tr>`);
                }
            });
        }

        // Biến để lưu timeout cho debounce
        let searchTimeout;
        let allEmployees = []; // Cache toàn bộ data nhân viên

        // Hàm tìm kiếm nhân viên
        function searchEmployees() {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(function () {
                const searchTerm = $('#search-employee').val().toLowerCase().trim();

                if (searchTerm === '') {
                    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả nhân viên
                    displayEmployees(allEmployees);
                    return;
                }

                // Lọc nhân viên theo từ khóa
                const filteredEmployees = allEmployees.filter(function (employee) {
                    return employee.id.toLowerCase().includes(searchTerm) ||
                        employee.full_name.toLowerCase().includes(searchTerm) ||
                        (employee.cccd && employee.cccd.toLowerCase().includes(searchTerm)) ||
                        (employee.phone && employee.phone.toLowerCase().includes(searchTerm)) ||
                        employee.department.toLowerCase().includes(searchTerm) ||
                        (employee.address && employee.address.toLowerCase().includes(searchTerm));
                });

                displayEmployees(filteredEmployees);
            }, 300);
        }

        // Hàm hiển thị danh sách nhân viên
        function displayEmployees(employees) {
            let html = '';
            if (employees.length > 0) {
                employees.forEach(function (employee) {
                    html += `<tr>
                                <td>${employee.id}</td>
                                <td>${employee.full_name}</td>
                                <td>${employee.department}</td>
                                <td>${employee.phone || 'Chưa cập nhật'}</td>
                                <td>${employee.cccd}</td>
                                <td>${employee.address || 'Chưa cập nhật'}</td>
                            </tr>`;
                });
            } else {
                const searchTerm = $('#search-employee').val().trim();
                const message = searchTerm ? 'Không tìm thấy nhân viên nào phù hợp' : 'Không có nhân viên nào trong phòng ban';
                html = `<tr><td colspan="6" class="text-center">${message}</td></tr>`;
            }
            $('#employees-table-body').html(html);
        }

        // Hàm xóa tìm kiếm
        function clearSearch() {
            $('#search-employee').val('');
            displayEmployees(allEmployees);
        }

        // Load employees và cache data
        function loadEmployeesWithCache() {
            $('#employees-table-body').html('<tr><td colspan="6" class="text-center">Đang tải dữ liệu...</td></tr>');

            $.ajax({
                url: '{{ route("manager.employees") }}',
                method: 'GET',
                success: function (data) {
                    allEmployees = data; // Cache data
                    displayEmployees(allEmployees);
                },
                error: function (xhr, status, error) {
                    console.error('Error loading employees:', error);
                    let errorMessage = 'Có lỗi xảy ra khi tải dữ liệu!';
                    if (xhr.status === 401) {
                        errorMessage = 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại!';
                        setTimeout(() => {
                            window.location.href = '{{ route("login") }}';
                        }, 2000);
                    }
                    $('#employees-table-body').html(`<tr><td colspan="6" class="text-center text-danger">${errorMessage}</td></tr>`);
                }
            });
        }

        function loadDepartment() {
            $.ajax({
                url: '{{ route("manager.department") }}',
                method: 'GET',
                success: function (data) {
                    if (data.department) {
                        $('#department-name').text('Phòng ' + data.department);
                    } else {
                        $('#department-name').text('Chưa xác định phòng ban');
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Không thể tải thông tin phòng ban:', error);
                    $('#department-name').text('Lỗi tải phòng ban');
                }
            });
        }

        function searchSalaries() {
            searchMonthlyTax(); // Redirect to new function
        }

        function searchMonthlyTax() {
            const month = $('#monthly-search-month').val();
            const year = $('#monthly-search-year').val();

            if (!month || !year) {
                alert('Vui lòng nhập đầy đủ tháng và năm!');
                return;
            }

            // Show loading state
            $('#monthly-tax-table-body').html('<tr><td colspan="5" class="text-center">Đang tải dữ liệu...</td></tr>');
            $('#total-monthly-tax').text('Đang tải...');
            $('#total-employees-count').text('Đang tải...');

            $.ajax({
                url: '{{ route("manager.monthly-tax") }}',
                method: 'GET',
                data: { month: month, year: year },
                success: function (data) {
                    if (data.success) {
                        // Update table
                        $('#monthly-tax-table-body').html(data.html);

                        // Update statistics
                        $('#total-monthly-tax').text(data.total_tax);
                        $('#total-employees-count').text(data.employee_count);
                    } else {
                        $('#monthly-tax-table-body').html(`<tr><td colspan="5" class="text-center text-danger">${data.message}</td></tr>`);
                        $('#total-monthly-tax').text('0 VNĐ');
                        $('#total-employees-count').text('0');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading monthly tax:', error);
                    let errorMessage = 'Có lỗi xảy ra khi tải dữ liệu thuế!';
                    if (xhr.status === 401) {
                        errorMessage = 'Phiên đăng nhập đã hết hạn!';
                    }
                    $('#monthly-tax-table-body').html(`<tr><td colspan="5" class="text-center text-danger">${errorMessage}</td></tr>`);
                    $('#total-monthly-tax').text('Lỗi');
                    $('#total-employees-count').text('Lỗi');
                }
            });
        }

        function searchAnnualTax() {
            const year = $('#annual-search-year').val();

            if (!year) {
                alert('Vui lòng nhập năm!');
                return;
            }

            console.log('Searching annual tax for year:', year);

            // Show loading state
            $('#annual-tax-table-body').html('<tr><td colspan="5" class="text-center">Đang tải dữ liệu...</td></tr>');
            $('#total-annual-salary').text('Đang tải...');
            $('#total-annual-tax').text('Đang tải...');
            $('#total-annual-net').text('Đang tải...');
            $('#annual-employees-count').text('Đang tải...');

            $.ajax({
                url: '{{ route("manager.annual-tax") }}',
                method: 'GET',
                data: { year: year },
                success: function (data) {
                    console.log('Annual tax response:', data);

                    if (data.success) {
                        // Update table
                        $('#annual-tax-table-body').html(data.html);

                        // Update statistics
                        $('#total-annual-salary').text(data.total_salary);
                        $('#total-annual-tax').text(data.total_tax);
                        $('#total-annual-net').text(data.total_net);
                        $('#annual-employees-count').text(data.employee_count);

                        // Update chart if data available and Chart.js is loaded
                        if (data.chart_data && typeof Chart !== 'undefined') {
                            console.log('Updating chart with data:', data.chart_data);
                            updateMonthlyTaxChart(data.chart_data);
                        } else if (typeof Chart === 'undefined') {
                            console.error('Chart.js is not loaded!');
                            $('#monthly-tax-chart').parent().html('<p class="text-center text-warning">Chart.js chưa được tải. Không thể hiển thị biểu đồ.</p>');
                        }
                    } else {
                        $('#annual-tax-table-body').html(`<tr><td colspan="5" class="text-center text-danger">${data.message}</td></tr>`);
                        $('#total-annual-salary').text('0 VNĐ');
                        $('#total-annual-tax').text('0 VNĐ');
                        $('#total-annual-net').text('0 VNĐ');
                        $('#annual-employees-count').text('0');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error loading annual tax:', error);
                    console.error('Response:', xhr.responseText);

                    let errorMessage = 'Có lỗi xảy ra khi tải báo cáo thuế hàng năm!';
                    if (xhr.status === 401) {
                        errorMessage = 'Phiên đăng nhập đã hết hạn!';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    $('#annual-tax-table-body').html(`<tr><td colspan="5" class="text-center text-danger">${errorMessage}</td></tr>`);
                    $('#total-annual-salary').text('Lỗi');
                    $('#total-annual-tax').text('Lỗi');
                    $('#total-annual-net').text('Lỗi');
                    $('#annual-employees-count').text('Lỗi');
                }
            });
        }    // Chart for monthly tax visualization
        let monthlyTaxChart;

        function updateMonthlyTaxChart(chartData) {
            const ctx = document.getElementById('monthly-tax-chart');

            if (!ctx) {
                console.error('Chart canvas not found');
                return;
            }

            if (monthlyTaxChart) {
                monthlyTaxChart.destroy();
            }

            monthlyTaxChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.months,
                    datasets: [{
                        label: 'Tổng thuế TNCN (VNĐ)',
                        data: chartData.taxes,
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253, 126, 20, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fd7e14',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Biểu đồ thuế TNCN theo tháng',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    tooltips: {
                        callbacks: {
                            label: function (context) {
                                return 'Thuế: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                            }
                        }
                    }
                }
            });
        }    // Cập nhật loadEmployees để sử dụng cache
        function loadEmployees() {
            loadEmployeesWithCache();
        }

        // Load data on page load
        $(document).ready(function () {
            loadEmployeesWithCache(); // Sử dụng function mới
            loadDepartment();
            searchMonthlyTax(); // Load monthly tax instead of salaries

            // Thêm Enter key support cho tìm kiếm
            $('#search-employee').on('keypress', function (e) {
                if (e.which === 13) { // Enter key
                    clearTimeout(searchTimeout);
                    searchEmployees();
                }
            });

            // Enter key support cho monthly tax search
            $('#monthly-search-month, #monthly-search-year').on('keypress', function (e) {
                if (e.which === 13) {
                    searchMonthlyTax();
                }
            });

            // Enter key support cho annual tax search
            $('#annual-search-year').on('keypress', function (e) {
                if (e.which === 13) {
                    searchAnnualTax();
                }
            });
        });
    </script>
@endpush
