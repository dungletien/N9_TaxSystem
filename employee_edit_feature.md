# 🆕 Tính năng mới: Chỉnh sửa thông tin nhân viên

## 🎯 **Mô tả tính năng**

Đã bổ sung chức năng chỉnh sửa thông tin nhân viên trong phần **Quản lý nhân viên** của kế toán.

## ✅ **Các thành phần đã thêm:**

### 1. **Controller Methods mới**

-   `getEmployee($id)` - Lấy thông tin chi tiết 1 nhân viên
-   `updateEmployee(Request $request, $id)` - Cập nhật thông tin nhân viên

### 2. **Routes mới**

-   `GET /accountant/employees/{id}` - API lấy thông tin nhân viên
-   `PUT /accountant/employees/{id}` - API cập nhật thông tin nhân viên

### 3. **Giao diện**

-   Thêm button "Sửa" (màu xanh) bên cạnh button "Xóa"
-   Modal popup với form chỉnh sửa đầy đủ thông tin:
    -   Mã nhân viên (disabled)
    -   Họ và tên
    -   Phòng ban
    -   Chức vụ
    -   Số điện thoại
    -   CCCD
    -   Giới tính
    -   Số người phụ thuộc
    -   Vai trò (nhân viên/trưởng phòng/kế toán)
    -   Địa chỉ

### 4. **JavaScript Functions**

-   `editEmployee(id)` - Hiển thị modal và load dữ liệu
-   `updateEmployee()` - Gửi request cập nhật

## 🚀 **Cách sử dụng:**

1. **Đăng nhập với tài khoản kế toán**
2. **Vào tab "Quản lý nhân viên"**
3. **Click button "Sửa" (màu xanh) bên cạnh nhân viên muốn chỉnh sửa**
4. **Chỉnh sửa thông tin trong modal popup**
5. **Click "Cập nhật" để lưu thay đổi**

## 🔐 **Bảo mật:**

-   ✅ CSRF token protection
-   ✅ Validation đầy đủ cho tất cả fields
-   ✅ Unique validation cho phone và CCCD (trừ chính record đang sửa)
-   ✅ Error handling và logging
-   ✅ Authorization check (chỉ kế toán mới có quyền)

## 📝 **Validation Rules:**

-   `full_name`: required, string, max 100 chars
-   `department`: required, enum (marketing|sales|nhân sự|kinh doanh)
-   `position`: required, enum (nhân viên)
-   `phone`: required, 10 chars, unique (except current record)
-   `cccd`: required, 12 chars, unique (except current record)
-   `role`: required, enum (ke-toan|nhan-vien|truong-phong)
-   `gender`: nullable, enum (Nam|Nữ)
-   `address`: nullable, string, max 255 chars
-   `dependent`: nullable, integer, min 0

## 🎨 **UI/UX Features:**

-   Loading states với spinner
-   Success/Error messages
-   Form validation trước khi submit
-   Auto-close modal sau khi cập nhật thành công
-   Auto-reload bảng nhân viên sau khi cập nhật

## 🧪 **Test Cases:**

1. **Sửa thông tin hợp lệ** → Success
2. **Sửa với phone/CCCD bị trùng** → Error validation
3. **Sửa với thông tin thiếu** → Error validation
4. **Sửa với ID không tồn tại** → Error not found
5. **Test CSRF protection** → Error 419 nếu thiếu token

Tính năng đã hoàn tất và sẵn sàng sử dụng! 🎉
