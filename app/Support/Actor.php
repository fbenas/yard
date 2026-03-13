<?php

namespace App\Support;

class Actor
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $name,
        public readonly string $status,
        public readonly array $memberships = [],
        public readonly array $scopes = [],
    ) {}

    public static function fromShunt(array $data, array $scopes = []): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
            name: $data['name'] ?? '',
            status: $data['status'] ?? 'active',
            memberships: $data['memberships'] ?? [],
            scopes: $scopes,
        );
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    public function organisations(): array
    {
        return array_map(
            fn ($m) => $m['organisation']['id'],
            $this->memberships
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'status' => $this->status,
            'memberships' => $this->memberships,
            'scopes' => $this->scopes,
        ];
    }
}
