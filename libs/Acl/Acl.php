<?php

namespace Acl;

use Nette\Security\Permission;

/**
 * 
 */
class Acl extends Permission {

    public function __construct() {
        // roles
        $this->addRole('guest');
        $this->addRole('editor', 'guest');
        $this->addRole('admin', 'editor');
        $this->addRole('developer');

        // resources
        $this->addResource('Admin:Homepage');
        $this->addResource('Admin:Asset');
        $this->addResource('Admin:File');

        // privileges
        $this->allow('admin', Permission::ALL, Permission::ALL);
    }

}
