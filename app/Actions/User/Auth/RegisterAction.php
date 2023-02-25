<?php

namespace App\Actions\User\Auth;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterAction
{
    public function execute(Request $request)
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    }
}
