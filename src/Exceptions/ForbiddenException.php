<?php

namespace AdminKit\Rbac\Exceptions;

use CodeIgniter\Exceptions\HTTPExceptionInterface;
use RuntimeException;

/**
 * Eccezione HTTP 403. Lanciata da RbacService::authorize() quando l'utente non
 * ha il permesso richiesto. Implementa HTTPExceptionInterface → CI4 risponde 403.
 */
class ForbiddenException extends RuntimeException implements HTTPExceptionInterface
{
    protected $code = 403;

    public function __construct(string $message = 'Accesso non autorizzato.', int $code = 403, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
