<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TransactionManagerController extends Controller
{
    //Tương tự WarehouseManagerController::class

    public function getAllEmployees(Request $request) {
        try {
            $employees = User::where('belongsTo', $request->user()->belongsTo)
                ->where('userType', '4') // Chỉ lấy nhân viên thuộc loại '4' (warehouse's employee)
                ->get(['userID', 'fullname', 'email', 'phoneNumber']);
            return response()->json([
                'status' => true,
                'employees' => $employees
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function createEmployee(Request $request) {
        try {
            
            $validateUser = Validator::make($request->all(), [
                'fullname' => 'required',
                'phoneNumber' => 'required',
                'email' => 'required',
                'password' => 'required|string|min:8',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors(),
                ], 422);
            }

            $userData = [
                'fullname' => $request->fullname,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'userType' => '4',
                'belongsTo' => $request->user()->belongsTo,
                // 'orderID' => Str::uuid(),
                
            ];

            $user = User::create($userData);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                //'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the user.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function deleteEmployee(Request $request) {
        if ($request->userID == null) {
            return response()->json([
                'status' => false,
                'message' => 'userID required'
            ], 400);
        }

        $employee = User::find($request->userID);
        if ($employee == null) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        if ($employee->userType != 4 || $employee->belongsTo != $request->user()->belongsTo) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have permission'
            ], 401);
        }
        
        $employee->delete();
        return response()->json([
            'status' => true,
            'message' => 'Delete employee successfully'
        ], 200);
    }
    
    public function getOrderStatistic(Request $request) {
        try {    
        $belongsToValue = $request->user()->belongsTo;
        $transaction = Transaction::where('transactionID', $belongsToValue)->first();
        $transactionID = $transaction->transactionID; 

            if ($request->from != null && $request->to != null) {
                $fromTimestamp = strtotime($request->from);
                $toTimestamp = strtotime($request->to);   
                if ($fromTimestamp > $toTimestamp) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bad request',
                    ], 400);
                } else {
                    // Lấy số lượng đơn hàng có ngày gửi trong khoảng thời gian từ from đến to
                    $created = OrderDetail::where('first_transaction_id', $transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                        ->count();
                    
                    $receivedOrders = OrderDetail::where('last_transaction_id', $transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) <= ?", [$request->to])
                        ->count();

                    $sentOrders = OrderDetail::where('first_transaction_id', $transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[2]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[2]')) <= ?", [$request->to])
                        ->count();

                
                    $completedOrders = OrderDetail::where('last_transaction_id', $transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) <= ?", [$request->to])
                        ->count();
                    
                    $failedOrders = OrderDetail::where('last_transaction_id', $transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) <= ?", [$request->to])
                        ->count();
                    
                    $revenue = OrderDetail::where('first_transaction_id', $transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                        ->sum('shipping_fee');
                    $transactionID = 1;
                    return response()->json([
                        "status" => true,
                        "received" => $receivedOrders,
                        "created" => $created,
                        "sent" => $sentOrders,
                        "completed" => $completedOrders,
                        "failed" => $failedOrders,
                        "revenue" => $revenue,
                        "transactionID" => $transactionID
                    ], 200);
                }
            } else if ($request->from == null && $request->to == null){
                    
                    $created = OrderDetail::where('first_transaction_id', $transactionID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [Carbon::now()])
                        ->count();
                    
                    $receivedOrders = OrderDetail::where('last_transaction_id', $transactionID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) <= ?", [Carbon::now()])
                        ->count();

                    $sentOrders = OrderDetail::where('first_transaction_id', $transactionID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[2]')) <= ?", [Carbon::now()])
                        ->count();
                    
                    $completedOrders = OrderDetail::where('last_transaction_id', $transactionID)
                    ->where('status', 'Đã giao hàng')
                    ->count();

                    $failedOrders = OrderDetail::where('last_transaction_id', $transactionID)
                    ->where('status', 'Không thành công')
                    ->count();

                    $revenue = OrderDetail::where('first_transaction_id', $transactionID)
                    ->sum('shipping_fee');
                
                    return response()->json([
                    "status" => true,
                    "received" => $receivedOrders,
                    "created" => $created,
                    "sent" => $sentOrders,
                    "completed" => $completedOrders,
                    "failed" => $failedOrders,
                    "revenue" => $revenue
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Bad request"
                ], 400);
            }
        
        } catch (Exception $exception) {
            // Xử lý các lỗi khác
            return response()->json([
                'status' => false,
                'message' => 'Bad request ' . $exception->getMessage(),
            ], 400);
        }
    }  
    
}
