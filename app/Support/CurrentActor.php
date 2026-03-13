<?php

namespace App\Support;

use App\Models\User;

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
        public readonly ?User $user = null,
    ) {
    }

    public static function fromAuth(
        array $data,
        array $scopes = [],
        ?string $activeOrganisationId = null,
        ?User $user = null,
    ): self {
        return new self(
            userId: $data['id'],
            email: $data['email'],
            name: $data['name'],
            status: $data['status'],
            memberships: $data['memberships'] ?? [],
            scopes: $scopes,
            activeOrganisationId: $activeOrganisationId,
            user: $user,
        );
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    public function organisationIds(): array
    {
        return array_values(array_filter(array_map(
            fn (array $membership) => $membership['organisation']['id'] ?? null,
            $this->memberships,
        )));
    }

    public function activeOrganisation(): ?string
    {
        return $this->activeOrganisationId;
    }

    public function hasOrganisation(string $organisationId): bool
    {
        return in_array($organisationId, $this->organisationIds(), true);
    }

    public function can(string $permission): bool
    {
        return $this->user?->can($permission) ?? false;
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
