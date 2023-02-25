<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        DB::beginTransaction();

        try {
            // get admin object
            $admin = Admin::where('email', request()->email)->first();

            // do the passwords match?
            if (!Hash::check(request()->password, $admin->password)) {
                // no they don't
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // log the admin in (needed for future requests)
            FacadesAuth::login($admin);

            if (!$admin->api_token) {
                $admin->api_token = Str::random(80);
            }

            $admin->save();

            DB::commit();

            // return token in json response
            return response()->json([
                'code' => 200,
                'message' => 'Logged in!',
                'validation' => null,
                'data' => [
                    'admin' => new AdminResource($admin),
                ],
            ]);
        } catch (\Exception$e) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);
        }

    }

    public function logout()
    {
        DB::beginTransaction();

        try {
            $admin = auth('api')->user();

            $admin->update(['api_token' => null]);

            DB::commit();

            // return token in json response
            return response()->json([
                'code' => 200,
                'message' => 'Logged out!',
                'validation' => null,
                'data' => [],
            ]);
        } catch (\Exception$e) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);
        }

    }
}