<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        abort_unless(actor_can('users.read'), 403);

        return UserResource::collection(
            User::query()->paginate(20)
        );
    }

    public function show(User $user): UserResource
    {
        abort_unless(actor_can('users.read'), 403);

        return new UserResource($user);
    }

    public function syncRoles(Request $request, User $user): UserResource
    {
        abort_unless(actor_can('roles.assign'), 403);

        $data = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->syncRoles($data['roles']);

        return new UserResource($user->fresh());
    }

    public function syncPermissions(Request $request, User $user): UserResource
    {
        abort_unless(actor_can('permissions.assign'), 403);

        $data = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user->syncPermissions($data['permissions']);

        return new UserResource($user->fresh());
    }
}
