<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\GroupOrders;
use App\Models\OrderDetail;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function getOrderDeatil(Request $request) {
        try {
            // Tìm đơn hàng theo orderID
            $order = OrderDetail::find($request->orderID);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found.'
                ], 404);
            }
            $orderData = $order->makeHidden('timeline')->toArray();
            // Trả về thông tin đơn hàng
            return response()->json([
                'status' => true,
                'data' => $orderData,
                'message' => 'Order information retrieved successfully'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

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
    
    public function getGroupOrders(Request $request) {
        $validate = Validator::make($request->all(), [
            'group_ordersID' => 'required',
        ]);
    
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error",
                'errors' => $validate->errors()
            ], 400);
        }
    
        $group = GroupOrders::find($request->group_ordersID);
    
        if (!$group) {
            return response()->json([
                'status' => false,
                'message' => "Group orders not found",
            ], 404);
        }
        $orderIDs = $group->orders;
    
        $orderDetails = OrderDetail::whereIn('orderID', $orderIDs)->get();
    
        return response()->json([
            'status' => true,
            'orderDetails' => $orderDetails,
        ], 200);
    }
    
    
}
