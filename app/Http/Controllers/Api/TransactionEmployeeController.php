<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;


class TransactionEmployeeController extends Controller
{

    public function getTransactionAndWarehouse(Request $request) {
        $transaction = Transaction::find($request->user()->belongsTo);
        $warehouse = Warehouse::find($transaction->belongsTo);
        return response()->json([
            'status' => true,
            'warehouseID' => $warehouse->warehouseID,
            'warehouse_name' => $warehouse->warehouse_name,
            'transactionID' => $transaction->transactionID,
            'transaction_name' => $transaction->transaction_name
        ], 200);
    }

    //timeline[0]
    public function createOrder(Request $request) {
        $validateOrder = Validator::make($request->all(),
        [
            'sender_name' => 'required',
            'sender_address' => 'required',
            'sender_phone' => 'required',
            'receiver_name' => 'required',
            'receiver_address' => 'required',
            'receiver_phone' => 'required',
            'first_transaction_id' => 'required',
            'last_transaction_id' => 'required',
            'first_warehouse_id' => 'required',
            'last_warehouse_id' => 'required',
            'confirm_time' => 'required|date',
            'weight' => 'required',
            'shipping_fee' => 'required',
            'orderType' => 'required|in:0,1',
        ]);

        if ($validateOrder->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error",
                'errors' => $validateOrder->errors()
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');

        $orderData = [
            'orderID' => Str::uuid(),
            'sender_name' => $request->sender_name,
            'sender_address' => $request->sender_address,
            'sender_phone' => $request->sender_phone,
            'receiver_name' => $request->receiver_name,
            'receiver_address' => $request->receiver_address,
            'receiver_phone' => $request->receiver_phone,
            'first_transaction_id' => $request->first_transaction_id,
            'last_transaction_id' => $request->last_transaction_id,
            'first_warehouse_id' => $request->first_warehouse_id,
            'last_warehouse_id' => $request->last_warehouse_id,
            'timeline' => [
                $confirmTime,
                null, null, null, null, null, null, null, null, null, null
            ],
            'weight' => $request->weight,
            'shipping_fee' => $request->shipping_fee,
            'orderType' => $request->orderType,
            'status' => 'Đã tiếp nhận'
        ];
        
        OrderDetail::create($orderData);

        return response()->json([
            'status' => true,
            'message' => 'Order crated successfully',
            'orderID' => $orderData['orderID']
        ], 201);
    }

    //Show orders list except completed or failed
    public function showOrdersList(Request $request) {
        $user = $request->user();
    
        //TODO: Xem lại phần này

        $orders1 = OrderDetail::where('first_transaction_id', $user->belongsTo)
            ->whereIn('status', ['Đã tiếp nhận', 'Chờ tập kết 1 đến', 'Rời giao dịch 1'])
            ->get();
    
        $orders2 = OrderDetail::where('last_transaction_id', $user->belongsTo)
            ->whereIn('status', ['Rời tập kết 2', 'Đến giao dịch 2, Đang giao hàng'])
            ->get();
    
        if ($orders1->isEmpty() && $orders2->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Orders not found'
            ], 404);
        }
    
        $orders = $orders1->merge($orders2);
    
