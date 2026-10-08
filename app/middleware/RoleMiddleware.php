<?php
declare(strict_types=1);

final class RoleMiddleware {
    //Role => permission list RBAC (Role-Based Access Control)
    public const PERMISSIONS = [
        'President'          => ['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],
        'Vice President'     => ['admin_org','admin_events','admin_forms','admin_cbl','manage_tickets'],
        'Secretary'          => ['admin_events','admin_forms','manage_tickets'],
        'Treasurer'          => ['manage_tickets'],
        'Assistant Treasurer'=> ['manage_tickets'],
        'Auditor'            => ['manage_tickets'],
        'Assistant Auditor'  => ['manage_tickets'],
        'Institutional'      => ['admin_events'],
        'Adviser'            => ['admin_cbl','manage_tickets'],
        'Member'             => [],
    ];

    public static function permissionsFor(string $role): array {
        return self::PERMISSIONS[$role] ?? [];
    }

    public static function has(string $permission): bool {
        $role = AuthMiddleware::user()['role'] ?? 'Member';
        return in_array($permission, self::permissionsFor($role), true);
    }

    public static function require(string $permission): void
    {
        if (!self::has($permission)) {
            json_out(['ok' => false, 'error' => 'Forbidden'], 403);
        }
    }

    public static function requireAny(array $permissions): void
    {
        foreach ($permissions as $p) {
            if (self::has($p)) {
                return;
            }
        }
        json_out(['ok' => false, 'error' => 'Forbidden'], 403);
    }
}