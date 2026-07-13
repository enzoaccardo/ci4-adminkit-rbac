<?php

namespace AdminKit\Rbac\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Aggiunge role_id alla tabella `users` (che deve già esistere). is_superadmin
 * resta un attributo utente dell'app (non gestito qui).
 */
class AddRoleIdToUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'role_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'default' => null],
        ]);
        $this->db->query('ALTER TABLE users ADD INDEX idx_users_role_id (role_id)');
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'role_id');
    }
}
