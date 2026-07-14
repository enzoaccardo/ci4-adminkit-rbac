<?php

namespace AdminKit\Rbac\Database\Migrations;

use AdminKit\Database\BaseMigration;

class CreateRolePermissionsTable extends BaseMigration
{
    public function up(): void
    {
        $this->createTable('role_permissions', [
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'permission_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
        ]);

        $this->rawQuery('ALTER TABLE role_permissions ADD UNIQUE INDEX uniq_role_perm (role_id, permission_id)');
        $this->rawQuery('ALTER TABLE role_permissions ADD CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE');
        $this->rawQuery('ALTER TABLE role_permissions ADD CONSTRAINT fk_rp_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        $this->dropTable('role_permissions');
    }
}
