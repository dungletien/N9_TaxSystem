<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản demo
        $users = [
            [
                'id' => 'NV001',
                'full_name' => 'Nguyễn Văn An',
                'dob' => '1990-01-15',
                'gender' => 'Nam',
                'address' => 'Hà Nội',
                'dependent' => 2,
                'phone' => '0123456789',
                'cccd' => '123456789012',
                'password' => Hash::make('123456'),
                'department' => 'marketing',
                'position' => 'nhân viên',
                'avatar' => null,
            ],
            [
                'id' => 'NV002',
                'full_name' => 'Trần Thị Bình',
                'dob' => '1992-03-20',
                'gender' => 'Nữ',
                'address' => 'Hồ Chí Minh',
                'dependent' => 1,
                'phone' => '0123456790',
                'cccd' => '123456789013',
                'password' => Hash::make('123456'),
                'department' => 'sales',
                'position' => 'nhân viên',
                'avatar' => null,
            ],
            [
                'id' => 'TP001',
                'full_name' => 'Lê Văn Cường',
                'dob' => '1985-07-10',
                'gender' => 'Nam',
                'address' => 'Đà Nẵng',
                'dependent' => 3,
                'phone' => '0123456791',
                'cccd' => '123456789014',
                'password' => Hash::make('123456'),
                'department' => 'marketing',
                'position' => 'nhân viên',
                'avatar' => null,
            ],
            [
                'id' => 'KT001',
                'full_name' => 'Phạm Thị Dung',
                'dob' => '1988-11-25',
                'gender' => 'Nữ',
                'address' => 'Hải Phòng',
                'dependent' => 0,
                'phone' => '0123456792',
                'cccd' => '123456789015',
                'password' => Hash::make('123456'),
                'department' => 'nhân sự',
                'position' => 'nhân viên',
                'avatar' => null,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
