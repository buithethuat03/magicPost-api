<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\OrderDetail;


class OrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() : void
    {

        // Tạo bản ghi đơn hàng
        OrderDetail::create([
            'orderID' => '069e3c3f-3096-4659-985a-aaa492c4ebcf',
            'sender_name' => 'Bùi Thế Thuật',
            'sender_address' => 'Phạm Văn Đồng, P.Anh Dũng, Q.Dương Kinh, Hải Phòng',
            'sender_phone' => '0123456789',
            'receiver_name' => 'Lê Tiến Vũ',
            'receiver_address' => '44 Trần Thái Tông, Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
            'receiver_phone' => '0123456782',
            'first_transaction_id' => 1100,
            'last_transaction_id' => 1000,
            'first_warehouse_id' => 10001,
            'last_warehouse_id' => 10000,
            'weight' => 5.00,
            'shipping_fee' => 10.00,
            'orderType' => '0', 
            'status' => 'Đã giao hàng', 
            'timeline' => [
                '2023-01-01 10:00:00',//Đã tiếp nhận
                '2023-01-01 12:00:00',//Chờ tập kết 1 đến 
                '2023-01-01 13:00:00',//Rời giao dịch 1
                '2023-01-01 14:00:00',//Đến tập kết 1
                '2023-01-02 16:00:00',//Rời tập kết 1
                '2023-01-02 21:00:00',//Đến tập kết 2
                '2023-01-03 9:00:00',//Rời tập kết 2
                '2023-01-03 11:00:00',//Đến giao dịch 2
                '2023-01-03 14:00:00',//Đang giao hàng
                '2023-01-03 16:00:00',//Đã giao hàng
                null//Không thành công 
                ]
            ]);

        OrderDetail::create([
            'orderID' => 'ad07caaa-9615-40eb-a910-0758e47c21e2',
            'sender_name' => 'Bùi Thế Thuật',
            'sender_address' => 'Phạm Văn Đồng, P.Anh Dũng, Q.Dương Kinh, Hải Phòng',
            'sender_phone' => '0123456789',
            'receiver_name' => 'Lê Tiến Vũ',
            'receiver_address' => '44 Trần Thái Tông, Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
            'receiver_phone' => '0123456782',
            'first_transaction_id' => 1100,
            'last_transaction_id' => 1000,
            'first_warehouse_id' => 10001,
            'last_warehouse_id' => 10000,
            'weight' => 5.00,
            'shipping_fee' => 100.00,
            'orderType' => '0', 
            'status' => 'Đã giao hàng', 
            'timeline' => [
                '2022-01-01 10:00:00',//Đã tiếp nhận
                '2022-01-01 12:00:00',//Chờ tập kết 1 đến 
                '2022-01-01 13:00:00',//Rời giao dịch 1
                '2022-01-01 14:00:00',//Đến tập kết 1
                '2022-01-02 16:00:00',//Rời tập kết 1
                '2022-01-02 21:00:00',//Đến tập kết 2
                '2022-01-03 9:00:00',//Rời tập kết 2
                '2022-01-03 11:00:00',//Đến giao dịch 2
                '2022-01-03 14:00:00',//Đang giao hàng
                '2022-01-03 16:00:00',//Đã giao hàng
                null//Không thành công 
                ]
            ]);

        OrderDetail::create([
            'orderID' => 'dde1b938-b610-48f0-82bc-9472685a0851',
            'sender_name' => 'Bùi Thế Thuật',
            'sender_address' => 'Phạm Văn Đồng, P.Anh Dũng, Q.Dương Kinh, Hải Phòng',
            'sender_phone' => '0123456789',
            'receiver_name' => 'Lê Tiến Vũ',
            'receiver_address' => '44 Trần Thái Tông, Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
            'receiver_phone' => '0123456782',
            'first_transaction_id' => 1100,
            'last_transaction_id' => 1000,
            'first_warehouse_id' => 10001,
            'last_warehouse_id' => 10000,
            'weight' => 5.00,
            'shipping_fee' => 1000.00,
            'orderType' => '0', 
            'status' => 'Không thành công', 
            'timeline' => [
                '2021-01-01 10:00:00',//Đã tiếp nhận
                '2021-01-01 12:00:00',//Chờ tập kết 1 đến 
                '2021-01-01 13:00:00',//Rời giao dịch 1
                '2021-01-01 14:00:00',//Đến tập kết 1
                '2021-01-02 16:00:00',//Rời tập kết 1
                '2021-01-02 21:00:00',//Đến tập kết 2
                '2021-01-03 9:00:00',//Rời tập kết 2
                '2021-01-03 11:00:00',//Đến giao dịch 2
                '2021-01-03 14:00:00',//Đang giao hàng
                null,//Đã giao hàng
                '2021-01-03 16:00:00'//Không thành công 
            ],
        ]);
    }
}