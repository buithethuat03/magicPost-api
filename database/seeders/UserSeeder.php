<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create boss
        User::create([
            //'userID' => Str::uuid(),
            'userID' => 100000,
            'fullname' => 'Bùi Thế Thuật',
            'phoneNumber' => '0123456789',
            'email' => 'boss@gmail.com',
            'password' => Hash::make('admin'),
            'userType' => '0',
            'belongsTo' => null,
        ]);

        //Create warehouse's leader
        User::create([
            'userID' => 100001,
            'fullname' => 'Hoàng Mạnh Quân',
            'phoneNumber' => '0123456780',
            'email' => 'warehouse1@gmail.com',
            'password' => Hash::make('warehouse1'),
            'userType' => '1',
            'belongsTo' => null,
        ]);

        User::create([
            'userID' => 100002,
            'fullname' => 'Hoàng Mạnh Qu',
            'phoneNumber' => '0123456781',
            'email' => 'warehouse2@gmail.com',
            'password' => Hash::make('warehouse2'),
            'userType' => '1',
            'belongsTo' => null,
        ]);
        
        //Create transaction's leader
        User::create([
            'userID' => 100003,
            'fullname' => 'Lê Tiến Vũ',
            'phoneNumber' => '0123456782',
            'email' => 'transaction1@gmail.com',
            'password' => Hash::make('transaction1'),
            'userType' => '2',
            'belongsTo' => null,
        ]);

        User::create([
            'userID' => 100004,
            'fullname' => 'Lê Tiến',
            'phoneNumber' => '0123456783',
            'email' => 'transaction2@gmail.com',
            'password' => Hash::make('transaction2'),
            'userType' => '2',
            'belongsTo' => null,
        ]);


        //Create warehouse's employee
        User::create([
            'userID' => 100005,
            'fullname' => 'Nguyễn Anh Bình',
            'phoneNumber' => '0123456784',
            'email' => 'warehouse11@gmail.com',
            'password' => Hash::make('warehouse11'),
            'userType' => '3',
            'belongsTo' => 10000,
        ]);

        User::create([
            'userID' => 100006,
            'fullname' => 'Nguyễn Anh Thanh',
            'phoneNumber' => '0123456785',
            'email' => 'warehouse12@gmail.com',
            'password' => Hash::make('warehouse12'),
            'userType' => '3',
            'belongsTo' => 10000,
        ]);

        User::create([
            'userID' => 100007,
            'fullname' => 'Nguyễn Thị Nhàn',
            'phoneNumber' => '0123456786',
            'email' => 'warehouse21@gmail.com',
            'password' => Hash::make('warehouse21'),
            'userType' => '3',
            'belongsTo' => 10001,
        ]);

        User::create([
            'userID' => 100008,
            'fullname' => 'Nguyễn Thị Thìn',
            'phoneNumber' => '0123456787',
            'email' => 'warehouse22@gmail.com',
            'password' => Hash::make('warehouse22'),
            'userType' => '3',
            'belongsTo' => 10001,
        ]);

        //Create transaction's employee
        User::create([
            'userID' => 100009,
            'fullname' => 'Bùi Gia Bảo',
            'phoneNumber' => '0123456790',
            'email' => 'transaction11@gmail.com',
            'password' => Hash::make('transaction11'),
            'userType' => '4',
            'belongsTo' => 1000,
        ]);

        User::create([
            'userID' => 100010,
            'fullname' => 'Bùi Gia Thanh',
            'phoneNumber' => '0123456791',
            'email' => 'transaction12@gmail.com',
            'password' => Hash::make('transaction12'),
            'userType' => '4',
            'belongsTo' => 1000,
        ]);

        User::create([
            'userID' => 100011,
            'fullname' => 'Bùi Vĩnh Long',
            'phoneNumber' => '0123456792',
            'email' => 'transaction21@gmail.com',
            'password' => Hash::make('transaction21'),
            'userType' => '4',
            'belongsTo' => 1001,
        ]);

        User::create([
            'userID' => 100012,
            'fullname' => 'Bùi Gia Phả',
            'phoneNumber' => '0123456793',
            'email' => 'transaction22@gmail.com',
            'password' => Hash::make('transaction22'),
            'userType' => '4',
            'belongsTo' => 1001,
        ]);

        User::create([
            //'userID' => 100008,
            'fullname' => 'Nguyễn Thị Thìn',
            'phoneNumber' => '0123456794',
            'email' => 'warehouse23@gmail.com',
            'password' => Hash::make('warehouse223'),
            'userType' => '3',
            'belongsTo' => 10001,
        ]);

    }
}
