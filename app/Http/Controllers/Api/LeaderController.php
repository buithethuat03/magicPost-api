<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Hash;

class LeaderController extends Controller
{
    /**
     * Get all information of employee
     * @param Request $request
     * @return response
     */
    public function getAllManager(Request $request)
    {
        try {
            // Lấy thông tin của tất cả quản lý
            $users = User::whereIn('userType', ['1','2'])->get();

            $users = $users->map(function ($user) {
                // Chỉ lấy những cột cần thiết từ mỗi user
                return [
                    'userID' => $user->userID,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'userType' => $user->userType,
                    'phoneNumber' => $user->phoneNumber,
                    'belongsTo' => $user->belongsTo
                ];
            }, 200);

            return response()->json(['users' => $users]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    
    /**
     * Get list of warehouse
     */
    public function getWarehouseList(Request $request) {
        try {
            $warehouses = Warehouse::select('warehouseID', 'warehouse_name')->get();
    
            return response()->json([
                'status' => true,
                'warehouses' => $warehouses,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Get warehouse list information (id, name, address, phone, manager_name)
     */
    public function getWarehouseListInformation(Request $request) {
        
        try {
            $warehouses = Warehouse::select('warehouseID', 'warehouse_name', 'warehouse_address', 'warehouse_phone', 'warehouse_manager_id')->get();
        
            $result = [];

            foreach ($warehouses as $warehouse) {
            
                $warehouseManager = User::find($warehouse->warehouse_manager_id);
        
                if ($warehouseManager) { 
                    $result[] = [
                        'warehouseID' => $warehouse->warehouseID,
                        'warehouse_name' => $warehouse->warehouse_name,
                        'warehouse_address' => $warehouse->warehouse_address,
                        'warehouse_phone' => $warehouse->warehouse_phone,
                        'warehouse_manager_name' => $warehouseManager->fullname,
                    ];
                } else {
                    $result[] = [
                        'warehouseID' => $warehouse->warehouseID,
                        'warehouse_name' => $warehouse->warehouse_name,
                        'warehouse_address' => $warehouse->warehouse_address,
                        'warehouse_phone' => $warehouse->warehouse_phone,
                        'warehouse_manager_name' => null
                    ];
                }
            }
        
            return response()->json([
                "status" => true,
                "warehouses" => $result
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Get all information of warehouse and transaction belongs to this one (ID, tên, địa chỉ, SDT, tên trưởng điểm)
     */
    public function getWarehouseDetailInformation(Request $request) {
        try {
            if ($request->warehouseID == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request'
                ], 400);
            } else {
                $warehouse = Warehouse::find($request->warehouseID);
    
                if (!$warehouse) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Warehouse not found'
                    ], 404);
                }
    
                $warehouseManager = User::find($warehouse->warehouse_manager_id);
    
                $transactions = Transaction::where('belongsTo', $request->warehouseID)->get();
    
                $formattedTransactions = [];
                foreach ($transactions as $transaction) {
                    $transactionManager = User::find($transaction->transaction_manager_id);
                    $formattedTransactions[] = [
                        'transactionID' => $transaction->transactionID,
                        'transaction_name' => $transaction->transaction_name,
                        'transaction_address' => $transaction->transaction_address,
                        'transaction_phone' => $transaction->transaction_phone,
                        'transaction_manager' => [
                            'fullname' => $transactionManager->fullname,
                        ],
                    ];
                }
    
                $warehouseDetail = [
                    'warehouseID' => $warehouse->warehouseID,
                    'warehouse_name' => $warehouse->warehouse_name,
                    'warehouse_address' => $warehouse->warehouse_address,
                    'warehouse_phone' => $warehouse->warehouse_phone,
                    'warehouse_manager' => [
                        'fullname' => $warehouseManager->fullname,
                    ],
                    'transactions' => $formattedTransactions,
                ];
    
                return response()->json([
                    'status' => true,
                    'warehouse' => $warehouseDetail,
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * View detail information of warehouse or transaction
     */
    public function viewPointDetailInformation(Request $request) {
        try {
            if ($request->type == 'warehouse') {
                if ($request->warehouseID == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bad request'
                    ], 400);
                }
                $warehouse = Warehouse::find($request->warehouseID);
                if ($warehouse == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Warehouse not found'
                    ], 404);
                }
                $manager = User::find($warehouse->warehouse_manager_id);
                
                $formattedManager = [
                    'userID' => $manager->userID,
                    'fullname' => $manager->fullname,
                    'email' => $manager->email,
                    'phoneNumber' => $manager->phoneNumber,
                ];
                
                $formattedWarehouse = [
                    'warehouseID' => $warehouse->warehouseID,
                    'warehouse_name' => $warehouse->warehouse_name,
                    'warehouse_address' => $warehouse->warehouse_address,
                    'warehouse_phone' => $warehouse->warehouse_phone,
                    'warehouse_manager' => $formattedManager
                ];
                    
                return response()->json(['warehouse' => $formattedWarehouse], 200);

            } else if ($request->type == 'transaction') {
                if ($request->transactionID == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bad request'
                    ], 400);
                }
                $transaction = Transaction::find($request->transactionID);
                if ($transaction == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Transaction not found'
                    ], 404);
                }
                $manager = User::find($transaction->transaction_manager_id);
            

                $formattedManager = [
                    'userID' => $manager->userID,
                    'fullname' => $manager->fullname,
                    'email' => $manager->email,
                    'phoneNumber' => $manager->phoneNumber,
                ];
                
                $formattedTransaction = [
                    'transactionID' => $transaction->transactionID,
                    'transaction_name' => $transaction->transaction_name,
                    'transaction_address' => $transaction->transaction_address,
                    'transaction_phone' => $transaction->transaction_phone,
                    'transaction_manager' => $formattedManager
                ];
                    
                return response()->json(['transaction' => $formattedTransaction], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Update information of manager
     * Nếu update từ nhân viên -> manager thì xóa nhân viên rồi sửa thông tin của manager
     * Nếu cố tình sửa thông tin của manager tại điểm chỉ định trong khi người này là manager của một điểm khác thì không được
     * Nếu không vào một trong hai trường hợp trên thì ok, sửa thông tin của manager tại điểm chỉ định
     */
    public function changeManagerInformation(Request $request) {
        try {
            if ($request->type == 'warehouse') {
                if ($request->warehouseID == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bad request'
                    ], 404);
                } else {
                    $warehouse = Warehouse::find($request->warehouseID);
                    $manager = User::find($warehouse->warehouse_manager_id);
                    if ($request->phoneNumber != null) {
                        $checkEmployee = User::where('phoneNumber', $request->phoneNumber)->first();
                        if($checkEmployee!= null) {
                            if ($checkEmployee->userType == '3' || $checkEmployee->userType == '4') {
                                //userType = 3 hoặc 4 là nhân viên
                                //Xóa thông tin của nhân viên trong database (trong bảng User)
                                $checkEmployee->delete();
                                if ($request->fullname != null && $request->email != null && $request->password != null) {
                                    $checkEmail = User::where('email', $request->email)->first();
                                
                                    if ($checkEmail) {
                                        return response()->json([
                                            'status' => false,
                                            'message' => 'Email is already taken',
                                        ], 400);
                                    }
                                
                                    $manager->update([
                                        'fullname' => $request->fullname,
                                        'email' => $request->email,
                                        'phoneNumber' => $request->phoneNumber,
                                        'password' => Hash::make($request->password)
                                    ]);
                                
                                    return response()->json([
                                        'status' => true,
                                        'message' => 'Change information successfully',
                                    ], 200);
                                } else {
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'Bad request'
                                    ], 400);
                                }
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'This user is manager, cannot edit'
                                ], 400);
                            }
                        } else {

                            //Nếu đây là người mới, sửa thông tin của manager trong Warehouse: request gồm có: fullname, email, phoneNumber
                            if ($request->fullname != null && $request->email != null && $request->password != null) {
                                $checkEmail = User::where('email', $request->email)->first();
                            
                                if ($checkEmail) {
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'Email is already taken',
                                    ], 400);
                                }
                            
                                $manager->update([
                                    'fullname' => $request->fullname,
                                    'email' => $request->email,
                                    'phoneNumber' => $request->phoneNumber,
                                    'password' => Hash::make($request->password),
                                ]);
                            
                                return response()->json([
                                    'status' => true,
                                    'message' => 'Change transaction manager information successfully',
                                ], 200);
                            } else {
                                return response()->json([   
                                    'status' => false,
                                    'message' => 'Bad request'
                                ], 400);
                            }
                        }
                    } else { 
                        if ($request->email != null) {
                            // Cập nhật email của trưởng điểm tập kết (Warehouse)
                            $checkEmail = User::where('email', $request->email)
                                ->where('userID', '<>', $manager->userID)
                                ->first();
                        
                            if ($checkEmail == null) {
                                $manager->update(['email' => $request->email]);
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Email is already taken'
                                ], 400);
                            }
                        }
                        if ($request->fullname != null) {
                            //Cập nhật tên của trưởng điểm tập kết (Warehouse)
                            $manager->update(['fullname' => $request->fullname]);
                        }
                        return response()->json([
                            'status' => true,
                            'message' => 'Change information successfully'
                        ], 200);
                    }
                }
            } else if ($request->type == 'transaction') {
                if ($request->transactionID == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bad request'
                    ], 404);
                } else {

                    $transaction = Transaction::find($request->transactionID);
                    $manager = User::find($transaction->transaction_manager_id);

                    if ($request->phoneNumber != null) {

                        $checkEmployee = User::where('phoneNumber', $request->phoneNumber)->first();

                        if($checkEmployee!= null) {

                            if ($checkEmployee->userType == '3' || $checkEmployee->userType == '4') {

                                //userType = 3 hoặc 4 là nhân viên
                                //Xóa thông tin của nhân viên trong database (trong bảng User)
                                $checkEmployee->delete();

                                if ($request->fullname != null && $request->email != null && $request->password != null) {
                                    $checkEmail = User::where('email', $request->email)->first();
                                
                                    if ($checkEmail) {
                                        return response()->json([
                                            'status' => false,
                                            'message' => 'Email is already taken',
                                        ], 400);
                                    }
                                
                                    $manager->update([
                                        'fullname' => $request->fullname,
                                        'email' => $request->email,
                                        'phoneNumber' => $request->phoneNumber,
                                        'password' => Hash::make($request->password)
                                    ]);
                                
                                    return response()->json([
                                        'status' => true,
                                        'message' => 'Change information successfully',
                                    ], 200);
                                } else {

                                    return response()->json([
                                        'status' => false,
                                        'message' => 'Bad request'
                                    ], 400);

                                }
                            } else {

                                return response()->json([
                                    'status' => false,
                                    'message' => 'This user is manager, cannot edit'
                                ], 400);

                            }
                        } else {
                            //Nếu đây là người mới, sửa thông tin của manager trong Warehouse: request gồm có: fullname, email, phoneNumber
                            if ($request->fullname != null && $request->email != null && $request->password != null) {
                                $checkEmail = User::where('email', $request->email)->first();
                            
                                if ($checkEmail) {
                                    return response()->json([
                                        'status' => false,
                                        'message' => 'Email is already taken',
                                    ], 400);
                                }
                            
                                $manager->update([
                                    'fullname' => $request->fullname,
                                    'email' => $request->email,
                                    'phoneNumber' => $request->phoneNumber,
                                    'password' => Hash::make($request->password),
                                ]);
                            
                                return response()->json([
                                    'status' => true,
                                    'message' => 'Change information successfully',
                                ], 200);
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Bad request'
                                ], 400);
                            }
                        }
                    } else { 
                        if ($request->email != null) {
                            // Cập nhật email của trưởng điểm giao dịch (Transaction)
                            $checkEmail = User::where('email', $request->email)
                                ->where('userID', '<>', $manager->userID)
                                ->first();
                        
                            if ($checkEmail == null) {
                                $manager->update(['email' => $request->email]);
                            } else {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Email is already taken'
                                ], 400);
                            }
                        }
                        if ($request->fullname != null) {
                            //Cập nhật tên của trưởng điểm giao dịch (Transaction)
                            $manager->update(['fullname' => $request->fullname]);
                        }
                        return response()->json([
                            'status' => true,
                            'message' => 'Change information successfully'
                        ], 200);
                    }
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    

    /**
     * Update information of points
     */
    public function changePointInformation(Request $request) {
        if ($request->type == 'warehouse') {
            if ($request->warehouseID == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request'
                ], 400);
            }

            $warehouse = Warehouse::find($request->warehouseID);

            if ($warehouse == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Warehouse not found'
                ], 404);
            }

            if ($request->warehouse_address == null && $request->warehouse_phone == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nothing to change'
                ], 400);
            }

            if ($request->warehouse_address != null) {
                $warehouse->warehouse_address = $request->warehouse_address;
            }

            if ($request->warehouse_phone != null) {
                $checkPhoneWarehouse = Warehouse::where('warehouse_phone', $request->warehouse_phone)
                    ->where('warehouseID', '<>', $request->warehouseID)
                    ->exists();

                $checkPhoneTransaction = Transaction::where('transaction_phone', $request->warehouse_phone)->exists();
                if ($checkPhoneWarehouse || $checkPhoneTransaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This phone has already been taken'
                    ], 409);
                } else {
                    $warehouse->warehouse_phone = $request->warehouse_phone;
                }
            }

            $warehouse->save();
            return response()->json([
                "status" => true,
                "message" => "Change information successfully"
            ], 200);

        } else if ($request->type == 'transaction') {
            $transaction = Transaction::find($request->transactionID);

            if ($transaction == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            if ($request->transaction_address == null && $request->transaction_phone == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Nothing to change'
                ], 400);
            }

            if ($request->transaction_address != null) {
                $transaction->transaction_address = $request->transaction_address;
            }

            if ($request->transaction_phone != null) {
                $checkPhoneTransaction = Transaction::where('transaction_phone', $request->transaction_phone)
                    ->where('transactionID', '<>', $request->transactionID)
                    ->exists();

                $checkPhoneWarehouse = Warehouse::where('warehouse_phone', $request->transaction_phone)->exists();
                if ($checkPhoneWarehouse || $checkPhoneTransaction) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This phone has already been taken'
                    ], 409);
                } else {
                    $transaction->transaction_phone = $request->transaction_phone;
                }
            }

            $transaction->save();

            return response()->json([
                "status" => true,
                "message" => "Change information successfully"
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Bad request'
            ], 400);
        }
    }


    /**
     * Xem thông tin của cá nhân dựa trên số điện thoại được cung cấp
     * 
     */
    public function viewEmployeeInformationViaPhoneNumber(Request $request) {
        if ($request->has('phoneNumber')) {
            $phoneNumber = $request->phoneNumber;
    
            // Kiểm tra số điện thoại hợp lệ
            if (preg_match('/^\d{10}$/', $phoneNumber)) {
                $employee = User::where('phoneNumber', $phoneNumber)->first();
    
                if ($employee) {
                    if ($employee->userType == 0) {
                        return response()->json([
                            'status' => true,
                            'userType' => $employee->userType,
                            'message' => 'This is leader'
                        ], 200);
                    } else if ($employee->userType == '1') {
                        $warehouse = Warehouse::where('warehouse_manager_id', $employee->userID)->first();
                        return response()->json([
                            'status' => true,
                            'fullname' => $employee->fullname,
                            'userType' => $employee->userType,
                            'phoneNumber' => $employee->phoneNumber,
                            'warehouse_name' => $warehouse->warehouse_name,
                        ], 200);
                    } else if ($employee->userType == '2') {
                        $transaction = Transaction::where('transaction_manager_id', $employee->userID)->first();
                        $warehouse = Warehouse::where('warehouseID', $transaction->belongsTo)->first();
                        return response()->json([
                            'status' => true,
                            'fullname' => $employee->fullname,
                            'userType' => $employee->userType,
                            'phoneNumber' => $employee->phoneNumber,
                            'transaction_name' => $transaction->transaction_name,
                            'warehouse_name' => $warehouse->warehouse_name,
                        ], 200);
                    } else if ($employee->userType == '3'){
                        $warehouse = Warehouse::where('warehouseID', $employee->belongsTo)->first();
                        return response()->json([
                            'status' => true,
                            'fullname' => $employee->fullname,
                            'userType' => $employee->userType,
                            'phoneNumber' => $employee->phoneNumber,
                            'warehouse_name' => $warehouse->warehouse_name,
                        ], 200);
                    } else if ($employee->userType == '4') {
                        $transaction = Transaction::where('transactionID', $employee->belongsTo)->first();
                        $warehouse = Warehouse::where('warehouseID', $transaction->belongsTo)->first();
                        return response()->json([
                            'status' => true,
                            'fullname' => $employee->fullname,
                            'userType' => $employee->userType,
                            'phoneNumber' => $employee->phoneNumber,
                            'transaction_name' => $transaction->transaction_name,
                            'warehouse_name' => $warehouse->warehouse_name,
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Employee not found with this phone number'
                    ], 404);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid phone number format'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Phone number is required'
            ], 400);
        }
    }
    
    
    /**
     * Trả về số lượng hàng đã chuyển thành công, đang vận chuyển, vận chuyển thất bại của toàn hệ thống điểm
     * Request  header chứa authentication token bearer của leader (có nghĩa là chỉ leader dùng được api này)
     *          body có hai trường hợp: 1 là không có gì (thì response sẽ tính tổng số lượng từ trước đến nay)
     *                                  2 là có dạng    {
     *                                                      "from" : "2023-01-01",
     *                                                      "to"   : "2023-31-01
     *                                                  }
     *                                  thì sẽ tính số lượng từ ngày from đến ngày to
     * 
     * Response trả về dạng json như sau: 
     * Các trường hợp bình thường
     * {
     *      "status" : true,
     *      "received" : x
     *      "completed" : y,
     *      "failed" : z
     *      "revenue" : t (khách hàng trả tiền trước khi gửi hàng, do đó chỉ cần cộng tổng các shipping_fee của order_details là được).
     * }
     * 
     * Các trường hợp bị lỗi trong request
     * {
     *      "status" : false,
     *      "message" : "bad request"
     * }
     * 
     */
    public function getOrderStatistics(Request $request) {
        /**
         * Logic thống kê số lượng đơn hàng
         * Đã xử lý trong middleware và trong routes, chỉ cần viết nội dung hàm này thôi.
         */
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
                $receivedOrders = OrderDetail::whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                    ->count();

                $completedOrders = OrderDetail::whereRaw("json_unquote(json_extract(timeline, '\$[9]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) <= ?", [$request->to])
                    ->count();

                $failedOrders = OrderDetail::whereRaw("json_unquote(json_extract(timeline, '\$[10]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) <= ?", [$request->to])
                    ->count();

                $revenue = OrderDetail::whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                    ->sum('shipping_fee');

                return response()->json([
                    "status" => true,
                    "received" => $receivedOrders,
                    "completed" => $completedOrders,
                    "failed" => $failedOrders,
                    "revenue" => $revenue
                ], 200);
            }
        } else if ($request->from == null && $request->to == null){
            $receivedOrders = OrderDetail::count();
            $completeOrders = OrderDetail::where('status', 'Đã giao hàng')->count();
            $failedOrders = OrderDetail::where('status', 'Không thành công')->count();
            $revenue = OrderDetail::sum('shipping_fee');
            return response()->json([
                "status" => true,
                "received" => $receivedOrders,
                "completed" => $completeOrders,
                "failed" => $failedOrders,
                "revenue" => $revenue
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Bad request"
            ], 400);
        }
    }


    /**
     * Giống như API trên, tuy nhiên API này sẽ xét trên một điểm cụ thể (có thể là tập kết hoặc giao dịch)
     * Request header chứa authentication token bearer của leader (có nghĩa là chỉ leader dùng được api này)
     * body có hai trường hợp: 1 là {
     *                                  "type" : "transaction" (hoặc "warehouse")
     *                                  nếu là warehouse thì có thêm trường warehouseID, còn là transactionID trong trường hợp còn lại
     *                              }
     *                              thì response sẽ tính tổng số lượng từ trước đến nay.
     * 
     *                                  2 là có dạng    {
     *                                                      "type" : "transaction" (hoặc "warehouse")
     *                                                      nếu là warehouse thì có thêm trường warehouseID, còn là transactionID trong trường hợp còn lại
     *                                                      "from" : "2023-01-01",
     *                                                      "to"   : "2023-31-01
     *                                                  }
     *                                  thì sẽ tính số lượng từ ngày from đến ngày to
     * 
     * Response trả về
     *      Nếu request là transaction
     *      {
     *          "status": true,
     *          "received": x,
     *          "completed": y,
     *          "failed": z,
     *          "revenue": t
     *      }
     * 
     *      Nếu request là warehouse
     *      {
     *          "status": true,
     *          "incoming": x,
     *          "outgoing": y,
     *      }
     */
    public function getOrderStatisticsByLocation(Request $request) {
        /**
         * Logic thống kê số lượng đơn hàng trên một địa điểm cụ thể
         * Đã xử lý trong middleware và trong routes, chỉ cần viết nội dung hàm này thôi.
         */
        if ($request->type == 'transaction') {

            if ($request->transactionID == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request',
                ], 400);
            }

            $checkTransaction = Transaction::find($request->transactionID);
            if ($checkTransaction == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found'
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
                    // Lấy số lượng đơn hàng có ngày gửi trong khoảng thời gian từ from đến to
                    $receivedOrders1 = OrderDetail::where('first_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                        ->count();
                    
                    $receivedOrders2 = OrderDetail::where('last_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) <= ?", [$request->to])
                        ->count();

                    $receivedOrders = $receivedOrders1 + $receivedOrders2;
                
                    $completedOrders = OrderDetail::where('last_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[9]')) <= ?", [$request->to])
                        ->count();
                    
                    $failedOrders = OrderDetail::where('last_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[10]')) <= ?", [$request->to])
                        ->count();
                    
                    $revenue = OrderDetail::where('first_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                        ->sum('shipping_fee');
                    
                    return response()->json([
                        "status" => true,
                        "received" => $receivedOrders,
                        "completed" => $completedOrders,
                        "failed" => $failedOrders,
                        "revenue" => $revenue
                    ], 200);
                }
            } else if ($request->from == null && $request->to == null){
                    
                    $receivedOrders1 = OrderDetail::where('first_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[0]')) <= ?", [$request->to])
                        ->count();
                    
                    $receivedOrders2 = OrderDetail::where('last_transaction_id', $request->transactionID)
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) >= ?", [$request->from])
                        ->whereRaw("json_unquote(json_extract(timeline, '\$[7]')) <= ?", [$request->to])
                        ->count();

                    $receivedOrders = $receivedOrders1 + $receivedOrders2;
                    
                    $completedOrders = OrderDetail::where('last_transaction_id', $request->transactionID)
                    ->where('status', 'Đã giao hàng')
                    ->count();

                    $failedOrders = OrderDetail::where('last_transaction_id', $request->transactionID)
                    ->where('status', 'Không thành công')
                    ->count();

                    $revenue = OrderDetail::where('first_transaction_id', $request->transactionID)
                    ->sum('shipping_fee');
                
                    return response()->json([
                    "status" => true,
                    "received" => $receivedOrders,
                    "completed" => $completedOrders,
                    "failed" => $failedOrders,
                    "revenue" => $revenue
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Bad request"
                ], 404);
            }
        } else if ($request->type == 'warehouse') {
            if ($request->warehouseID == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bad request',
                ], 400);
            }

            $checkWarehouse = Warehouse::find($request->warehouseID);
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
                    $incoming1 = OrderDetail::where('first_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[3]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[3]')) <= ?", [$request->to])
                    ->count();

                    $incoming2 = OrderDetail::where('last_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[5]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[5]')) <= ?", [$request->to])
                    ->count();

                    $incoming = $incoming1 + $incoming2;
                    
                    $outgoing1 = OrderDetail::where('first_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[4]')) >= ?", [$request->from])
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[4]')) <= ?", [$request->to])
                    ->count();

                    $outgoing2 = OrderDetail::where('last_warehouse_id', $request->warehouseID)
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
                    
                    $incoming1 = OrderDetail::where('first_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[3]')) <= ?", [now()])
                    ->count();

                    $incoming2 = OrderDetail::where('last_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[5]')) <= ?", [now()])
                    ->count();

                    $incoming = $incoming1 + $incoming2;

                    $outgoing1 = OrderDetail::where('first_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[4]')) <= ?", [now()])
                    ->count();

                    $outgoing2 = OrderDetail::where('last_warehouse_id', $request->warehouseID)
                    ->whereRaw("json_unquote(json_extract(timeline, '\$[6]')) <= ?", [now()])
                    ->count();

                    $outgoing = $outgoing1 + $outgoing2;
                    return response()->json([
                        'status' => true,
                        'incoming' => $incoming,
                        'outgoing' => $outgoing 
                    ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Bad request"
                ], 400);
            }
        } else {
            return response()->json([
                "status" => false,
                "message" => "Bad request"
            ], 400);
        }
    }

}