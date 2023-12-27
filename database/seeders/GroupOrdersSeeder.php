<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GroupOrders;

class GroupOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        GroupOrders::create([
            'group_ordersID' => 'dde1b938-b610-48f0-82bc-9472685a0851',
            'orders' => [
                'dde1b938-b610-48f0-82bc-9472685a0851',
                'ad07caaa-9615-40eb-a910-0758e47c21e2'
            ]
        ]);
    }
}
