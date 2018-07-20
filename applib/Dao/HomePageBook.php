<?php

class Dao_HomePageBook extends DaoBase {

    protected $_table = 'home_page_book';

    public function queryRencentBooks($reqNum) {
        $db = $this->getDB();

        $sql = sprintf(
            'SELECT * FROM %s ORDER BY id DESC LIMIT %d',
            $this->_table, $reqNum
        );
        $stmt = $db->prepare($sql);
        $ret = $stmt->execute();

        $queryRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return empty($queryRet) ? array() : $queryRet;
    }

    public function save($bookID) {
        $db = $this->getDB();

        $curTime = time();
        $sql = sprintf(
            'INSERT INTO `%s` (book_id, create_at, update_at) values (%d, %d, %d)',
            $this->_table, $bookID, $curTime, $curTime
        );
        $stmt = $db->prepare($sql);
        $ret = $stmt->execute();
        if (!$ret) {
            Log::warning('mysql err['.$stmt->errorInfo().']');
            return false;
        }
        return $db->lastInsertId();
    }

    public function getByBookID($bookID) {
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE book_id=%d',
            $this->_table, $bookID
        );

        $db = $this->getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $dataRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        return empty($dataRet) ? array() : $dataRet;
    }

}


