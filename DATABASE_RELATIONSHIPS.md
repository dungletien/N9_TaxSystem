# Quan hệ giữa các bảng trong hệ thống thuế

## 1. Tổng quan cấu trúc cơ sở dữ liệu

Hệ thống quản lý thuế bao gồm 7 bảng chính:

### Bảng chính:
- **users**: Lưu thông tin nhân viên
- **user_roles**: Lưu vai trò của người dùng
- **month_taxes**: Lưu thông tin thuế theo tháng
- **year_taxes**: Lưu thông tin thuế theo năm
- **deductions**: Lưu thông tin giảm trừ thuế

### Bảng hệ thống:
- **sessions**: Quản lý phiên đăng nhập
- **password_reset_tokens**: Token reset mật khẩu

## 2. Mô tả chi tiết các bảng

### 2.1 Bảng `users` (Bảng trung tâm)
```sql
- id (string, 5) - PRIMARY KEY
- full_name (string, 100)
- dob (date, nullable)
- gender (enum: 'Nam', 'Nữ')
- address (string, nullable)
- dependent (integer, nullable) - Số người phụ thuộc
- phone (string, 10, unique)
- cccd (string, 12, unique)
- password (string)
- department (enum: 'marketing', 'sales', 'nhân sự', 'kinh doanh')
- position (enum: 'nhân viên', default)
- avatar (string, nullable)
- timestamps
```

### 2.2 Bảng `user_roles`
```sql
- user_id (string, 5) - FOREIGN KEY
- user_type (enum: 'ke-toan', 'nhan-vien', 'truong-phong')
- PRIMARY KEY: [user_id, user_type]
- timestamps
```

### 2.3 Bảng `month_taxes`
```sql
- id (auto-increment) - PRIMARY KEY (được thêm sau)
- user_id (string, 10) - FOREIGN KEY
- month (tinyInteger)
- year (year)
- salary (decimal, 15,2)
- tax (decimal, 15,2)
- net_salary (decimal, 15,2)
- UNIQUE: [user_id, month, year]
- timestamps
```

### 2.4 Bảng `year_taxes`
```sql
- user_id (string, 10) - FOREIGN KEY
- year (year)
- total_salary (decimal, 15,2)
- total_tax (decimal, 15,2)
- net_salary (decimal, 15,2)
- PRIMARY KEY: [user_id, year]
- timestamps
```

### 2.5 Bảng `deductions`
```sql
- id (auto-increment) - PRIMARY KEY
- month (tinyInteger, nullable)
- year (year)
- self_deduction (decimal, 15,2) - Giảm trừ bản thân
- dependent_deduction (decimal, 15,2) - Giảm trừ người phụ thuộc
- timestamps
```

## 3. Quan hệ giữa các bảng

### 3.1 Quan hệ One-to-Many (1:N)

#### `users` → `user_roles` (1:N)
- **Khóa ngoại**: `user_roles.user_id` → `users.id`
- **Mô tả**: Một người dùng có thể có nhiều vai trò
- **Ràng buộc**: CASCADE DELETE (khi xóa user, xóa tất cả roles)
- **Eloquent**: 
  ```php
  // User Model
  public function userRoles(): HasMany
  {
      return $this->hasMany(UserRole::class, 'user_id');
  }
  
  // UserRole Model
  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class, 'user_id');
  }
  ```

#### `users` → `month_taxes` (1:N)
- **Khóa ngoại**: `month_taxes.user_id` → `users.id`
- **Mô tả**: Một người dùng có nhiều bản ghi thuế theo tháng
- **Ràng buộc**: CASCADE DELETE
- **Unique constraint**: [user_id, month, year] - Mỗi user chỉ có 1 bản ghi thuế/tháng/năm
- **Eloquent**:
  ```php
  // User Model
  public function monthTaxes(): HasMany
  {
      return $this->hasMany(MonthTax::class, 'user_id');
  }
  
  // MonthTax Model
  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class, 'user_id');
  }
  ```

#### `users` → `year_taxes` (1:N)
- **Khóa ngoại**: `year_taxes.user_id` → `users.id`
- **Mô tả**: Một người dùng có nhiều bản ghi thuế theo năm
- **Ràng buộc**: CASCADE DELETE
- **Primary key**: [user_id, year] - Mỗi user chỉ có 1 bản ghi thuế/năm
- **Eloquent**:
  ```php
  // User Model
  public function yearTaxes(): HasMany
  {
      return $this->hasMany(YearTax::class, 'user_id');
  }
  
  // YearTax Model
  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class, 'user_id');
  }
  ```

