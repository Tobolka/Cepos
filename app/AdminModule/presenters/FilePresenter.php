<?php

namespace AdminModule;

use Nette\Application\UI\Form;
use Nette\Application\Responses\FileResponse;
use Nette\Utils\Strings;

/**
 * Covering files handling
 * 
 * @author Radek Tobolka
 */
class FilePresenter extends SecuredPresenter {

    private $files;

    public function startup() {
        parent::startup();
        $this->files = $this->getService('file');
    }

    public function renderDefault() {
        
    }

    /**
     * Form for uploading PDFs.
     * 
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentUploadForm() {
        $form = new Form();

        $form->addUpload('file', 'Chose File')->addRule(Form::MIME_TYPE, 'File has to be PDF', 'application/pdf');

        $company = array(
            'cepos' => 'Čepos',
            'gastro' => 'Gastro',
        );
        $form->addRadioList('company', 'leaflets action', $company)->setDefaultValue('cepos');

        $form->addText('name', 'Name')->setRequired('Set name');

        $form->addSubmit('save', 'Upload');

        $form->onSuccess[] = callback($this, 'save');
        $form->setTranslator($this->context->translator);

        return $form;
    }

    /**
     * Handeling downloading PDF.
     * 
     * @param string $name
     */
    public function handleDownload($name) {
        $file = $this->files->find($name);
        $pdf = WWW_DIR . '/files/' . $file->path;
        $this->sendResponse(new FileResponse($pdf));
    }

    /**
     * Form validation, PDF uploader.
     * 
     * @param \Nette\Application\UI\Form $form
     */
    public function save(Form $form) {
        $values = $form->getValues();
        $file = $values->file;
        $company = $values->company;

        if ($file->isOk()) {
            $extension = pathinfo($file->getName(), PATHINFO_EXTENSION);
            $name = Strings::webalize($form->values->name);

            $file->move(WWW_DIR . '/files/' . $name . '.' . $extension);

            $data = array(
                'path' => $name . '.' . $extension,
            );

            $this->files->update($company, $data);

            $this->flashMessage("File saved");
        } else {
            $this->flashMessage("File uploadind failed ", 'error');
        }

        $this->redirect('this');
    }

}
