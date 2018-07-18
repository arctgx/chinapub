<?php

class Dao_RecBook extends DaoBase {

    protected $_table = 'rec_book';

    public function save($fromID, $toID) {
        $db = $db = $this->getDB();

        $curTime = time();
        $sql = sprintf(
            'INSERT INTO `%s` (book_id_from, book_id_to, create_at, update_at) VALUES (%d, %d, %d, %d)',
            $this->_table, $fromID, $toID, $curTime, $curTime
        );
        $stmt = $db->prepare($sql);
        $ret = $stmt->execute();
        if (!$ret) {
            Log::warning('mysql err['.$stmt->errorInfo().']');
            return false;
        }
        return $db->lastInsertId();
    }

    public function queryInfo($fromID, $toID) {
        $db = $db = $this->getDB();

        $sql = sprintf(
            'SELECT * FROM `%s` WHERE book_id_from=:book_id_from AND book_id_to=:book_id_to',
            $this->_table
        );
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':book_id_from', $fromID, \PDO::PARAM_INT);
        $stmt->bindParam(':book_id_to',   $toID,   \PDO::PARAM_INT);
        $stmt->execute();

        $queryRet = $stmt->fetch(\PDO::FETCH_ASSOC);
        return empty($queryRet) ? array() :  $queryRet;
    }

}


