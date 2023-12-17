<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {
        // try {
        //     //Validated
        //     $validateUser = Validator::make($request->all(), 
        //     [
        //         'fullname' => 'required',
        //         'phoneNumber' => 'required|unique:users,phoneNumber',
        //         'email' => 'required|email|unique:users,email',
        //         'password' => ['required', Password::defaults()],
        //         'userType' => 'required',
        //         'belongsTo' => 'sometimes'
        //     ]);

        //     if($validateUser->fails()){
        //         return response()->json([
        //             'status' => false,
        //             'message' => 'Validation error',
        //             'errors' => $validateUser->errors()
        //         ], 401);
        //     }

        //     $userData = [
        //         //userID increment?
        //         'fullname' => $request->fullname,
        //         'phoneNumber' => $request->phoneNumber,
        //         'email' => $request->email,
        //         'password' => Hash::make($request->password),
        //         'userType' => $request->userType,
        //     ];

        //     if ($request->belongsTo !== null) {
        //         $userData['belongsTo'] = $request->belongsTo;
        //     }
            
        //     $user = User::create($userData);

        //     return response()->json([
        //         'status' => true,
        //         'message' => 'User created successfully',
        //         //'token' => $user->createToken("API TOKEN")->plainTextToken
        //     ], 201);

        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $th->getMessage()
        //     ], 500);
        // }
    }

    /**
     * Login The User
     * @param Request $request
     * @return response
     */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'phoneNumber' => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['phoneNumber', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Wrong phone number or password',
                ], 401);
            }
            
            $user = User::where('phoneNumber', $request->phoneNumber)->first();
            
            if ($user->userType == '-1') {
                return response()->json([
                    'status' => false,
                    'message' => 'This account have been banned',
                ], 401);
            }

            $user->tokens()->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                'fullname' => $user->fullname,
                'userType' => $user->userType,
                'token' => $user->createToken("API TOKEN")->plainTextToken,
                'token_type' => 'Bearer',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
        

    /**
     * Logout user
     * @param Request $request
     * @return response
     */
    public function logoutUser(Request $request)
    {
        try {
            if ($token = $request->bearerToken()) {
                $model = Sanctum::$personalAccessTokenModel;
                $accessToken = $model::findToken($token);
            
                if ($accessToken && $accessToken->tokenable_id == $request->user()->userID) {
                    // Lấy userID của token yêu cầu và so sánh với userID của người dùng đăng nhập
                    $userTokens = $model::where('tokenable_id', $request->user()->userID)
                        ->where('tokenable_type', get_class($request->user()))
                        ->get();
            
                    // Lặp qua tất cả các token của người dùng và xóa chúng
                    foreach ($userTokens as $userToken) {
                        $userToken->delete();
                    }
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => 'User logout successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    

    
    /**
     * View information 
     * @param Request $request
     * @return response
     */
    public function viewInformation(Request $request) {
        try {
            // Sử dụng $request->user() để lấy thông tin người dùng đăng nhập
            $user = $request->user();
        
            if ($user) {
                // Chỉ lấy những cột cần thiết từ user
                $formattedUser = [
                    'userID' => $user->userID,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'userType' => $user->userType,
                    'phoneNumber' => $user->phoneNumber,
                    'belongsTo' => $user->belongsTo
                ];
        
                return response()->json(['user' => $formattedUser]);
            } else {
                // Trường hợp không tìm thấy user
                return response()->json(['error' => 'Bad user infomation.'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Change user's email infomation
     * @param Request $request
     * @return response
     * Request
     * {
     *     "email": email
     * }
     * 
     * Response 
     * - Nếu email bị trùng, trả về 
     * {
     *      "status": false,
     *      "message": "This email has already taken"
     * }, STATUS 409
     * 
     * - Nếu cập nhật thành công, trả về
     * {
     *      "status": true,
     *      "message": "change user's email successfully"
     * }, STATUS 200
     */
    public function changeUserEmail(Request $request) {
        // Kiểm tra định dạng email
        $validatedData = Validator::make($request->all(), 
            [
                //'email' => 'required|email|unique:users,email',
                //'email' => 'required|email'
                'email' => [
                    'required',
                    'email',
                    function ($attribute, $value, $fail) {
                        // Kiểm tra xem email có chứa ký tự nào khác chữ cái tiếng Anh không
                        if (!preg_match('/^[A-Za-z0-9_.-]+$/', $value)) {
                            $fail('Invalid email');
                        }
                    },
                ],
            ]);

            if($validatedData->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validatedData->errors()
                ], 401);
            }
        // Kiểm tra tính duy nhất của email trong cơ sở dữ liệu
        $user = $request->user();
        $checkEmail = User::where('email', $request->email)
            ->where('userID', '<>', $user->userID) // Loại trừ người dùng hiện tại
            ->exists();
    
        if ($checkEmail) {
            // Trả về lỗi nếu email đã tồn tại trong hệ thống
            return response()->json([
                'status' => false,
                'message' => 'This email has already been taken'
            ], 409);
        }
    
        // Cập nhật email nếu không có vấn đề
        $user->email = $request->email;
        $user->save();
    
        // Trả về thông báo thành công
        return response()->json([
            'status' => true,
            'message' => 'Change user\'s email successfully'
        ], 200);
    }


    /**
     * Change user's password
     * @param Request $request
     * @return response
     * 
     * Request
     * {
     *      "currentPassword": currentPassword,
     *      "newPssword": newPassword
     * }
     * 
     * Response
     * - Nếu cập nhật thành công, trả về
     * {
     *      "status": true,
     *      "message": "change user's password successfully"
     * }, STATUS 200
     * 
     * - Nếu mật khẩu cũ không trùng với mật khẩu được lưu trong database, trả về
     * {
     *      "status": false,
     *      "message": "bad current password"
     * }, STATUS 401
     */
    public function changeUserPassword(Request $request) {
        $validatePassword = Validator::make($request->all(), 
            [
                'currentPassword' => 'required',
                'newPassword' => [
                    'required',
                    'min:8',
                    'regex:/^[a-zA-Z0-9]+$/',
                ],
            ]);

            if($validatePassword->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validatePassword->errors()
                ], 401);
            }
        $user = $request->user();
    
        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->currentPassword, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Bad current password'
            ], 401);
        }
    
        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->newPassword);
        $user->save();
    
        if ($token = $request->bearerToken()) {
            $model = Sanctum::$personalAccessTokenModel;
            $accessToken = $model::findToken($token);
        
            if ($accessToken && $accessToken->tokenable_id == $request->user()->userID) {
                // Lấy userID của token yêu cầu và so sánh với userID của người dùng đăng nhập
                $userTokens = $model::where('tokenable_id', $request->user()->userID)
                    ->where('tokenable_type', get_class($request->user()))
                    ->get();
        
                // Lặp qua tất cả các token của người dùng và xóa chúng
                foreach ($userTokens as $userToken) {
                    $userToken->delete();
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Change user\'s password successfully"
        ], 200);
    }


    /**
     * Return userType based on API token
     * @param Request $request
     * @return response
     */
    public function checkPermission(Request $request) {
        try {
            $user = $request->user();
        return response()->json([
            "status" => true,
            'userType' => $user->userType
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}   
