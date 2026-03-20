<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        abort_unless(actor_can('permissions.read'), 403);

        return PermissionResource::collection(
            Permission::query()->paginate(50)
        );
    }

    public function show(Permission $permission): PermissionResource
    {
        abort_unless(actor_can('permissions.read'), 403);

        return new PermissionResource($permission);
    }
}
