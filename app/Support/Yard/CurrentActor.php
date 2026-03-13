<?php

namespace App\Support\Yard;

class CurrentActor
{
    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $name,
        public readonly string $status,
        public readonly array $memberships = [],
        public readonly array $scopes = [],
        public readonly ?string $activeOrganisationId = null,
    ) {
    }

    public static function fromShunt(array $data, array $scopes = [], ?string $activeOrganisationId = null): self
    {
        return new self(
            userId: $data['id'],
            email: $data['email'],
            name: $data['name'],
            status: $data['status'],
            memberships: $data['memberships'] ?? [],
            scopes: $scopes,
            activeOrganisationId: $activeOrganisationId,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
            'name' => $this->name,
            'status' => $this->status,
            'memberships' => $this->memberships,
            'scopes' => $this->scopes,
            'active_organisation_id' => $this->activeOrganisationId,
        ];
    }
}
