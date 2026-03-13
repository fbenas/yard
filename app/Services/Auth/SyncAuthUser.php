<?php

namespace App\Services\Auth;

use App\Models\User;

class SyncAuthUser
{
    public function handle(array $data): User
    {
        return User::query()->updateOrCreate(
            [
                'auth_user_id' => $data['id'],
            ],
            [
                'name' => $data['name'] ?? '',
                'email' => $data['email'],
                'status' => $data['status'] ?? 'active',
            ]
        );
    }
}
