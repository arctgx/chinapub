<?php

class Dao_Book extends DaoBase {

    protected $_table = 'book';

    const STATUS_TODO = 0; // 待处理
    const STATUS_DONE = 1; // 处理完成

    public function save($saveData) {
        $db = $this->getDB();

        $curTime = time();
        $sql = sprintf(
            'INSERT INTO `%s` (book_id, from_id, book_url, create_at, update_at) VALUES (:book_id, :from_id, :book_url, :create_at, :update_at)',
            $this->_table
        );
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':book_id',    $saveData['book_id'],    \PDO::PARAM_INT);
        $stmt->bindParam(':from_id',    $saveData['from_id'],    \PDO::PARAM_INT);
        $stmt->bindParam(':book_url',   $saveData['book_url'],   \PDO::PARAM_STR);
        $stmt->bindParam(':create_at',  $saveData['create_at'],  \PDO::PARAM_INT);
        $stmt->bindParam(':update_at',  $saveData['update_at'],  \PDO::PARAM_INT);
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

    public function getInfoByBookIDList($bookIDList) {
        $db = $this->getDB();
        if (!is_array($bookIDList) || empty($bookIDList)) {
            return array();
        }
        $bookIDList = array_unique($bookIDList);
        $bookIDList = array_map('intval', $bookIDList);

        $sql = sprintf(
            'SELECT * FROM `%s` WHERE book_id IN (%s)',
            $this->_table, implode(',', $bookIDList)
        );

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $queryRet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($queryRet)) {
            return array();
        }

        $converRet = array();
        foreach ($queryRet as $item) {
            $converRet[$item['book_id']] = $item;
        }

        return $converRet;
    }

    public function getUnProcessList($lastID, $reqNum) {
        $db = $this->getDB();
        $sql = sprintf(
            'SELECT * FROM `%s` WHERE id > :last_id AND process_status=%d ORDER BY id ASC LIMIT :req_num',
            $this->_table, self::STATUS_TODO
        );

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':last_id', $lastID, \PDO::PARAM_INT);
        $stmt->bindParam(':req_num', $reqNum, \PDO::PARAM_INT);

        $stmt->execute();
        $ret = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $ret;
    }

    public function update($ID, $updateData) {
        $canUpdateFields = array(
            'title'          => \PDO::PARAM_STR,
            'cover_url'      => \PDO::PARAM_STR,
            'category'       => \PDO::PARAM_STR,
            'author'         => \PDO::PARAM_STR,
            'press'          => \PDO::PARAM_STR,
            'ISBN'           => \PDO::PARAM_STR,
            'process_status' => \PDO::PARAM_INT,
        );

        $upFields = $bindKeys = array();
        foreach ($canUpdateFields as $k => $v) {
            if (isset($updateData[$k])) {
                $upFields[] = $k;
                $bindKeys[] = $k .'=:' . $k;
            }
        }
        if (empty($upFields)) {
            return 0;
        }
        // 添加更新时间
        $canUpdateFields['update_at'] = \PDO::PARAM_INT;
        $upFields[] = 'update_at';
        $bindKeys[] = 'update_at=:update_at';
        $updateData['update_at'] = time();

        $db = $this->getDB();
        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->_table, implode(',', $bindKeys)
        );

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $ID, \PDO::PARAM_INT);
        foreach ($upFields as $key) {
            $stmt->bindParam(':'.$key, $updateData[$key], $canUpdateFields[$key]);
        }

        $ret = $stmt->execute();
        return $ret;
    }
}

