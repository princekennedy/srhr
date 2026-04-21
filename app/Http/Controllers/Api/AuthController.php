<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'email_verified_at' => now(),
        ]);

        if (Schema::hasTable('roles')) {
            $role = \Spatie\Permission\Models\Role::query()
                ->where('name', 'user')
                ->where('guard_name', 'web')
                ->first();

            if ($role !== null) {
                $user->assignRole($role);
            }
        }

        $token = $user->createToken('android')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'user' => $this->userPayload($user),
                'token' => $token,
                'access_token' => $token,
                'plainTextToken' => $token,
                'token_type' => 'Bearer',
                'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            ],
            'meta' => [],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $payload['email'])->first();

        if ($user === null || ! Hash::check($payload['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
                'data' => [],
                'meta' => [],
            ], 422);
        }

        $token = $user->createToken('android')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => $this->userPayload($user),
                'token' => $token,
                'access_token' => $token,
                'plainTextToken' => $token,
                'token_type' => 'Bearer',
                'permissions' => $user->getAllPermissions()->pluck('name')->values(),
            ],
            'meta' => [],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful.',
            'data' => [],
            'meta' => [],
        ]);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames()->values(),
        ];
    }
}