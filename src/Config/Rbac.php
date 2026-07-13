<?php

namespace AdminKit\Rbac\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configurazione RBAC. Pubblicabile con override in app/Config/Rbac.php.
 */
class Rbac extends BaseConfig
{
    /** TTL (secondi) della cache dei permessi per ruolo. */
    public int $cacheTtl = 3600;

    /** Prefisso chiavi cache (cache nativa CI4). */
    public string $cachePrefix = 'adminkit_rbac';

    /** Chiavi di sessione da cui leggere lo stato utente. */
    public string $userIdSessionKey     = 'user_id';
    public string $roleIdSessionKey     = 'role_id';
    public string $superAdminSessionKey = 'is_superadmin';

    /** Colonna del ruolo sulla tabella utenti. */
    public string $usersTable      = 'users';
    public string $roleIdColumn    = 'role_id';
}
