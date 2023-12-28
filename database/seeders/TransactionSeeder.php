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
            'transaction_phone' => '0987645300',
            'transaction_manager_id' => 110001,
            'belongsTo' => 10000,
        ]);

        Transaction::create([
            'transactionID' => 1001,
            'transaction_name' => 'Điểm giao dịch Hà Đông',
            'transaction_address' => '495 Quang Trung, P.Phú La, Q.Hà Đông, Hà Nội',
            'transaction_phone' => '0987645301',
            'transaction_manager_id' => 110002,
            'belongsTo' => 10000,
        ]);

        Transaction::create([
            'transactionID' => 1100,
            'transaction_name' => 'Điểm giao dịch Dương Kinh',
            'transaction_address' => '296 Phạm Văn Đồng, P.Anh Dũng, Q.Dương Kinh, Hải Phòng',
            'transaction_phone' => '0987645310',
            'transaction_manager_id' => 210001,
            'belongsTo' => 10001,
        ]);

        Transaction::create([
            'transactionID' => 1101,
            'transaction_name' => 'Điểm giao dịch Đồ Sơn',
            'transaction_address' => '176 Lý Thánh Tông, P.Vạn Sơn, Q.Đồ Sơn, Hải Phòng',
            'transaction_phone' => '0987645311',
            'transaction_manager_id' => 210002,
            'belongsTo' => 10001,
        ]);

        Transaction::create([
            'transactionID' => 1200,
            'transaction_name' => 'Điểm giao dịch Hải Châu',
            'transaction_address' => '129 Lê Đình Dương, P.Nam Dương, Q.Hải Châu, Đà Nẵng',
            'transaction_phone' => '0987645320',
            'transaction_manager_id' => 310001,
            'belongsTo' => 10002,
        ]);

        Transaction::create([
            'transactionID' => 1201,
            'transaction_name' => 'Điểm giao dịch Hòa Vang',
            'transaction_address' => 'Thôn Dương Lâm, X.Hòa Vang, H.Hòa Vang, Đà Nẵng',
            'transaction_phone' => '0987645321',
            'transaction_manager_id' => 310002,
            'belongsTo' => 10002,
        ]);

        Transaction::create([
            'transactionID' => 1300,
            'transaction_name' => 'Điểm giao dịch Quận 1',
            'transaction_address' => '25/65 Nguyễn Bỉnh Khiêm, P.Bến Nghé, Quận 1, TP.Hồ Chí Minh',
            'transaction_phone' => '0987645330',
            'transaction_manager_id' => 410001,
            'belongsTo' => 10003,
        ]);

        Transaction::create([
            'transactionID' => 1301,
            'transaction_name' => 'Điểm giao dịch Quận 10',
            'transaction_address' => '71 Trần Thiện Chánh, Phường 12, Quận 10, TP.Hồ Chí Minh',
            'transaction_phone' => '0987645331',
            'transaction_manager_id' => 410002,
            'belongsTo' => 10003,
        ]);

        Transaction::create([
            'transactionID' => 1400,
            'transaction_name' => 'Điểm giao dịch Cái Răng',
            'transaction_address' => '23 Võ Nguyên Giáp, P.Phú Thứ, Q.Cái Răng, Cần Thơ',
            'transaction_phone' => '0987645340',
            'transaction_manager_id' => 510001,
            'belongsTo' => 10004,
        ]);

        Transaction::create([
            'transactionID' => 1401,
            'transaction_name' => 'Điểm giao dịch Bình Thuỷ',
            'transaction_address' => '23 CMT8, P.An Thới, Q. Bình Thủy, Cần Thơ',
            'transaction_phone' => '0987645341',
            'transaction_manager_id' => 510002,
            'belongsTo' => 10004,
        ]);
    }
}
