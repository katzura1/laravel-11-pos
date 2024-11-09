<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Butschster\Head\Facades\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        Meta::prependTitle("Sign In");
        return view("pages.sign-in");
    }

    public function authenticate(LoginRequest $request)
    {
        try {
            $validated = $request->safe()->only('username', 'password');
            if (Auth::attempt($validated)) {
                // Authentication passed...
                $request->session()->regenerate();

                return response()->json([
                    'status' => true,
                    'message' => 'Login success',
                ]);
            }
            throw new \Exception('Login failed, please check your username and password');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json([
            "status" => true,
            "message" => "You have been logged out",
        ]);
    }
}
