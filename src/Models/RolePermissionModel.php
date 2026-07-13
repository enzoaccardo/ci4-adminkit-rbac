<?php

namespace AdminKit\Rbac\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table          = 'role_permissions';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $useTimestamps  = false;
    protected $useSoftDeletes = false;

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
