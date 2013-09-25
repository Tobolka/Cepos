<?php

namespace AdminModule;

use Nette\Application\UI\Form;

/**
 * Coverning work with assets (articles, categories, links). 
 * 
 * @author Radek Tobolka
 */
class AssetPresenter extends SecuredPresenter {

    private $assets;

    /** @persistent */
    public $id;

    /** @persistent */
    public $type;

    /** @persistent */
    public $language;
    public $content;

    /**
     * Setting language, getting services.
     */
    public function startup() {
        parent::startup();
        $this->assets = $this->getService('asset');

        if (!isset($this->language)) {
            $this->language = 'cs';
        }
        $this->template->articles = $this->assets->allArticles($this->language);
    }

    /**
     * Edit view.
     * 
     * @param int $id
     */
    public function renderEdit($id) {
        $asset = $this->assets->find($id);

        $this->id = $id;
        $this->type = $asset->type;
        $this->content = $asset->content;
        $this->language = $asset->language;
    }

    /**
     * Form for edit form for links and articles.
     * 
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentEditForm() {
        $form = new Form();

        if ($this->type == 'link') {
            $form->addText('content', 'Comment Internal')->setDefaultValue($this->content)->setAttribute('class', 'span9')
                    ->addRule(Form::URL, 'Url is not vaild');
        } else {
            $form->addTextArea('content', 'Comment Internal')->setDefaultValue($this->content)->setAttribute('class', 'redactor');
        }

        $form->addSubmit('save', 'Save');

        $form->onSuccess[] = callback($this, 'save');
        $form->setTranslator($this->context->translator);

        return $form;
    }

    /**
     * Validation, saving forms.
     * 
     * @param \Nette\Application\UI\Form $form
     */
    public function save(Form $form) {
        $values = $form->getValues();

        $data = array(
            'content' => $values->content,
        );

        $this->assets->update($this->id, $data);

        $this->flashMessage("Updated");

        if (!$this->isAjax()) {
            $this->redirect('this');
        } else {
            $this->invalidateControl('editor');
            $this->invalidateControl('message');
        }
    }

    /**
     * Picture uploader.
     */
    public function handlePicture() {
        $dir = 'img/content/';

        $_FILES['file']['type'] = strtolower($_FILES['file']['type']);

        if ($_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg' || $_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/pjpeg') {

            $filename = md5(date('YmdHis')) . '.jpg';
            $file = $dir . $filename;

            move_uploaded_file($_FILES['file']['tmp_name'], $file);

            $array = array(
                'filelink' => '/' . $dir . $filename
            );

            $this->sendResponse(new JsonResponse($array));
        }
    }

}
