<?php

// 数据保存
class DataService {

    protected $_dao_home_page_book;
    protected $_dao_book;
    protected $_dao_rec_book;

    public function __construct() {

        $this->_dao_home_page_book = new Dao_HomePageBook();
        $this->_dao_book           = new Dao_Book();
        $this->_dao_rec_book       = new Dao_RecBook();
    }

    // 查询 首页
    public function queryHomePage($queryNum) {
        $bookList = $this->_dao_home_page_book->queryRencentBooks($queryNum);
        if (empty($bookList)) {
            return array();
        }
        $bookIDList = array_column($bookList, 'book_id');

        $bookInfos = $this->_dao_book->getInfoByBookIDList($bookIDList);

        $retData = array();
        foreach ($bookList as $item) {
            $bookID = $item['book_id'];
            if (!isset($bookInfos[$bookID])) {
                $strLog = sprintf('can not get book info of book_id [%d]', $bookID);
                Log::warning($strLog);
                continue;
            }
            $oneBookInfo = $bookInfos[$bookID];

            if ($oneBookInfo['process_status'] == Dao_Book::STATUS_TODO) {
                $strLog = sprintf('book info not processed, book_id [%d]', $bookID);
                Log::warning($strLog);
                continue;
            }

            $retData[] = array(
                'book_id'       => $bookID,
                'title'         => $oneBookInfo['title'],
                'url'           => $oneBookInfo['book_url'],
                'homepage_time' => date('Y-m-d H:i:s', $item['create_at']),
            );
        }

        return $retData;
    }

    // 存储
    public function checkAndSaveHomePageBook($bookID) {
        $daoHomePageBook = $this->_dao_home_page_book;
        $bookInfo = $daoHomePageBook->getByBookID($bookID);
        if (!empty($bookInfo)) {
            $strLog = sprintf('book exists in home page table, book_id [%d]', $bookID);
            Log::notice($strLog);
            return $bookInfo['id'];
        }
        return $daoHomePageBook->save($bookID);
    }

    public function checkAndSaveBook($bookID, $fromID) {
        $daoBook = $this->_dao_book;
        $bookInfo = $daoBook->getByBookID($bookID);
        if (!empty($bookInfo)) {
            $strLog = sprintf('book exists in book table, book_id [%d]', $bookID);
            Log::notice($strLog);
            return $bookInfo['id'];
        }

        $curTime = time();
        $saveData = array(
            'book_id'   => $bookID,
            'from_id'   => $fromID,
            'book_url'  => sprintf('http://product.china-pub.com/%d', $bookID),
            'create_at' => $curTime,
            'update_at' => $curTime,
        );
        return $daoBook->save($saveData);
    }

    public function checkAndSaveRecBook($fromID, $toID) {
        $daoRecBook = $this->_dao_rec_book;
        $recBookInfo = $daoRecBook->queryInfo($fromID, $toID);
        if (!empty($recBookInfo)) {
            $strLog = sprintf('rec book info exists in rec book table, from_id[%d] to_id[%d]', $fromID, $toID);
            Log::notice($strLog);
            return $recBookInfo['id'];
        }

        return $daoRecBook->save($fromID, $toID);
    }

    public function getUnProcessList($lastID) {
        return $this->_dao_book->getUnProcessList($lastID, 10);
    }

    public function updateBook($bookTableID, $bookID, $bookInfo, $recBookList) {
        // 更新书籍信息
        $bookUpData = array(
            'title'     => $bookInfo['title'],
            'cover_url' => $bookInfo['cover_url'],
            'category'  => $bookInfo['category'],
            'author'    => $bookInfo['author'],
            'press'     => $bookInfo['press'],
            'ISBN'      => $bookInfo['ISBN'],
        );
        $this->_dao_book->update($bookTableID, $bookUpData);

        if (!empty($recBookList)) {
            foreach ($recBookList as $oneRecBookID) {
                self::checkAndSaveBook($oneRecBookID, $bookID);
                self::checkAndSaveRecBook($bookID, $oneRecBookID);
            }
        }

        $bookUpData = array(
            'process_status' => Dao_Book::STATUS_DONE,
        );
        $this->_dao_book->update($bookTableID, $bookUpData);
    }
}
