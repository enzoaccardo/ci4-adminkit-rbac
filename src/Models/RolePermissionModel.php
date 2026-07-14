<?php

namespace AdminKit\Rbac\Models;

use AdminKit\Models\BaseModel;

/**
 * Pivot role↔permission: nessun ciclo di vita proprio. Audit/soft-delete
 * disattivati (la tabella ha i campi audit dalla BaseMigration, non usati qui).
 */
class RolePermissionModel extends BaseModel
{
    protected $table      = 'role_permissions';
    protected $primaryKey = 'id';

    protected $useTimestamps  = false;
    protected $useSoftDeletes = false;
    protected $beforeInsert   = [];
    protected $beforeUpdate   = [];
    protected $beforeDelete   = [];

    protected $allowedFields = ['role_id', 'permission_id'];

    /** Sostituisce i permessi di un ruolo con l'elenco dato. */
    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->db->table($this->table)->where('role_id', $roleId)->delete();
        foreach ($permissionIds as $permId) {
            $this->db->table($this->table)->insert([
                'role_id'       => $roleId,
                'permission_id' => (int) $permId,
            ]);
        }
    }

    /** @return int[] id dei permessi assegnati al ruolo */
    public function getPermissionIdsForRole(int $roleId): array
    {
        $rows = $this->db->table($this->table)
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($rows, 'permission_id'));
    }
}
