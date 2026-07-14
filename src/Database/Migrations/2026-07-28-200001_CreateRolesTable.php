<?php

namespace AdminKit\Rbac\Database\Migrations;

use AdminKit\Database\BaseMigration;

class CreateRolesTable extends BaseMigration
{
    public function up(): void
    {
        $this->createTable('roles', [
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'level'       => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 1],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
        ]);
        $this->rawQuery('ALTER TABLE roles ADD UNIQUE INDEX uniq_roles_slug (slug)');
    }

    public function down(): void
    {
        $this->dropTable('roles');
    }
}
