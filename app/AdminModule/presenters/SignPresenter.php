<?php

namespace AdminModule;

use Nette\Application\UI,
    Nette\Security as NS;

/**
 * Covering sign in, sign out process.
 *  
 * @author Radek Tobolka
 */
class SignPresenter extends BasePresenter {

    /** @persistent */
    public $backlink = '';

    /**
     * Sign in form.
     * 
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
        $form = new UI\Form;
        $form->addText('username', 'Username:')
                ->setRequired('Please provide a username.')
                ->setAttribute('placeholder', 'username');

        $form->addPassword('password', 'Password:')
                ->setRequired('Please provide a password.')
                ->setAttribute('placeholder', 'password');


        $form->addCheckbox('remember', 'Remember me on this computer');

        $form->addSubmit('send', 'Sign in');

        $form->onSuccess[] = callback($this, 'signInFormSubmitted');
        $form->setTranslator($this->context->translator);

        return $form;
    }

    /**
     * Validation, handeling sign in process.
     * @param type $form
     */
    public function signInFormSubmitted($form) {
        try {
            $values = $form->getValues();
            if ($values->remember) {
                $this->getUser()->setExpiration('+ 14 days', FALSE);
            } else {
                $this->getUser()->setExpiration('+ 20 minutes', TRUE);
            }
            $this->getUser()->login($values->username, $values->password);

            $this->flashMessage("You have been signed in.");

            $this->getApplication()->restoreRequest($this->backlink);
            $this->redirect(":Admin:Asset:edit");
        } catch (NS\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    /**
     * Handling sign out
     */
    public function actionOut() {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('Sign:in');
    }

}
