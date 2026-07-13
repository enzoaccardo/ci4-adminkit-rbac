<?php

namespace AdminKit\Rbac\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useTimestamps = true;
    protected $useSoftDeletes = false;

    protected $allowedFields = ['name', 'slug', 'level', 'description', 'is_active'];

    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[100]',
        'slug'  => 'required|min_length[2]|max_length[100]|alpha_dash|is_unique[roles.slug,id,{id}]',
        'level' => 'required|integer|greater_than[0]|less_than_equal_to[100]',
    ];

    public function getBySlug(string $slug): ?object
    {
        return $this->where('slug', $slug)->where('is_active', 1)->first();
    }

    public function getActive(): array
    {
        return $this->where('is_active', 1)->orderBy('level', 'DESC')->findAll();
    }
}
