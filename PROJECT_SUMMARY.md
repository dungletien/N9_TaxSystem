# Hệ Thống Phần Mềm Tính Thuế Thu Nhập Cá Nhân - Laravel

## 🎯 Tổng quan dự án

Đây là phiên bản Laravel của hệ thống tính thuế thu nhập cá nhân, được phát triển dựa trên dự án PTTKPM_Thue gốc với đầy đủ tính năng và cấu trúc tối ưu.

## 🏗️ Kiến trúc hệ thống

### **Công nghệ sử dụng:**
- **Framework:** Laravel 11
- **Database:** MySQL (Aiven Cloud)
- **Frontend:** Blade Templates + Bootstrap 5 + jQuery + Font Awesome
- **Authentication:** Laravel Session-based
- **API:** RESTful APIs

### **Cấu trúc thư mục:**
```
tax/
├── app/
│   ├── Http/Controllers/     # Controllers cho từng vai trò
│   └── Models/              # Eloquent Models
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Dữ liệu mẫu
├── resources/views/         # Blade templates
├── routes/web.php          # Routes definition
└── public/                 # Static assets
```

## 🗄️ Cơ sở dữ liệu

### **Kết nối Aiven MySQL:**
- **Host:** mysql-de64caf-dungdb.e.aivencloud.com
- **Port:** 18957
- **Database:** defaultdb
- **SSL:** Enabled với bypass certificate verification

### **Cấu trúc bảng:**
1. **`users`** - Thông tin nhân viên
   - Primary Key: `id` (string, 10 chars)
   - Fields: full_name, dob, gender, address, dependent, phone, cccd, password, department, position, avatar

2. **`user_roles`** - Phân quyền người dùng
   - Composite Key: (user_id, user_type)
   - Types: ke-toan, nhan-vien, truong-phong

3. **`deductions`** - Mức giảm trừ thuế
   - Fields: month, year, self_deduction, dependent_deduction

4. **`month_taxes`** - Lương và thuế hàng tháng
   - Composite Key: (user_id, month, year)
   - Fields: salary, tax, net_salary

5. **`year_taxes`** - Quyết toán thuế hàng năm
   - Composite Key: (user_id, year)
   - Fields: total_salary, total_tax, net_salary

## 👥 Hệ thống phân quyền

### **1. Nhân viên (`nhan-vien`)**
**Routes:** `/employee/*`
**Tính năng:**
- ✅ Cập nhật thông tin cá nhân (họ tên, địa chỉ, điện thoại, ngày sinh, người phụ thuộc)
- ✅ Xem lương và thuế cá nhân theo năm
- ✅ Tính thử thuế với input salary và dependent
- ✅ Dashboard responsive với navigation tabs

### **2. Trưởng phòng (`truong-phong`)**
**Routes:** `/manager/*`
**Tính năng:**
- ✅ Xem danh sách nhân viên trong phòng ban
- ✅ Xem lương và thuế của phòng ban theo tháng/năm
- ✅ Dashboard quản lý với bộ lọc thời gian
- ✅ Quyền truy cập giới hạn theo department

### **3. Kế toán (`ke-toan`)**
**Routes:** `/accountant/*`
**Tính năng:**
- ✅ **Quản lý nhân viên:** Xem, xóa theo phòng ban
- ✅ **Quản lý tài khoản:** Tạo account mới với validation
- ✅ **Thiết lập giảm trừ:** Config cho 12 tháng/năm
- ✅ **Lương và thuế:** Nhập salary, tự động tính thuế
- ✅ **Quyết toán thuế:** Báo cáo annual tax theo phòng ban

## 🔐 Tài khoản demo

| Vai trò | CCCD | Mật khẩu | Mã NV |
|---------|------|----------|-------|
| Nhân viên | `123456789012` | `123456` | NV001 |
| Nhân viên | `123456789013` | `123456` | NV002 |
| Trưởng phòng | `123456789014` | `123456` | TP001 |
| Kế toán | `123456789015` | `123456` | KT001 |

## ⚙️ Tính năng chính

### **Tính thuế thu nhập cá nhân:**
- Áp dụng bậc thuế lũy tiến theo quy định của Việt Nam
- Tự động tính giảm trừ gia cảnh (bản thân + người phụ thuộc)
- Công thức tính thuế chính xác với 7 bậc thuế

### **Quản lý dữ liệu:**
- CRUD operations với Eloquent ORM
- Validation đầy đủ cho tất cả forms
- Soft deletes và foreign key constraints
- Data seeding cho development

### **API Endpoints:**
- RESTful API design
- JSON responses cho AJAX calls
- Error handling và status codes
- CSRF protection

### **Giao diện người dùng:**
- Responsive design với Bootstrap 5
- Modern gradient themes cho từng vai trò
- Interactive dashboards với multiple tabs
- Real-time form validation

## 🚀 Cài đặt và sử dụng

### **1. Khởi chạy server:**
```bash
php artisan serve
```

### **2. Truy cập ứng dụng:**
- URL: `http://127.0.0.1:8000`
- Chọn chức vụ và đăng nhập với tài khoản demo

### **3. Database operations:**
```bash
# Reset database
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status

# Test database connection
php artisan tinker
```

## 📊 So sánh với dự án gốc

| Tính năng | Dự án gốc (PHP thuần) | Laravel version |
|-----------|----------------------|-----------------|
| Framework | Vanilla PHP | Laravel 11 |
| Database | MySQL với raw queries | Eloquent ORM |
| Authentication | Session PHP | Laravel Session |
| Routing | File-based | Laravel Routes |
| Templates | Pure HTML/PHP | Blade Templates |
| Validation | Manual validation | Laravel Validation |
| Security | Basic security | CSRF, SQL injection protection |
| Code structure | Procedural | MVC Architecture |

## 🔧 Tối ưu hóa

### **Performance:**
- Eloquent relationships để optimize queries
- Proper indexing trên database
- Caching cho static data

### **Security:**
- CSRF token protection
- SQL injection prevention với Eloquent
- Input validation và sanitization
- Password hashing với bcrypt

### **Maintainability:**
- MVC architecture
- Service layer pattern
- Reusable components
- Clear separation of concerns

## 📈 Mở rộng tương lai

### **Tính năng có thể thêm:**
- Xuất báo cáo Excel/PDF
- Email notifications
- Multi-language support
- Audit trails
- Advanced reporting dashboard
- Mobile app API
- Real-time notifications

### **Technical improvements:**
- Queue jobs cho heavy tasks
- Redis caching
- API rate limiting
- Unit testing
- Docker containerization

## 🎉 Kết luận

Dự án Laravel Tax System đã được hoàn thành thành công với đầy đủ tính năng của phiên bản gốc, đồng thời được nâng cấp với:
- Modern framework architecture
- Cloud database integration
- Enhanced security features
- Professional UI/UX design
- Scalable code structure

Hệ thống sẵn sàng để deploy production và có thể dễ dàng mở rộng cho các tính năng mới trong tương lai.
