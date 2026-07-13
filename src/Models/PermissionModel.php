<?php

namespace AdminKit\Rbac\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table          = 'permissions';
    protected $primaryKey     = 'id';
    protected $returnType     = 'object';
    protected $useTimestamps  = false;
    protected $useSoftDeletes = false;

    protected $allowedFields = ['action', 'name', 'slug', 'description'];

    public function getBySlug(string $slug): ?object
    {
        return $this->where('slug', $slug)->first();
    }

    /** Permessi assegnati a un ruolo (join role_permissions). */
    public function getForRole(int $roleId): array
    {
        return $this->db->table('permissions p')
            ->select('p.*')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $roleId)
            ->get()
            ->getResultObject();
    }

    /** Tutti i permessi raggruppati per modulo (prefisso dello slug). */
    public function getAllWithModule(): array
    {
        $rows = $this->db->table('permissions p')
            ->select("p.*, SUBSTRING_INDEX(p.slug, '.', 1) as module_slug")
            ->orderBy("SUBSTRING_INDEX(p.slug, '.', 1)")
            ->orderBy('p.action')
            ->get()
            ->getResultObject();

        $grouped = [];
        foreach ($rows as $row) {
            $row->module_name = ucfirst(str_replace('_', ' ', $row->module_slug));
            $grouped[$row->module_slug][] = $row;
        }

        return $grouped;
    }
}
