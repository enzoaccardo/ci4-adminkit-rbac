<?php

namespace AdminKit\Rbac\Traits;

/**
 * Zucchero per i controller che non estendono BaseAdminController (es. API):
 * espone can()/authorize() delegando al service('rbac'). I controller admin del
 * kit hanno già questi metodi via soft-discovery, quindi lì il trait non serve.
 */
trait HasRbac
{
    protected function can(string $permission): bool
    {
        $rbac = service('rbac');

        return $rbac->isSuperAdmin() || $rbac->can($permission);
    }

    protected function authorize(string $permission): void
    {
        service('rbac')->authorize($permission);
    }
}
