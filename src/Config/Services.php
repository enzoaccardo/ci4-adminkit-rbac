<?php

namespace AdminKit\Rbac\Config;

use AdminKit\Rbac\Services\RbacService;
use CodeIgniter\Config\BaseService;

/**
 * Servizio rbac, auto-scoperto da CodeIgniter. La sua sola presenza attiva il
 * soft-discovery nel BaseAdminController del kit (che vi delega authorize()/can()).
 */
class Services extends BaseService
{
    public static function rbac(bool $getShared = true): RbacService
    {
        if ($getShared) {
            return static::getSharedInstance('rbac');
        }

        return new RbacService();
    }
}
