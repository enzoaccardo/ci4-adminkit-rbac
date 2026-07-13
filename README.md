# ci4-adminkit-rbac

RBAC (ruoli / permessi) per **CodeIgniter 4**, spoke dell'ecosistema `ci4-adminkit`. Implementa il contratto `AdminKit\Contracts\Rbac`: **installarlo attiva il controllo permessi nel kit via soft-discovery** (il `BaseAdminController` delega `authorize()`/`can()` al `service('rbac')` se presente). Senza questo pacchetto, il kit resta fail-closed sulle azioni con permesso.

## Cosa contiene
- **`service('rbac')`** (`RbacService`, auto-scoperto) — `isSuperAdmin()`, `can()`, `authorize()` (contratto) + API estesa (`userCan`, `roleCan`, `assignPermission`, `revokePermission`, `getPermissionsForCurrentUser`). Cache permessi su cache nativa CI4 (version-key), nessuna dipendenza da servizi dell'app.
- **Model** `RoleModel` / `PermissionModel` (ruoli, permessi, join `role_permissions`).
- **Migrazioni**: `roles`, `permissions`, `role_permissions`, + `role_id` su `users`.
- **Trait** `HasRbac` (per controller non-kit, es. API: `can()`/`authorize()`).
- **Config** `Rbac` (TTL cache, chiavi di sessione, tabella/colonna utenti).
- **Eccezione** `ForbiddenException` (HTTP 403).

## Installazione (dev, path repository)
```jsonc
"repositories": [
  { "type": "path", "url": "../ci4-adminkit" },
  { "type": "path", "url": "../ci4-adminkit-rbac" }
]
```
```bash
composer require enzoaccardo/ci4-adminkit-rbac:@dev
php spark migrate --all     # roles/permissions/role_permissions + role_id su users
```
Richiede una tabella `users` con `id` e (per il superadmin) una colonna/sessione `is_superadmin`. I **permessi e ruoli sono dati dell'app**: seedali tu (il pacchetto porta lo schema, non i dati).

## Contratto / sessione
`RbacService` legge lo stato utente dalla sessione: `user_id`, `role_id`, `is_superadmin` (nomi configurabili in `Config\Rbac`). Un superadmin bypassa i controlli. I controller admin del kit ottengono `authorize()`/`can()` automaticamente (soft-discovery); i controller non-kit possono usare il trait `HasRbac`.
