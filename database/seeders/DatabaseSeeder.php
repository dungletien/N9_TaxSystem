<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Deduction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo user mẫu
        $users = [
            [
                'id' => 'NV001',
                'full_name' => 'Nguyễn Văn An',
                'phone' => '0901234567',
                'cccd' => '123456789012',
                'password' => Hash::make('123456'),
                'department' => 'marketing',
                'gender' => 'Nam',
            ],
            [
                'id' => 'NV002',
                'full_name' => 'Trần Thị Bình',
                'phone' => '0901234568',
                'cccd' => '123456789013',
                'password' => Hash::make('123456'),
                'department' => 'sales',
                'gender' => 'Nữ',
            ],
            [
                'id' => 'TP001',
                'full_name' => 'Lê Văn Cường',
                'phone' => '0901234569',
                'cccd' => '123456789014',
                'password' => Hash::make('123456'),
                'department' => 'marketing',
                'gender' => 'Nam',
            ],
            [
                'id' => 'KT001',
                'full_name' => 'Phạm Thị Dung',
                'phone' => '0901234570',
                'cccd' => '123456789015',
                'password' => Hash::make('123456'),
                'department' => 'nhân sự',
                'gender' => 'Nữ',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // Tạo roles
        $roles = [
            ['user_id' => 'NV001', 'user_type' => 'nhan-vien'],
            ['user_id' => 'NV002', 'user_type' => 'nhan-vien'],
            ['user_id' => 'TP001', 'user_type' => 'truong-phong'],
            ['user_id' => 'KT001', 'user_type' => 'ke-toan'],
        ];

        foreach ($roles as $roleData) {
            UserRole::create($roleData);
        }

        // Tạo mức giảm trừ cho năm 2024
        for ($month = 1; $month <= 12; $month++) {
            Deduction::create([
                'month' => $month,
                'year' => 2024,
                'self_deduction' => 11000000,
                'dependent_deduction' => 4400000,
            ]);
        }

        // Tạo mức giảm trừ cho năm 2025
        for ($month = 1; $month <= 12; $month++) {
            Deduction::create([
                'month' => $month,
                'year' => 2025,
                'self_deduction' => 11000000,
                'dependent_deduction' => 4400000,
            ]);
        }
    }
}
