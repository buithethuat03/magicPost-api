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
            'warehouse_name' => 'Điểm tập kết Thái Bình',
            'warehouse_address' => '224B Lê Đại Hành, P. Kỳ Bá, Thái Bình',
            'warehouse_phone' => '0987654312',
            'warehouse_manager_id' => 100002,
        ]);
    }
}
