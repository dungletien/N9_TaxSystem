# Hệ Thống Quản Lý Lương và Thuế Thu Nhập Cá Nhân

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
</p>

## Mô tả dự án

Hệ thống quản lý lương và thuế thu nhập cá nhân được xây dựng bằng Laravel Framework, phục vụ cho việc tính toán, quản lý và quyết toán thuế TNCN cho doanh nghiệp. Hệ thống hỗ trợ 3 vai trò chính: **Nhân viên**, **Trưởng phòng** và **Kế toán**.

## Tính năng chính

### 🧑‍💼 Nhân viên
- Xem và cập nhật thông tin cá nhân
- Xem lịch sử lương và thuế theo năm (bao gồm số người phụ thuộc)
- Tính thử thuế TNCN theo mức lương và số người phụ thuộc
- Thay đổi mật khẩu

### 👨‍💼 Trưởng phòng
- Dashboard tổng quan phòng ban
- Quản lý danh sách nhân viên trong phòng
- Xem báo cáo lương và thuế của phòng ban
- Xóa nhân viên (không thể xóa chính mình)

### 🧮 Kế toán
- Quản lý tất cả nhân viên trong công ty
- Tạo và quản lý tài khoản người dùng
- Thiết lập mức giảm trừ gia cảnh theo tháng/năm
- Nhập và lưu dữ liệu lương hàng tháng
- Thực hiện quyết toán thuế hàng năm
- Reset mật khẩu cho nhân viên

## Yêu cầu hệ thống

- **PHP:** >= 8.2
- **Laravel:** 11.x
- **Database:** SQLite (mặc định) hoặc MySQL
- **Node.js:** >= 16.x (để build frontend assets)
- **Composer:** >= 2.0

## Cài đặt

### 1. Clone repository
```bash
git clone <repository-url>
cd tax_system/tax
```

### 2. Cài đặt dependencies
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

### 3. Cấu hình môi trường
```bash
# Copy file cấu hình
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Cấu hình database
Mặc định sử dụng SQLite. Nếu muốn dùng MySQL, cập nhật file `.env`:

**SQLite (mặc định):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

**MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tax_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Tạo database và chạy migration
```bash
# Tạo file SQLite (nếu chưa có)
touch database/database.sqlite

# Chạy migration và seeder
php artisan migrate --seed
```

### 6. Build frontend assets
```bash
npm run dev
# hoặc cho production:
npm run build
```

### 7. Chạy ứng dụng
```bash
php artisan serve
```

Truy cập: `http://localhost:8000`

## Tài khoản mặc định

Sau khi chạy seeder, bạn có thể đăng nhập với các tài khoản sau:

| Vai trò | CCCD | Mật khẩu | Loại tài khoản |
|---------|------|----------|----------------|
| Nhân viên | 123456789012 | 123456 | nhan-vien |
| Trưởng phòng | 123456789013 | 123456 | truong-phong |
| Kế toán | 123456789014 | 123456 | ke-toan |

## Cấu trúc dự án

```
tax/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Xử lý đăng nhập/đăng xuất
│   │   ├── EmployeeController.php      # Chức năng nhân viên
│   │   ├── ManagerController.php       # Chức năng trưởng phòng
│   │   └── AccountantController.php    # Chức năng kế toán
│   └── Models/
│       ├── User.php                    # Model người dùng
│       ├── UserRole.php               # Model vai trò
│       ├── MonthTax.php               # Model thuế tháng
│       ├── YearTax.php                # Model thuế năm
│       └── Deduction.php              # Model giảm trừ
├── database/
│   ├── migrations/                     # File migration
│   ├── seeders/                       # File seeder
│   └── database.sqlite                # Database SQLite
├── resources/
│   └── views/
│       ├── auth/                      # Views đăng nhập
│       ├── employee/                  # Views nhân viên
│       ├── manager/                   # Views trưởng phòng
│       └── accountant/                # Views kế toán
└── routes/
    └── web.php                        # Định nghĩa routes
```

## API Documentation

### Authentication
- `POST /login` - Đăng nhập
- `POST /logout` - Đăng xuất

### Employee APIs
- `GET /employee/salaries?year={year}` - Lấy lịch sử lương
- `POST /employee/calculate-tax` - Tính thử thuế
- `POST /employee/update-profile` - Cập nhật thông tin

### Manager APIs
- `GET /manager/employees` - Danh sách nhân viên phòng
- `GET /manager/salaries?month={month}&year={year}` - Lương phòng ban
- `DELETE /manager/employees` - Xóa nhân viên

### Accountant APIs
- `GET /accountant/employees` - Danh sách tất cả nhân viên
- `POST /accountant/accounts` - Tạo tài khoản mới
- `POST /accountant/salaries` - Lưu lương hàng tháng
- `GET /accountant/annual-tax` - Quyết toán thuế năm

## Tính năng thuế TNCN

### Bậc thuế áp dụng
| Thu nhập chịu thuế (tháng) | Thuế suất | Khống trừ luỹ tiến |
|---------------------------|-----------|-------------------|
| ≤ 5 triệu | 5% | 0 |
| > 5 - 10 triệu | 10% | 250.000 |
| > 10 - 18 triệu | 15% | 750.000 |
| > 18 - 32 triệu | 20% | 1.650.000 |
| > 32 - 52 triệu | 25% | 3.250.000 |
| > 52 - 80 triệu | 30% | 5.850.000 |
| > 80 triệu | 35% | 9.850.000 |

### Mức giảm trừ mặc định
- **Giảm trừ bản thân:** 11.000.000 VNĐ/tháng
- **Giảm trừ người phụ thuộc:** 4.400.000 VNĐ/tháng/người

## Troubleshooting

### Lỗi database
```bash
# Xóa và tạo lại database
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### Lỗi permissions
```bash
# Linux/macOS
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows
# Cấp quyền Full Control cho thư mục storage và bootstrap/cache
```

### Lỗi Composer/NPM
```bash
# Clear cache và reinstall
composer clear-cache
composer install --no-cache

npm cache clean --force
npm install
```

## Contributing

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

## License

Dự án được phát hành dưới giấy phép [MIT License](https://opensource.org/licenses/MIT).

## Liên hệ

- **Email:** [your-email@example.com]
- **GitHub:** [your-github-profile]

---

**Lưu ý:** Đây là dự án học tập/demo. Không sử dụng cho môi trường production mà không có các biện pháp bảo
