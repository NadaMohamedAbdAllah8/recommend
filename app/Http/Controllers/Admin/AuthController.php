<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use Auth;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        if (auth()->guard('vendor')->attempt([
            'responsible_person_email' => $request->email,
            'password' => $request->password,
        ])) {

            return redirect()->route('vendor.index');

        } else {
            session()->flash('error', "Invalid credentials");

            return redirect()->route('vendor.login');
        }
    }

    public function logout()
    {
        auth()->guard('vendor')->logout();

        return redirect()->route('vendor.login');
    }
}
