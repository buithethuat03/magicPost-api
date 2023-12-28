<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\OrderDetail;
use App\Models\GroupOrders;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WarehouseEmployeeController extends Controller
{
    public function showOrdersList(Request $request) {
        $user = $request->user();


        $orders1 = OrderDetail::where('first_warehouse_id', $user->belongsTo)
            ->whereIn('status', ['Rời giao dịch 1', 'Đến tập kết 1', 'Rời tập kết 1'])
            ->get();
    
        $orders2 = OrderDetail::where('last_warehouse_id', $user->belongsTo)
            ->whereIn('status', ['Rời tập kết 1', 'Đến tập kết 2', 'Rời tập kết 2'])
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

    //timeline[3] or [5]
    public function confirmOrderFromTransaction(Request $request) {
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
            $ordes1 = OrderDetail::where('orderID', $orderID)
                ->where('first_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Rời giao dịch 1')
                ->first();

            if (!$orders1) {
                $orders2 = OrderDetail::where('orderID', $orderID)
                    ->where('first_warehouse_id', null)
                    ->where('last_warehouse_id', $request->user()->belongsTo)
                    ->where('status', 'Rời giao dịch 1')
                    ->first();
                if (!$orders2) {
                    $badResponse[] = "Order not found with ID {$orderID}";
                }
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
                ->where('first_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Rời giao dịch 1')
                ->first();
            if (!$order) {
                $order->status = 'Đến tập kết 1';
                $timeline = $order->timeline;
                $timeline[3] = $confirmTime;
                $order->timeline = $timeline;
                $order->save();
            } else {
                $order = OrderDetail::where('orderID', $orderID)
                    ->where('first_warehouse_id', null)
                    ->where('last_warehouse_id', $request->user()->belongsTo)
                    ->where('status', 'Rời giao dịch 1')
                    ->first();
                    $order->status = 'Đến tập kết 2';
                    $timeline = $order->timeline;
                    $timeline[5] = $confirmTime;
                    $order->timeline = $timeline;
                    $order->save();
            }
            
        }

        $groupData = [
            'group_ordersID' => Str::uuid(),
            'orders' => $request->ordersID
        ];

        GroupOrders::create($groupData);
        
        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
            'group_ordersID' => $groupData['group_ordersID']
        ], 200);
    }

    //timeline[6]
    public function confirmOrderToTransaction(Request $request) {
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
                ->where('last_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Đến tập kết 2')
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
                ->where('last_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Đến tập kết 2')
                ->first();
            
            $order->status = 'Rời tập kết 2';
            $timeline = $order->timeline;
            $timeline[6] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        $groupData = [
            'group_ordersID' => Str::uuid(),
            'orders' => $request->ordersID
        ];

        GroupOrders::create($groupData);
        
        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
            'group_ordersID' => $groupData['group_ordersID']
        ], 200);
    }

    //timeline[5]
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
                ->where('last_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Rời tập kết 1')
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
                ->where('last_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Rời tập kết 1')
                ->first();
            
            $order->status = 'Đến tập kết 2';
            $timeline = $order->timeline;
            $timeline[5] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        $groupData = [
            'group_ordersID' => Str::uuid(),
            'orders' => $request->ordersID
        ];

        GroupOrders::create($groupData);
        
        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
            'group_ordersID' => $groupData['group_ordersID']
        ], 200);
    }

    //timeline[4]
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
                ->where('first_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Đến tập kết 1')
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
                ->where('first_warehouse_id', $request->user()->belongsTo)
                ->where('status', 'Đến tập kết 1')
                ->first();
            
            $order->status = 'Rời tập kết 1';
            $timeline = $order->timeline;
            $timeline[4] = $confirmTime;
            $order->timeline = $timeline;
            $order->save();
        }

        $groupData = [
            'group_ordersID' => Str::uuid(),
            'orders' => $request->ordersID
        ];

        GroupOrders::create($groupData);
        
        return response()->json([
            'status' => true,
            'message' => 'Confirm successfully',
            'group_ordersID' => $groupData['group_ordersID']
        ], 200);
    }
}