        return response()->json([
            "status" => true,
            "orders" => $orders
        ], 200);
    }

    //timeline[1]
    public function createOrderListToWarehouse(Request $request) {
        $validateRequest = Validator::make($request->all(), [
            'ordersID' => 'required|array',
            'confirm_time' => 'required|date',
        ]);

        if ($validateRequest->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateRequest->errors(),
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');
        $badResponse = [];

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đã tiếp nhận')
                ->first();

            if (!$order) {
                $badResponse[] = "Order not found with ID {$orderID}";
            }
        }

        if (!empty($badResponse)) {
            return response()->json([
                'status' => false,
                'message' => $badResponse,
            ], 404);
        }

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đã tiếp nhận')
                ->first();

            $order->status = 'Chờ tập kết 1 đến';
            $timeline = $order->timeline;
            $timeline[1] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        
        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
        ], 200);
    }

    //timeline[2]
    public function confirmOrderToWarehouse(Request $request) {
        $validateRequest = Validator::make($request->all(), [
            'ordersID' => 'required|array',
            'confirm_time' => 'required|date',
        ]);

        if ($validateRequest->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateRequest->errors(),
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');
        $badResponse = [];

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Chờ tập kết 1 đến')
                ->first();

            if (!$order) {
                $badResponse[] = "Order not found with ID {$orderID}";
            }
        }

        if (!empty($badResponse)) {
            return response()->json([
                'status' => false,
                'message' => $badResponse,
            ], 404);
        }

        foreach ($request->ordersID as $ordersID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Chờ tập kết 1 đến')
                ->first();
            
            $order->status = 'Rời giao dịch 1';
            $timeline = $order->timeline;
            $timeline[2] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
        ], 200);
    }

    //timeline[7]
    public function confirmOrderFromWarehouse(Request $request) {
        $validateRequest = Validator::make($request->all(), [
            'ordersID' => 'required|array',
            'confirm_time' => 'required|date',
        ]);

        if ($validateRequest->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateRequest->errors(),
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');
        $badResponse = [];

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('last_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Rời tập kết 2')
                ->first();

            if (!$order) {
                $badResponse[] = "Order not found with ID {$orderID}";
            }
        }

        if (!empty($badResponse)) {
            return response()->json([
                'status' => false,
                'message' => $badResponse,
            ], 404);
        }

        foreach ($request->ordersID as $ordersID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Rời tập kết 2')
                ->first();
            
            $order->status = 'Đến giao dịch 2';
            $timeline = $order->timeline;
            $timeline[7] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
        ], 200);
    }

    //timeline[8]
    public function confirmShippingOrderToShipper(Request $request) {
        $validateRequest = Validator::make($request->all(), [
            'ordersID' => 'required|array',
            'confirm_time' => 'required|date',
        ]);

        if ($validateRequest->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateRequest->errors(),
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');
        $badResponse = [];

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('last_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đến giao dịch 2')
                ->first();

            if (!$order) {
                $badResponse[] = "Order not found with ID {$orderID}";
            }
        }

        if (!empty($badResponse)) {
            return response()->json([
                'status' => false,
                'message' => $badResponse,
            ], 404);
        }

        foreach ($request->ordersID as $ordersID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đến giao dịch 2')
                ->first();
            
            $order->status = 'Đang giao hàng';
            $timeline = $order->timeline;
            $timeline[8] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
        ], 200);
    }

    //timeline[9]
    public function confirmCompletedOrder(Request $request) {
        $validateRequest = Validator::make($request->all(), [
            'ordersID' => 'required|array',
            'confirm_time' => 'required|date',
        ]);

        if ($validateRequest->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateRequest->errors(),
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');
        $badResponse = [];

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('last_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đang giao hàng')
                ->first();

            if (!$order) {
                $badResponse[] = "Order not found with ID {$orderID}";
            }
        }

        if (!empty($badResponse)) {
            return response()->json([
                'status' => false,
                'message' => $badResponse,
            ], 404);
        }

        foreach ($request->ordersID as $ordersID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đang giao hàng')
                ->first();
            
            $order->status = 'Đã giao hàng';
            $timeline = $order->timeline;
            $timeline[9] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
        ], 200);
    }

    //timeline[10]
    public function confirmFailedOrder(Request $request) {
        $validateRequest = Validator::make($request->all(), [
            'ordersID' => 'required|array',
            'confirm_time' => 'required|date',
        ]);

        if ($validateRequest->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateRequest->errors(),
            ], 400);
        }

        $confirmTime = Carbon::parse($request->confirm_time)->format('Y-m-d H:i:s');
        $badResponse = [];

        foreach ($request->ordersID as $orderID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('last_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đang giao hàng')
                ->first();

            if (!$order) {
                $badResponse[] = "Order not found with ID {$orderID}";
            }
        }

        if (!empty($badResponse)) {
            return response()->json([
                'status' => false,
                'message' => $badResponse,
            ], 404);
        }

        foreach ($request->ordersID as $ordersID) {
            $order = OrderDetail::where('orderID', $orderID)
                ->where('first_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đang giao hàng')
                ->first();
            
            $order->status = 'Không thành công';
            $timeline = $order->timeline;
            $timeline[10] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
        ], 200);
    }

    /**Trả về số lượng đơn hàng thành công/thất bại từ trước đến nay, hoặc trong một khoảng thời gian
     * 
     */
    public function getOrderStatistic(Request $request) {
        if ($request->from != null && $request->to != null) {
            $fromTimestamp = strtotime($request->from);
            $toTimestamp = strtotime($request->to);

            if ($fromTimestamp > $toTimestamp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request',
                ], 400);
            } else {
                $completedOrders = OrderDetail::where('last_transaction_id', $request->user()->belongsTo)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) >= ?", [$request->from])
                ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) <= ?", [$request->to])
                ->count();

                $failedOrders = OrderDetail::where('last_transaction_id', $request->user()->belongsTo)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) <= ?", [$request->to])
                    ->count();

                return response()->json([
                    'status' => true,
                    'completed' => $completedOrders,
                    'failed' => $failedOrders
                ], 200);
            }
        } else if ($request->from == null && $request->to == null) {
            $completedOrders = OrderDetail::where('last_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Đã giao hàng')->count();
            $failedOrders = OrderDetail::where('last_transaction_id', $request->user()->belongsTo)
                ->where('status', 'Không thành công')->count();
            return response()->json([
                'status' => true,
                'completed' => $completedOrders,
                'failed' => $failedOrders
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Bad request'
            ], 400);
        }
    }
}
