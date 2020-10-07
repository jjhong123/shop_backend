<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;

class UserController extends Controller
{
    /**
     * 登入
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  email  $email
     * @param  password  $password
     * @param  device_name  $device_name
     * @return \Illuminate\Http\Response
     */
    public function Login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $request["device_name"] = "desktop";

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['資料不正確'],
            ]);
        }

        $user["token"] = $user->createToken($request->device_name)->plainTextToken;
        $data["user"] = $user;
        return response()->json($data, 200);
    }

    /**
     * 註冊
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  email  $email
     * @param  name  $name
     * @param  password  $password
     * @return \Illuminate\Http\Response
     */
    public function Register(Request $request)
    {
        $user = User::Create([
            "email" => $request["email"],
            "name" => $request["name"],
            "password" => Hash::make($request["password"]),
            "roles" => "guest"
        ]);

        return response()->json($user, 200);
    }


    /**
     * 取得用戶資訊
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {

        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }
}
