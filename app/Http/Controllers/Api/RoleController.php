<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        abort_unless(actor_can('roles.read'), 403);

        return RoleResource::collection(
            Role::query()->with('permissions')->paginate(20)
        );
    }

    public function show(Role $role): RoleResource
    {
        abort_unless(actor_can('roles.read'), 403);

        return new RoleResource($role->load('permissions'));
    }

    public function store(Request $request): RoleResource
    {
        abort_unless(actor_can('roles.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return new RoleResource($role->load('permissions'));
    }

    public function update(Request $request, Role $role): RoleResource
    {
        abort_unless(actor_can('roles.update'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update([
            'name' => $data['name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return new RoleResource($role->fresh()->load('permissions'));
    }

    public function destroy(Role $role)
    {
        abort_unless(actor_can('roles.delete'), 403);

        $role->delete();

        return response()->json([
            'message' => 'Role deleted.',
        ]);
    }
}
