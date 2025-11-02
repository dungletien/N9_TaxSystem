<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\ManagerController;

// Trang chủ và đăng nhập
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes cho Nhân viên
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::post('/update-profile', [EmployeeController::class, 'updateProfile'])->name('update.profile');
    Route::get('/salaries', [EmployeeController::class, 'getSalaries'])->name('salaries');
    Route::post('/calculate-tax', [EmployeeController::class, 'calculateTax'])->name('calculate.tax');
});

// Routes cho Kế toán
Route::prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');

    // Quản lý nhân viên
    Route::get('/employees', [AccountantController::class, 'getEmployees'])->name('employees');
    Route::get('/employees/{id}', [AccountantController::class, 'getEmployee'])->name('employees.get');
    Route::put('/employees/{id}', [AccountantController::class, 'updateEmployee'])->name('employees.update');
    Route::delete('/employees/{id}', [AccountantController::class, 'deleteEmployee'])->name('employees.delete');

    // Quản lý tài khoản
    Route::post('/accounts', [AccountantController::class, 'createAccount'])->name('accounts.create');
    Route::get('/accounts', [AccountantController::class, 'getAccounts'])->name('accounts');
    Route::post('/accounts/{id}/reset-password', [AccountantController::class, 'resetPassword'])->name('accounts.reset-password');

    // Thiết lập giảm trừ
    Route::get('/deductions', [AccountantController::class, 'getDeductions'])->name('deductions');
    Route::post('/deductions', [AccountantController::class, 'setupDeductions'])->name('deductions.setup');

    // Lương và thuế
    Route::get('/salaries', [AccountantController::class, 'getSalaries'])->name('salaries');
    Route::post('/salaries', [AccountantController::class, 'saveSalaries'])->name('salaries.save');

    // Quyết toán thuế
    Route::get('/annual-tax', [AccountantController::class, 'getAnnualTax'])->name('annual.tax');
    Route::post('/annual-tax', [AccountantController::class, 'saveAnnualTax'])->name('annual.tax.save');
    Route::post('/calculate-annual-tax', [AccountantController::class, 'calculateAnnualTax'])->name('calculate.annual.tax');
});

// Routes cho Trưởng phòng
Route::prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/employees', [ManagerController::class, 'getEmployees'])->name('employees');
    Route::delete('/employees', [ManagerController::class, 'deleteEmployee'])->name('delete-employee');
    Route::get('/department', [ManagerController::class, 'getDepartment'])->name('department');
    Route::get('/salaries', [ManagerController::class, 'getSalaries'])->name('salaries');
    Route::get('/monthly-tax', [ManagerController::class, 'getMonthlyTax'])->name('monthly-tax');
    Route::get('/annual-tax', [ManagerController::class, 'getAnnualTax'])->name('annual-tax');
});
