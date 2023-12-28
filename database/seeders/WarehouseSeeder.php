<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;



class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::create([
            'warehouseID' => 10000,
            'warehouse_name' => 'Điểm tập kết Hà Nội',
            'warehouse_address' => '278 Bạch Đằng, P.Chương Dương, Q.Hoàn Kiếm, Hà Nội',
            'warehouse_phone' => '0987654311',
            'warehouse_manager_id' => 100001,
        ]);

        Warehouse::create([
            'warehouseID' => 10001,
            'warehouse_name' => 'Điểm tập kết Hải Phòng',
            'warehouse_address' => '43 Phạm Phú Thứ, P.Hạ Lý, Q.Hồng Bàng, Hải Phòng',
            'warehouse_phone' => '0987654312',
            'warehouse_manager_id' => 100002,
        ]);

        Warehouse::create([
            'warehouseID' => 10002,
            'warehouse_name' => 'Điểm tập kết Đà Nẵng',
            'warehouse_address' => '146 Duy Tân, P.Hòa Thuận Tây, Q.Hải Châu, Đà Nẵng',
            'warehouse_phone' => '0987654313',
            'warehouse_manager_id' => 100003,
        ]);

        Warehouse::create([
            'warehouseID' => 10003,
            'warehouse_name' => 'Điểm tập kết Hồ Chí Minh',
            'warehouse_address' => '44A Nguyễn Hiền, Phường 4, Quận 3, TP.Hồ Chí Minh',
            'warehouse_phone' => '0987654314',
            'warehouse_manager_id' => 100004,
        ]);

        Warehouse::create([
            'warehouseID' => 10004,
            'warehouse_name' => 'Điểm tập kết Cần Thơ',
            'warehouse_address' => '15A Trần Phú, P.Cái Khế, Q.Ninh Kiều, Cần Thơ',
            'warehouse_phone' => '0987654315',
            'warehouse_manager_id' => 100005,
        ]);
    }
}
