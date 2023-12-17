<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function getAllWarehouseAndTransaction(Request $request) {
        try {
            $warehouses = Warehouse::with(['transactions' => function ($query) {
                // Chọn các cột bạn quan tâm từ bảng transactions
                $query->select('transactionID', 'transaction_name', 'belongsTo');
            }])
            ->select('warehouseID', 'warehouse_name')
            ->get();
    
            // Chuyển đổi kết quả truy vấn để đáp ứng định dạng mong muốn
            $warehouses = $warehouses->map(function ($warehouse) {
                $transactions = $warehouse->transactions->map(function ($transaction) {
                    // Chỉ lấy những cột cần thiết từ mỗi transaction
                    return [
                        'transactionID' => $transaction->transactionID,
                        'transaction_name' => $transaction->transaction_name,
                    ];
                });
    
                return [
                    'warehouseID' => $warehouse->warehouseID,
                    'warehouse_name' => $warehouse->warehouse_name,
                    'transactions' => $transactions,
                ];
            });
    
            return response()->json([
                'warehouses' => $warehouses,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    
    
}
