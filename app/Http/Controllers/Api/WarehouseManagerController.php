<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\OrderDetail;

class WarehouseManagerController extends Controller
{
    /**
     * Trả về thông tin của nhân viên tại điểm tập kết do mình quản lý
     * Request: không có body
     * Response:
     * {
     *      "employees":[
     *          {
     *              "userID": userID,
     *              "fullname": fullname,
     *              "email": email,
     *              "phoneNumber": phoneNumber
     *          },
     *          {
     *              tương tự
     *          },...
     *      ]
     * }, STATUS 200
     */
    public function getAllEmployees(Request $request) {
        try {
            $employees = User::where('belongsTo', $request->user()->belongsTo)
                ->where('userType', '3') // Chỉ lấy nhân viên thuộc loại '3' (warehouse's employee)
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

    /**
     * Tạo một tài khoản nhân viên mới
     * Request: 
     * {
     *      "fullname": fullname,
     *      "email": email,
     *      "phoneNumber": phoneNumber,
     *      "password": password
     * }
     * 
     * Logic & response: xem phần [UserController::class, 'createUser']
     */
    public function createEmployee(Request $request)
    {
        try {
            //Validated
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
                'userType' => '3',
                'belongsTo' => $request->user()->belongsTo,
            ];

            $user = User::create($userData);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the user.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa một nhân viên 
     * Request:
     * {
     *      "userID": userID
     * }
     * 
     * Logic & response: 
     * - Nếu nhân viên không thuộc quyền quản lý của người này
     * {
     *      "status": false,
     *      "message": "this account do not have permission"
     * }, STATUS 403
     * 
     * - Nếu không tìm được nhân viên ứng với userID mà request cung cấp
     * {
     *      "status": false,
     *      "message": "employee not found"
     * }, STATUS 404
     * 
     * - Nếu xóa nhân viên thành công
     * {
     *      "status": true,
     *      "message": "delete employee successfully"
     * }, STATUS 200
     */
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

        if ($employee->userType != 3 || $employee->belongsTo != $request->user()->belongsTo) {
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

    /**
     * Sửa thông tin nhân viên (email và số điện thoại không được trùng với bản ghi đã lưu trong database)
     * Request:
     * {
     *      "userID": userID,
     *      "fullname": fullname, có thể trống
     *      "phoneNumber": phoneNumber, có thể trống
     *      "email": email, có thể trống
     * }
     * 
     * Logic & response:
     *  - Nếu nhân viên không thuộc quyền quản lí của người này => trả về lỗi không được phép, 403
     *  - Nếu số điện thoại hoặc email đã tồn tại => trả về lỗi đã tồn tại, 409
     *  - Nếu cập nhật thành công => trả về thành công, 200
     */
    public function updateEmployee(Request $request) {
        //Không dùng hàm này nữa
    }


    /**
     * Thống kê số lượng hàng đi, đến trên điểm tập kết do mình quản lí
     * Tham khảo [LeaderController::class, 'GetOrderStatisticByLocation']
     * Nếu người này đang cố tình xem của điểm tập kết/giao dịch không do mình quản lý thì trả về mã lỗi 403
     */
    public function GetOrderStatistic(Request $request) {
        $warehouseID = $request->user()->belongsTo;
        $checkWarehouse = Warehouse::find($warehouseID);
        if ($checkWarehouse == null) {
            return response()->json([
                'status' => false,
                'message' => 'Warehouse not found'
            ], 404);
        }
        if ($request->from != null && $request->to != null) {
            $fromTimestamp = strtotime($request->from);
            $toTimestamp = strtotime($request->to);

            if ($fromTimestamp > $toTimestamp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request',
                ], 400);
            } else {
                $incoming1 = OrderDetail::where('first_warehouse_id',$warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[3]')) >= ?", [$request->from])
                ->whereRaw("json_unquote(json_extract(timeline, '\$[3]')) <= ?", [$request->to])
                ->count();

                $incoming2 = OrderDetail::where('last_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[5]')) >= ?", [$request->from])
                ->whereRaw("json_unquote(json_extract(timeline, '\$[5]')) <= ?", [$request->to])
                ->count();

                $incoming = $incoming1 + $incoming2;
                
                $outgoing1 = OrderDetail::where('first_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[4]')) >= ?", [$request->from])
                ->whereRaw("json_unquote(json_extract(timeline, '\$[4]')) <= ?", [$request->to])
                ->count();

                $outgoing2 = OrderDetail::where('last_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[6]')) >= ?", [$request->from])
                ->whereRaw("json_unquote(json_extract(timeline, '\$[6]')) <= ?", [$request->to])
                ->count();

                $outgoing = $outgoing1 + $outgoing2;
                return response() ->json([
                    'status' => true,
                    'incoming' => $incoming,
                    'outgoing' => $outgoing 
                ], 200);
            }
        } else if ($request->from == null && $request->to == null){
                
                $incoming1 = OrderDetail::where('first_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[3]')) <= ?", [now()])
                ->count();

                $incoming2 = OrderDetail::where('last_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[5]')) <= ?", [now()])
                ->count();

                $incoming = $incoming1 + $incoming2;

                $outgoing1 = OrderDetail::where('first_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[4]')) <= ?", [now()])
                ->count();

                $outgoing2 = OrderDetail::where('last_warehouse_id', $warehouseID)
                ->whereRaw("json_unquote(json_extract(timeline, '\$[6]')) <= ?", [now()])
                ->count();

                $outgoing = $outgoing1 + $outgoing2;
                return response()->json([
                    'status' => true,
                    'incoming' => $incoming,
                    'outgoing' => $outgoing 
                ], 200);
        } 
        else {
            return response()->json([
                "status" => false,
                "message" => "Bad request"
            ], 400);
        }
    } 
}
