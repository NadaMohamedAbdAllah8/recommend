<?php

namespace App\Http\Controllers\User;

use App\Actions\User\Auth\RegisterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $register_action): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $register_action->execute($request);

            $token = $user->createToken('myapptoken')->plainTextToken;

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => 'Registered!',
                'validation' => null,
                'data' => ['user' => $user,
                    'token' => $token],
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid login details',
                ], 401);
            }
            $user = User::where('email', $request['email'])->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'code' => 200,
                'message' => 'Logged In!',
                'validation' => null,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer'],
            ]);
        } catch (\Exception $e) {
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
        try {
            auth()->user()->tokens()->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Logged out!',
                'validation' => null,
                'data' => [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error!',
                'validation' => null,
                'data' => [],
            ]);
        }
    }
}
