<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Transaction;
use App\Models\GroupOrders;
use App\Models\OrderDetail;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function getOrderDetail(Request $request) {
        try {
            // Tìm đơn hàng theo orderID
            $order = OrderDetail::find($request->orderID);
    
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $first_transaction = Transaction::find($order->first_transaction_id)->transaction_name;
            $last_transaction = Transaction::find($order->last_transaction_id)->transaction_name;
            $first_warehouse = null;
            if ($order->first_warehouse_id != null) {
                $first_warehouse = Warehouse::find($order->first_warehouse_id)->warehouse_name;
            }
            $last_warehouse = Warehouse::find($order->last_warehouse_id)->warehouse_name;
            $orderData = [
                'orderID' => $order->orderID,
                'sender_name' => $order->sender_name,
                'sender_address' => $order->sender_address,
                'sender_phone' => $order->sender_phone,
                'receiver_name' => $order->receiver_name,
                'receiver_address' => $order->receiver_address,
                'receiver_phone' => $order->receiver_phone,
                'first_transaction_name' => $first_transaction,
                'last_transaction_name' => $last_transaction,
                'first_warehouse_name' => $first_warehouse,
                'last_warehouse_name' => $last_warehouse,
                'weight' => $order->weight,
                'shipping_fee' => $order->shipping_fee,
                'orderType' => $order->orderType,
                'status' => $order->status,
                'timeline' => $order->timeline,
            ];

            // Trả về thông tin đơn hàng
            return response()->json([
                'status' => true,
                'message' => 'Order information retrieved successfully',
                'data' => $orderData,
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
