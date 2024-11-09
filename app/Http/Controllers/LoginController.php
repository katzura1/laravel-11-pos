<?php

namespace App\Http\Controllers;

use Butschster\Head\Facades\Meta;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        Meta::prependTitle("Sign In");
        return view("pages.sign-in");
    }
}
