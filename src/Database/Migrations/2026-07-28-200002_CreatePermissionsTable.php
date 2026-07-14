<?php

namespace AdminKit\Rbac\Database\Migrations;

use AdminKit\Database\BaseMigration;

class CreatePermissionsTable extends BaseMigration
{
    public function up(): void
    {
        $this->createTable('permissions', [
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
        ]);
        $this->rawQuery('ALTER TABLE permissions ADD UNIQUE INDEX uniq_permissions_slug (slug)');
    }

    public function down(): void
    {
        $this->dropTable('permissions');
    }
}
