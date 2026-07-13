<?php

namespace AdminKit\Rbac\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermissionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('permissions', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('permissions', true);
    }
}
