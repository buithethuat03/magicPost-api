<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaderController;
use App\Http\Controllers\Api\WarehouseManagerController;
use App\Http\Controllers\Api\TransactionManagerController;
use App\Http\Controllers\Api\WarehouseEmployeeController;
use App\Http\Controllers\Api\TransactionEmployeeController;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::post('/register', [UserController::class, 'createUser']);
Route::post('/login', [UserController::class, 'loginUser']);
Route::get('/get_all_points', [Controller::class, 'getAllWarehouseAndTransaction']);


Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [UserController::class, 'logoutUser']);
    Route::get('/checkPermission', [UserController::class, 'checkPermission']);
    Route::get('/auth/view_information', [UserController::class, 'viewInformation']);
    Route::patch('/change_email', [UserController::class, 'changeUserEmail']);
    Route::patch('/change_password', [UserController::class, 'changeUserPassword']);
});


Route::middleware('auth:sanctum', 'checkUserType:0')->group(function () {
    Route::get('/boss/managers', [LeaderController::class, 'getAllManager']);
    Route::get('/boss/get_warehouse_list', [LeaderController::class, 'getWarehouseList']);
    Route::get('/boss/get_warehouse_list_information', [LeaderController::class, 'getWarehouseListInformation']);
    Route::get('/boss/get_warehouse_details', [LeaderController::class, 'getWarehouseDetailInformation']);
    Route::get('/boss/view_point_details', [LeaderController::class, 'viewPointDetailInformation']);
    Route::patch('/boss/change_manager_information', [LeaderController::class, 'changeManagerInformation']);
    Route::patch('/boss/change_point_information', [LeaderController::class, 'changePointInformation']);
    Route::get('/boss/get_order_statistics', [LeaderController::class, 'getOrderStatistics']);
    Route::get('/boss/get_order_statistics_by_location', [LeaderController::class, 'getOrderStatisticsByLocation']);
});



Route::middleware('auth:sanctum', 'checkUserType:0,1,2')->group(function() {
    Route::get('/leader/view_employee_information_via_number', [LeaderController::class, 'viewEmployeeInformationViaPhoneNumber']);
});


Route::middleware('auth:sanctum', 'checkUserType:1')->group(function () {
       
    Route::get('/warehouse/get_all_employees', [WarehouseManagerController::class, 'getAllEmployees']);
    Route::post('/warehouse/create_employee', [WarehouseManagerController::class, 'createEmployee']);
    Route::delete('/warehouse/delete_employee', [WarehouseManagerController::class, 'deleteEmployee']);
    Route::get('/warehouse/get_order_statistics', [WarehouseManagerController::class, 'GetOrderStatistic']);
});

Route::middleware('auth:sanctum', 'checkUserType:2')->group(function () {
    
    Route::get('/transaction/get_all_employees', [TransactionManagerController::class, 'getAllEmployees']);
    Route::post('/transaction/create_employee', [TransactionManagerController::class, 'createEmployee']);
    Route::delete('/transaction/delete_employee', [TransactionManagerController::class, 'deleteEmployee']);
    Route::get('/transaction/get_order_statistics', [TransactionManagerController::class, 'GetOrderStatistic']);
});

Route::middleware('auth:sanctum', 'checkUserType:3')->group(function() {
    Route::get('/warehouse/show_orders_list', [WarehouseEmployeeController::class, 'showOrdersList']);
    Route::patch('/confirm_order_from_transaction', [WarehouseEmployeeController::class, 'confirmOrderFromTransaction']);
    Route::patch('/confirm_order_to_transaction', [WarehouseEmployeeController::class, 'confirmOrderToTransaction']);
    Route::patch('/confirm_order_from_warehouse', [WarehouseEmployeeController::class, 'confirmOrderFromWarehouse']);
    Route::patch('/confirm_order_to_warehouse', [WarehouseEmployeeController::class, 'confirmOrderToWarehouse']);
});

Route::middleware('auth:sanctum', 'checkUserType:4')->group(function() {
    Route::get('/get_transaction_and_warehouse', [TransactionEmployeeController::class, 'getTransactionAndWarehouse']);
    Route::post('/create_order', [TransactionEmployeeController::class, 'createOrder']);
    Route::get('/transaction/show_orders_list', [TransactionEmployeeController::class, 'showOrdersList']);
    Route::patch('/create_orderlist_to_warehouse', [TransactionEmployeeController::class, 'createOrderListToWarehouse']);
    Route::patch('/confirm_order_to_warehouse', [TransactionEmployeeController::class, 'confirmOrderToWarehouse']);
    Route::patch('/confirm_order_from_warehouse', [TransactionEmployeeController::class, 'confirmOrderFromWarehouse']);
    Route::patch('/confirm_shipping_order_to_shipper', [TransactionEmployeeController::class, 'confirmShippingOrderToShipper']);
    Route::patch('/confirm_completed_order', [TransactionEmployeeController::class, 'confirmCompletedOrder']);
    Route::patch('/confirm_failed_order', [TransactionEmployeeController::class, 'confirmFailedOrder']);
    Route::get('/get_order_statistic', [TransactionEmployeeController::class, 'getOrderStatistic']);
});


