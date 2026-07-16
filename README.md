# ci4-adminkit-rbac

Ruoli e permessi per ci4-adminkit.

Il kit definisce un contratto (`AdminKit\Contracts\Rbac`) ma nessuna
implementazione. Questo pacchetto è l'implementazione: appena è installato,
`authorize()` e `can()` nei controller del pannello iniziano a rispondere sul
serio, senza altro da configurare. Se lo rimuovi, il kit torna a bloccare per
sicurezza le azioni che richiedono un permesso.

## Come funziona

Tre tabelle: `roles`, `permissions` e la pivot `role_permissions`. Ogni utente
ha un `role_id`; chi è superadmin salta i controlli. I permessi di un ruolo
vengono tenuti in cache e la cache si invalida quando cambi le assegnazioni.

I permessi in sé sono roba dell'applicazione, non del pacchetto: qui c'è lo
schema, i ruoli e i permessi li carichi tu con un seeder in base ai moduli che
hai (`utenti.modifica`, `report.esporta`, quello che ti serve).

## Installazione

```
composer require enzoaccardo/ci4-adminkit-rbac
php spark migrate --all
```

Le migrazioni creano le tre tabelle e aggiungono `role_id` a `users`.

## Uso

Nei controller del pannello scrivi direttamente `$this->authorize('utenti.modifica')`
oppure `$this->can('utenti.elimina')`: il kit inoltra al servizio. Fuori dal
pannello, per esempio in un controller di API, c'è il trait `HasRbac` con gli
stessi due metodi.

## Licenza

MIT. Vedi [LICENSE](LICENSE).
