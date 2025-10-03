@extends('layouts.app')

@section('title', 'Đăng Nhập - Phần Mềm Tính Thuế')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 400px;
        width: 100%;
    }

    .login-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
        padding: 2rem;
    }

    .login-form {
        padding: 2rem;
    }

    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .form-control {
        border-radius: 25px;
        border: 2px solid #e1e5e9;
        padding: 12px 20px;
        margin-bottom: 1rem;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .required {
        color: red;
    }

    .alert {
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="login-header">
        <h2><i class="fas fa-calculator"></i> PHẦN MỀM TÍNH THUẾ</h2>
        <p class="mb-0">Hệ thống quản lý thuế thu nhập cá nhân</p>
    </div>

    <div class="login-form">
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

            <div class="mb-3">
                <label for="user-type" class="form-label">Chọn chức vụ <span class="required">*</span></label>
                <select class="form-control" id="user-type" name="user_type" required>
                    <option value="nhan-vien" {{ old('user_type') == 'nhan-vien' ? 'selected' : '' }}>Nhân Viên</option>
                    <option value="truong-phong" {{ old('user_type') == 'truong-phong' ? 'selected' : '' }}>Trưởng Phòng</option>
                    <option value="ke-toan" {{ old('user_type') == 'ke-toan' ? 'selected' : '' }}>Kế Toán</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="cccd" class="form-label">CCCD <span class="required">*</span></label>
                <input type="text" class="form-control" id="cccd" name="cccd"
                       maxlength="12" value="{{ old('cccd') }}" required>
                <small class="text-muted">CCCD phải có 12 số.</small>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Mật Khẩu <span class="required">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validate CCCD input
    document.getElementById('cccd').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 12) {
            this.value = this.value.slice(0, 12);
        }
    });
</script>
@endpush
