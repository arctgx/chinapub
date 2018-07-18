<?php

class BookTask extends TaskBase {

    protected $_task_id;

    // 更新book详细数据
    // php -f cli.php Book Update
    public function UpdateAction() {
        $this->_task_id = sprintf('book_update_task_%d', time());

        $strLog = sprintf('task[%s] start', $this->_task_id);
        Log::notice($strLog);

        $dataService = new DataService();

        $lastID = 0;
        while (true) {
            $bookList = $dataService->getUnProcessList($lastID);
            if (empty($bookList)) {
                Log::notice('book list is empty');
                break;
            }
            // var_dump($bookList);
            foreach ($bookList as $oneBook) {
                $lastID = $ID = $oneBook['id'];

                $bookID = $oneBook['book_id'];
                $htmlContents = Spider::getBookPage($bookID);
                if ($htmlContents === false) {
                    $strLog = sprintf('get book page fail, book_id[%d] book_table_id[%d]', $bookID, $ID);
                    Log::warning($strLog);
                    continue;
                }

                // 解析图书信息
                $bookInfo = Parser::extractBookInfo($htmlContents);
                if ($bookInfo===false) {
                    $strLog = sprintf('extractBookInfo fail, book_id[%d] book_table_id[%d]', $bookID, $ID);
                    Log::warning($strLog);
                    continue;
                }

                // 获取推荐图书列表
                $recBooks = Parser::extractBookIDs($htmlContents, $bookID);

                $dataService->updateBook($ID, $bookID, $bookInfo, $recBooks);
            }
        }
    }
}


