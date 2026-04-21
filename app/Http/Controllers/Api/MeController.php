<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profile loaded successfully.',
            'data' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
                'roles' => $user?->getRoleNames()->values() ?? [],
                'permissions' => $user?->getAllPermissions()->pluck('name')->values() ?? [],
            ],
            'meta' => [],
        ]);
    }

    public function permissions(Request $request): JsonResponse
    {
        $user = $request->user();

        $permissions = $user?->getAllPermissions()->pluck('name')->values() ?? [];

        return response()->json([
            'success' => true,
            'message' => 'Permissions loaded successfully.',
            'data' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
                'roles' => $user?->getRoleNames()->values() ?? [],
                'permissions' => $permissions,
            ],
            'permissions' => $permissions,
            'meta' => [],
        ]);
    }
}