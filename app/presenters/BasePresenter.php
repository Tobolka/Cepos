<?php
/**
 * Setting language and creating template.
 * 
 * @author Radek Tobolka
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

    /** @persistent */
    public $lang;

    /**
     * Setting language, creating template, translation.
     * 
     * @param class $class
     * @return template
     */
    public function createTemplate($class = NULL) {
        $template = parent::createTemplate($class);

        // If language isn't set, use default
        if (!isset($this->lang)) {
            $this->lang = $this->context->parameters["lang"];
        }

        $this->context->translator->setLang($this->lang);
        $template->setTranslator($this->context->translator);

        return $template;
    }

}
