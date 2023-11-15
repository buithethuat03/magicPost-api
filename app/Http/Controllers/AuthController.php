<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'error_account'], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'email'=> 'required|unique:users',
            'password'=> 'required',
            'fullname'=> 'required',  
        ]);

        $user = User::create($request->all());

        return response()->json(['success' => true]);
    }
}