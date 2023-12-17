<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::create([
            'transactionID' => 1000,
            'transaction_name' => 'Điểm giao dịch Cầu Giấy',
            'transaction_address' => '6/118 Nguyễn Khánh Toàn, P.Quan Hoa, Q.Cầu Giấy, Hà Nội',
            'transaction_phone' => '0987654300',
            'transaction_manager_id' => 100003,
            'belongsTo' => 10000,
        ]);

        Transaction::create([
            'transactionID' => 1001,
            'transaction_name' => 'Điểm giao dịch Đông Hưng',
            'transaction_address' => 'Xóm 4, thôn Kinh Hào, xã Đông Kinh, huyện Đông Hưng, tỉnh Thái Bình',
            'transaction_phone' => '0987654301',
            'transaction_manager_id' => 100004,
            'belongsTo' => 10001,
        ]);
    }
}
