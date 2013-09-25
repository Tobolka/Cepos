<?php

namespace AdminModule;

use Nette\Security\User;

/**
 * Covering secure check
 * 
 * @author Radek Tobolka
 */
abstract class SecuredPresenter extends BasePresenter {

    /**
     * Checking if user is loged in
     */
    public function startup() {
        parent::startup();

        if (!$this->user->isLoggedIn()) {
            if ($this->user->getLogoutReason() === User::INACTIVITY) {
                $this->flashMessage('Session timeout, you have been logged out', 'warning');
            }

            $backlink = $this->getApplication()->storeRequest();
            $this->redirect('Sign:in', array('backlink' => $backlink));
        } else {
            if (!$this->user->isAllowed($this->name, $this->action)) {
                $this->flashMessage('Access diened. You don\'t have permissions to view that page.', 'warning');
                $this->redirect('Sign:in');
            }
        }
    }

}
