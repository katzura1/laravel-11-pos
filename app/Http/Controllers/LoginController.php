<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\OutletUser;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function chooseOutlet(Request $request)
    {
        Meta::prependTitle("Choose Outlet");
        $userOutlets = OutletUser::select(
            'outlet_users.outlet_id',
            'outlets.name as outlet_name',
        )
            ->join('outlets', 'outlets.id', '=', 'outlet_users.outlet_id')
            ->where('outlet_users.user_id', auth()->user()->id)
            ->get();
        return view("pages.users.choose-outlet", [
            'userOutlets' => $userOutlets,
        ]);
    }

    public function setOutlet(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'outlet_id' => 'required|exists:outlet_users,outlet_id,user_id,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                ], 500);
        }
        $outlet = Outlet::find($request->outlet_id);
        session()->put('outlet_id', $request->outlet_id);
        session()->put('outlet_name', $outlet->name);

        return response()->json([
            'status' => true,
            'message' => 'Outlet has been changed',
            ]);
    }
}
