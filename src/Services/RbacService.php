<?php

namespace AdminKit\Rbac\Services;

use AdminKit\Contracts\Rbac as RbacContract;
use AdminKit\Rbac\Exceptions\ForbiddenException;
use AdminKit\Rbac\Models\PermissionModel;
use AdminKit\Rbac\Models\RoleModel;

/**
 * RBAC ruoli/permessi. Implementa AdminKit\Contracts\Rbac: la sua presenza come
 * service('rbac') attiva il soft-discovery nel BaseAdminController del kit.
 *
 * Cache dei permessi per ruolo sulla cache nativa CI4 (nessuna dipendenza da
 * servizi dell'app); invalidazione tramite version key.
 */
class RbacService implements RbacContract
{
    protected RoleModel       $roleModel;
    protected PermissionModel $permissionModel;

    /** Cache in-memory per-richiesta. */
    protected array $memo = [];

    private object $cfg;

    public function __construct()
    {
        $this->roleModel       = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->cfg             = config('Rbac');
    }

    // -- Contratto ------------------------------------------------------------

    public function isSuperAdmin(): bool
    {
        return (bool) session()->get($this->cfg->superAdminSessionKey);
    }

    public function can(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $userId = $this->getCurrentUserId();

        return $userId !== null && $this->userCan($userId, $permission);
    }

    public function authorize(string $permission): void
    {
        if (! $this->isSuperAdmin() && ! $this->can($permission)) {
            throw new ForbiddenException();
        }
    }

    // -- API estesa -----------------------------------------------------------

    public function userCan(int $userId, string $permission): bool
    {
        $roleId = $this->getRoleIdForUser($userId);

        return $roleId !== null && in_array($permission, $this->getPermissionsForRole($roleId), true);
    }

    public function roleCan(string $roleSlug, string $permission): bool
    {
        $role = $this->roleModel->getBySlug($roleSlug);

        return $role !== null && in_array($permission, $this->getPermissionsForRole((int) $role->id), true);
    }

    public function getPermissionsForCurrentUser(): array
    {
        $userId = $this->getCurrentUserId();
        if ($userId === null) {
            return [];
        }
        $roleId = $this->getRoleIdForUser($userId);

        return $roleId === null ? [] : $this->getPermissionsForRole($roleId);
    }

    public function assignPermission(string $roleSlug, string $permissionSlug): bool
    {
        $role       = $this->roleModel->getBySlug($roleSlug);
        $permission = $this->permissionModel->getBySlug($permissionSlug);
        if ($role === null || $permission === null) {
            return false;
        }

        $table = db_connect()->table('role_permissions');
        $exists = $table->where('role_id', $role->id)->where('permission_id', $permission->id)->countAllResults();
        if (! $exists) {
            $table->insert(['role_id' => $role->id, 'permission_id' => $permission->id]);
            $this->clearCache();
        }

        return true;
    }

    public function revokePermission(string $roleSlug, string $permissionSlug): bool
    {
        $role       = $this->roleModel->getBySlug($roleSlug);
        $permission = $this->permissionModel->getBySlug($permissionSlug);
        if ($role === null || $permission === null) {
            return false;
        }

        db_connect()->table('role_permissions')
            ->where('role_id', $role->id)->where('permission_id', $permission->id)->delete();
        $this->clearCache();

        return true;
    }

    public function clearCache(): void
    {
        $this->memo = [];
        // invalida tutte le entry bumpando la version key
        $verKey = $this->cfg->cachePrefix . ':ver';
        cache()->save($verKey, $this->cacheVersion() + 1, 0);
    }

    public function flushAllCache(): void
    {
        $this->clearCache();
    }

    // -- Interni --------------------------------------------------------------

    protected function getPermissionsForRole(int $roleId): array
    {
        $slot = "role:{$roleId}";
        if (isset($this->memo[$slot])) {
            return $this->memo[$slot];
        }

        $key    = $this->cfg->cachePrefix . ':' . $this->cacheVersion() . ':role:' . $roleId;
        $cached = cache()->get($key);
        if (is_array($cached)) {
            return $this->memo[$slot] = $cached;
        }

        $rows  = $this->permissionModel->getForRole($roleId);
        $slugs = array_column(array_map(static fn ($r) => (array) $r, $rows), 'slug');

        cache()->save($key, $slugs, $this->cfg->cacheTtl);

        return $this->memo[$slot] = $slugs;
    }

    protected function getRoleIdForUser(int $userId): ?int
    {
        $slot = "role_id:{$userId}";
        if (array_key_exists($slot, $this->memo)) {
            return $this->memo[$slot];
        }

        if ($userId === $this->getCurrentUserId()) {
            $roleId = session()->get($this->cfg->roleIdSessionKey);
            if ($roleId !== null) {
                return $this->memo[$slot] = (int) $roleId;
            }
        }

        $row = db_connect()->table($this->cfg->usersTable)
            ->select($this->cfg->roleIdColumn)
            ->where('id', $userId)
            ->get()
            ->getRowObject();

        $col = $this->cfg->roleIdColumn;

        return $this->memo[$slot] = ($row && $row->{$col} !== null) ? (int) $row->{$col} : null;
    }

    protected function getCurrentUserId(): ?int
    {
        $key = $this->cfg->userIdSessionKey;

        return session()->has($key) ? (int) session()->get($key) : null;
    }

    private function cacheVersion(): int
    {
        return (int) (cache()->get($this->cfg->cachePrefix . ':ver') ?: 1);
    }
}
