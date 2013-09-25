<?php

/**
 * Handling file connection to DB.
 * 
 * @author Radek Tobolka
 */
class File extends Nette\Object {

    private $database;

    public function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
    }

    /**
     * Return table of files.
     *
     * @return object
     */
    private function files() {
        return $this->database->table('file');
    }

    /**
     * Returning one file acoeding to name.
     * 
     * @param type $name
     * @return array
     */
    public function find($name) {
        return $this->files()->where('name', $name)->fetch();
    }

    /**
     * Update for files
     * 
     * @param string $name
     * @param array $data
     * @return bool
     */
    public function update($name, $data) {
        return $this->files()->where('name', $name)->update($data);
    }

}
