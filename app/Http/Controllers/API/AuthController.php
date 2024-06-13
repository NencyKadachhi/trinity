<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    public function registration(Request $request)
    {
        $data = $request->all();
        try {
            $rules = array(
                'username' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'password_confirm' => 'required|same:password'
            );

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {

                $messages = $validator->messages();

                return response()->json([
                    "code" => 401,
                    "message" => "something went wrong!",
                ]);
            } else {
                if (isset($data) && count($data) > 0) {
                    $add = new User();
                    $add->name = $data['username'];
                    $add->email = $data['email'];
                    $add->password = Hash::make($data['password']);
                    if ($add->save()) {
                        return response()->json([
                            "code" => 200,
                            "message" => "Registration successfully!",
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function login(Request $request)
    {
        $data = $request->all();
        try {
            $rules = array(
                'email' => 'required|email',
                'password' => 'required',
            );
            $messages = array([
                'email.required' => 'Email is required',
                'password' => 'Password is required',
            ]);

            $validator = Validator::make($data, $rules, $messages);

            if ($validator->fails()) {

                $messages = $validator->messages();
                return response()->json([
                    "code" => 401,
                    "message" => $messages,
                ]);
            } else {
                if (isset($data) && count($data) > 0) {
                    $find = User::where('email', $data['email'])->first();
                    if (!empty($find) && Hash::check($data['password'], $find->password)) {
                        $token = $find->createToken("API TOKEN")->plainTextToken;
                        Auth::login($find);
                        return response()->json([
                            "code" => 200,
                            "message" => ["data"=>Auth::user(),"token"=>$token],
                        ]);
                    } else {
                        return response()->json([
                            "code" => 401,
                            "message" => "Please enter correct password!",
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function logout(Request $request)
    {
        try {
           Auth::user()->currentAccessToken()->delete();
            return response()->json([
                "code" => 200,
                "message" => "Logout successfully",
            ]);
        } catch (\Throwable $th) {
            dD($th);
            report($th);
            return false;
        }
    }
}
