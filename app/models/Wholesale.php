<?php

/**
 * Model for wholesales with conection to DB.
 * 
 * @author Radek Tobolka
 */
class Wholesale extends Nette\Object {

    private $database;

    public function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
    }

    /**
     * Returning table of wholesales 
     * 
     * @return object
     */
    private function wholesales() {
        return $this->database->table('wholesale');
    }

    /**
     * Returning all wholesales.
     * 
     * @return object
     */
    public function all() {
        return $this->wholesales()->order('group')->order('order');
    }

    /**
     * Returning group of wholesales.
     * 
     * @param id $group
     * @return object
     */
    public function group($group) {
        return $this->wholesales()->where('group', $group)->order('order');
    }

    /**
     * Returning one item from wholesales.
     * 
     * @param int $id
     * @return object
     */
    public function item($id) {
        return $this->wholesales()->where('id', $id);
    }

}
