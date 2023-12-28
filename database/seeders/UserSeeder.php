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
            'phoneNumber' => '0123456790',
            'email' => 'warehouse1@gmail.com',
            'password' => Hash::make('warehouse1'),
            'userType' => '1',
            'belongsTo' => 10000,
        ]);

        User::create([
            'userID' => 100002,
            'fullname' => 'Hoàng Mạnh Quâ',
            'phoneNumber' => '0123456791',
            'email' => 'warehouse2@gmail.com',
            'password' => Hash::make('warehouse2'),
            'userType' => '1',
            'belongsTo' => 10001,
        ]);
        
        User::create([
            'userID' => 100003,
            'fullname' => 'Hoàng Mạnh Qu',
            'phoneNumber' => '0123456792',
            'email' => 'warehouse3@gmail.com',
            'password' => Hash::make('warehouse3'),
            'userType' => '1',
            'belongsTo' => 10002,
        ]);

        User::create([
            'userID' => 100004,
            'fullname' => 'Hoàng Mạnh Q',
            'phoneNumber' => '0123456793',
            'email' => 'warehouse4@gmail.com',
            'password' => Hash::make('warehouse4'),
            'userType' => '1',
            'belongsTo' => 10003,
        ]);

        User::create([
            'userID' => 100005,
            'fullname' => 'Hoàng Mạnh',
            'phoneNumber' => '0123456794',
            'email' => 'warehouse5@gmail.com',
            'password' => Hash::make('warehouse5'),
            'userType' => '1',
            'belongsTo' => 10004,
        ]);



        //Create transaction's leader
        User::create([
            'userID' => 110001,
            'fullname' => 'Lê Tiến Vũ',
            'phoneNumber' => '0123456700',
            'email' => 'transaction11@gmail.com',
            'password' => Hash::make('transaction11'),
            'userType' => '2',
            'belongsTo' => 1000,
        ]);

        User::create([
            'userID' => 110002,
            'fullname' => 'Lê Tiến V',
            'phoneNumber' => '0123456701',
            'email' => 'transaction12@gmail.com',
            'password' => Hash::make('transaction12'),
            'userType' => '2',
            'belongsTo' => 1001,
        ]);

        User::create([
            'userID' => 210001,
            'fullname' => 'Lê Tiến',
            'phoneNumber' => '0123456703',
            'email' => 'transaction21@gmail.com',
            'password' => Hash::make('transaction21'),
            'userType' => '2',
            'belongsTo' => 1100,
        ]);

        User::create([
            'userID' => 210002,
            'fullname' => 'Lê Tiến',
            'phoneNumber' => '0123456704',
            'email' => 'transaction22@gmail.com',
            'password' => Hash::make('transaction22'),
            'userType' => '2',
            'belongsTo' => 1101,
        ]);

        User::create([
            'userID' => 310001,
            'fullname' => 'Lê Tiến Mạnh',
            'phoneNumber' => '0123456705',
            'email' => 'transaction31@gmail.com',
            'password' => Hash::make('transaction31'),
            'userType' => '2',
            'belongsTo' => 1200,
        ]);

        User::create([
            'userID' => 310002,
            'fullname' => 'Lê Tiến Mạn',
            'phoneNumber' => '0123456706',
            'email' => 'transaction32@gmail.com',
            'password' => Hash::make('transaction32'),
            'userType' => '2',
            'belongsTo' => 1201,
        ]);
        
        User::create([
            'userID' => 410001,
            'fullname' => 'Lê Tiến Mạ',
            'phoneNumber' => '0123456707',
            'email' => 'transaction41@gmail.com',
            'password' => Hash::make('transaction41'),
            'userType' => '2',
            'belongsTo' => 1300,
        ]);

        User::create([
            'userID' => 410002,
            'fullname' => 'Lê Tiến Minh',
            'phoneNumber' => '0123456708',
            'email' => 'transaction42@gmail.com',
            'password' => Hash::make('transaction42'),
            'userType' => '2',
            'belongsTo' => 1301,
        ]);

        User::create([
            'userID' => 510001,
            'fullname' => 'Lê Tiến Min',
            'phoneNumber' => '0123456709',
            'email' => 'transaction51@gmail.com',
            'password' => Hash::make('transaction51'),
            'userType' => '2',
            'belongsTo' => 1400,
        ]);

        User::create([
            'userID' => 510002,
            'fullname' => 'Lê Tiến Mi',
            'phoneNumber' => '0123456710',
            'email' => 'transaction52@gmail.com',
            'password' => Hash::make('transaction52'),
            'userType' => '2',
            'belongsTo' => 1401,
        ]);

        //Create warehouse's employee
        User::create([
            'userID' => 1000000,
            'fullname' => 'Nguyễn Anh Bình',
            'phoneNumber' => '0123457600',
            'email' => 'warehouse11@gmail.com',
            'password' => Hash::make('warehouse11'),
            'userType' => '3',
            'belongsTo' => 10000,
        ]);

        User::create([
            'userID' => 1000001,
            'fullname' => 'Nguyễn Anh Thanh',
            'phoneNumber' => '0123457601',
            'email' => 'warehouse12@gmail.com',
            'password' => Hash::make('warehouse12'),
            'userType' => '3',
            'belongsTo' => 10000,
        ]);

        User::create([
            'userID' => 2000000,
            'fullname' => 'Nguyễn Thị Nhàn',
            'phoneNumber' => '0123457602',
            'email' => 'warehouse21@gmail.com',
            'password' => Hash::make('warehouse21'),
            'userType' => '3',
            'belongsTo' => 10001,
        ]);

        User::create([
            'userID' => 2000001,
            'fullname' => 'Nguyễn Thị Thìn',
            'phoneNumber' => '0123457603',
            'email' => 'warehouse22@gmail.com',
            'password' => Hash::make('warehouse22'),
            'userType' => '3',
            'belongsTo' => 10001,
        ]);

        //Create transaction's employee
        User::create([
            'userID' => 1100000,
            'fullname' => 'Bùi Gia Bảo',
            'phoneNumber' => '0123455600',
            'email' => 'transaction111@gmail.com',
            'password' => Hash::make('transaction111'),
            'userType' => '4',
            'belongsTo' => 1000,
        ]);

        User::create([
            'userID' => 1100001,
            'fullname' => 'Bùi Gia Thanh',
            'phoneNumber' => '0123455601',
            'email' => 'transaction112@gmail.com',
            'password' => Hash::make('transaction112'),
            'userType' => '4',
            'belongsTo' => 1000,
        ]);

        User::create([
            'userID' => 2100000,
            'fullname' => 'Bùi Vĩnh Long',
            'phoneNumber' => '0123455602',
            'email' => 'transaction211@gmail.com',
            'password' => Hash::make('transaction211'),
            'userType' => '4',
            'belongsTo' => 1100,
        ]);

        User::create([
            'userID' => 2100001,
            'fullname' => 'Bùi Gia Phả',
            'phoneNumber' => '0123455603',
            'email' => 'transaction212@gmail.com',
            'password' => Hash::make('transaction212'),
            'userType' => '4',
            'belongsTo' => 1100,
        ]);

    }
}
