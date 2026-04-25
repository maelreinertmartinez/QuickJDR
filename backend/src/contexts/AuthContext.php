<?php

namespace QuickJDR\contexts;

class AuthContext
{
    public function __construct(
        public readonly int $userId,
        public readonly array $roles,
    ) {}

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }
}
