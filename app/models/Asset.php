<?php

/**
 * Model for Assets with access to DB.
 * 
 * @author Radek Tobolka
 */
class Asset extends Nette\Object {

    private $database;

    /**
     * Connection to DB.
     * 
     * @param Nette\Database\Connection $database
     */
    public function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
    }

    /**
     * Return table assets.
     * 
     * @return object
     */
    private function assets() {
        return $this->database->table('asset');
    }

    /**
     * Return all artycles from assets.
     * 
     * @param string $lang
     * @return array
     */
    public function allArticles($lang) {
        return $this->assets()->order('parent')->order('order')->where('NOT type', 'category')->where('NOT type', 'wholesales')->where('language', $lang);
    }

    /**
     * Return alss assets, depending on langugage.
     *  
     * @param string $lang
     * @return array
     */
    public function all($lang) {
        return $this->assets()->order('order')->order('parent')->where('language', $lang)->fetchPairs('id');
    }

    /**
     * Return item sidebar.
     *
     * @param string $lang
     * @return array
     */
    public function sidebar($lang) {
        return $this->assets()->where('language', $lang)->where('type', 'sidebar')->fetch();
    }

    /**
     * Finding one item from assets according to id.
     * 
     * @param int $id
     * @return object
     */
    public function find($id) {
        return $this->assets()->get($id);
    }

    /**
     * Finding one item from assets according to url and language.
     * @param string $url
     * @param string $lang
     * @return object
     */
    public function findUrl($url, $lang) {
        return $this->assets()->where('url', $url)->where('language', $lang)->fetch();
    }

    /**
     * Update asset.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        return $this->assets()->where('id', $id)->update($data);
    }

}