### 3.2 Bảng độc lập

#### `deductions`
- **Mô tả**: Bảng lưu thông tin giảm trừ thuế theo thời gian
- **Không có khóa ngoại**: Bảng này độc lập, không liên kết trực tiếp với bảng khác
- **Mục đích**: Lưu mức giảm trừ cá nhân và người phụ thuộc theo từng tháng/năm
- **Cách sử dụng**: Được tham chiếu bởi logic business để tính thuế

## 4. Sơ đồ quan hệ Entity-Relationship (ERD)

```
┌─────────────────┐       ┌──────────────────┐
│     users       │ 1---N │   user_roles     │
│                 │       │                  │
│ • id (PK)      │←──────│• user_id (FK)   │
│ • full_name     │       │ • user_type      │
│ • dob           │       │ • PRIMARY KEY:   │
│ • gender        │       │   [user_id,      │
│ • address       │       │    user_type]    │
│ • dependent     │       └──────────────────┘
│ • phone         │
│ • cccd          │       ┌──────────────────┐
│ • password      │ 1---N │   month_taxes    │
│ • department    │       │                  │
│ • position      │←──────│• id (PK)        │
│ • avatar        │       │ • user_id (FK)   │
└─────────────────┘       │ • month          │
                          │ • year           │
                          │ • salary         │
                          │ • tax            │
                          │ • net_salary     │
                          │ • UNIQUE:        │
                          │   [user_id,      │
                          │    month, year]  │
                          └──────────────────┘
                          
                          ┌──────────────────┐
                    1---N │   year_taxes     │
                          │                  │
                    ──────│• user_id (FK)   │
                          │ • year           │
                          │ • total_salary   │
                          │ • total_tax      │
                          │ • net_salary     │
                          │ • PRIMARY KEY:   │
                          │   [user_id, year]│
                          └──────────────────┘

┌─────────────────┐
│   deductions    │ (Bảng độc lập)
│                 │
│ • id (PK)      │
│ • month         │
│ • year          │
│ • self_deduction│
│ • dependent_    │
│   deduction     │
└─────────────────┘
```

## 5. Ràng buộc và quy tắc

### 5.1 Ràng buộc toàn vẹn
1. **Khóa chính**:
   - `users.id`: Unique string(5)
   - `user_roles`: Composite key [user_id, user_type]
   - `month_taxes`: Auto-increment id + unique [user_id, month, year]
   - `year_taxes`: Composite key [user_id, year]
   - `deductions.id`: Auto-increment

2. **Khóa ngoại**:
   - Tất cả có CASCADE DELETE
   - Đảm bảo tính nhất quán dữ liệu

3. **Ràng buộc duy nhất**:
   - `users.phone`: Unique
   - `users.cccd`: Unique
   - `month_taxes`: [user_id, month, year]

### 5.2 Quy tắc nghiệp vụ
1. **Vai trò người dùng**: Một user có thể có nhiều role
2. **Thuế tháng**: Mỗi user chỉ có 1 bản ghi thuế cho mỗi tháng/năm
3. **Thuế năm**: Mỗi user chỉ có 1 bản ghi thuế cho mỗi năm
4. **Giảm trừ**: Áp dụng chung cho tất cả users trong cùng thời kỳ

## 6. Truy vấn thường dùng

### 6.1 Lấy thông tin user và roles
```php
$user = User::with('userRoles')->find($userId);
$roles = $user->userRoles->pluck('user_type');
```

### 6.2 Lấy thuế tháng của user
```php
$monthTaxes = User::find($userId)->monthTaxes()
    ->where('year', $year)
    ->orderBy('month')
    ->get();
```

### 6.3 Tính tổng thuế năm
```php
$totalTax = MonthTax::where('user_id', $userId)
    ->where('year', $year)
    ->sum('tax');
```

### 6.4 Lấy giảm trừ theo thời gian
```php
$deduction = Deduction::where('year', $year)
    ->where('month', $month)
    ->first();
```

## 7. Lưu ý kỹ thuật

1. **Composite Primary Keys**: `month_taxes` và `year_taxes` sử dụng composite keys
2. **String Primary Key**: `users` sử dụng string key thay vì auto-increment
3. **Decimal Precision**: Tất cả số tiền sử dụng decimal(15,2)
4. **Enum Values**: Sử dụng enum cho gender, department, position, user_type
5. **Nullable Fields**: Một số trường cho phép null như address, dob, avatar
