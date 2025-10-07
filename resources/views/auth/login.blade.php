@extends('layouts.app')

@section('title', 'Đăng Nhập - Phần Mềm Tính Thuế')

@push('styles')
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            display: flex;
        }

        .login-left {
            flex: 1;
            background: url('https://cdn.pixabay.com/photo/2023/10/06/16/28/silhouette-8298662_1280.png') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 4rem 3rem;
            position: relative;
            overflow: hidden;
        }

        .login-right {
            flex: 1;
            padding: 4rem 5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h3 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #7f8c8d;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e8ecf0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #004e92;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(0, 78, 146, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #004e92 0%, #000428 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 78, 146, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .required {
            color: #e74c3c;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: none;
        }

        .alert-danger {
            background-color: #fee;
            color: #c0392b;
            border-left: 4px solid #e74c3c;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 100vw;
                height: 100vh;
            }


            .login-right {
                padding: 2rem;
                flex: 1;
                justify-content: flex-start;
                overflow-y: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="login-container">
        <!-- Left Side - Image/Branding -->
        <div class="login-left">
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="form-header">
                <h1>Phần mềm quản lý thuế</h1>
                <p>Vui lòng nhập thông tin để truy cập</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label for="user-type" class="form-label">
                        <i class="fas fa-user-tag"></i> Chọn chức vụ <span class="required">*</span>
                    </label>
                    <select class="form-control" id="user-type" name="user_type" required>
                        <option value="nhan-vien" {{ old('user_type') == 'nhan-vien' ? 'selected' : '' }}>Nhân Viên</option>
                        <option value="truong-phong" {{ old('user_type') == 'truong-phong' ? 'selected' : '' }}>Trưởng Phòng
                        </option>
                        <option value="ke-toan" {{ old('user_type') == 'ke-toan' ? 'selected' : '' }}>Kế Toán</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cccd" class="form-label">
                        <i class="fas fa-id-card"></i> Số CCCD <span class="required">*</span>
                    </label>
                    <input type="text" class="form-control" id="cccd" name="cccd" placeholder="Nhập 12 số CCCD"
                        maxlength="12" value="{{ old('cccd') }}" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mật Khẩu <span class="required">*</span>
                    </label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu"
                        required>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Validate CCCD input
        document.getElementById('cccd').addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
        });
    </script>
@endpush
