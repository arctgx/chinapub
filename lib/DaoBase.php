<?php

class DaoBase {

    protected $_db_name = 'chinapub';

    protected $_table = '';

    public function getDB() {
        return DbManager::getDB($this->_db_name);
    }

    public function getByID($ID) {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE id=%d',
            $this->_table, $ID
        );

        $db = $this->getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $dataRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        return empty($dataRet) ? array() : $dataRet;
    }
}

