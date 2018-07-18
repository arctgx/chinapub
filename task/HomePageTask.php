<?php

class HomePageTask extends TaskBase {

    protected $_task_id;

    // php -f cli.php HomePage Spider
    public function SpiderAction() {
        $this->_task_id = sprintf('homepage_task_%d', time());

        $strLog = sprintf('task[%s] start', $this->_task_id);
        Log::notice($strLog);

        $htmlContents = Spider::getHomePage();
        if ($htmlContents===false) {
            $strLog = sprintf('get home page fail');
            Log::fatal($strLog);
            exit(1);
        }

        // 提取书籍id
        $bookIDList = Parser::extractBookIDs($htmlContents);
        // var_dump($bookIDList);
        if (empty($bookIDList)) {
            $strLog = sprintf('extractBookIDs has no data');
            Log::warning($strLog);
            exit(1);
        }
        $strLog = sprintf('home extract %d book id', count($bookIDList));
        Log::notice($strLog);

        // 存储数据
        $dataService = new DataService();
        foreach ($bookIDList as $bookID) {
            $homePageInsertID = $dataService->checkAndSaveHomePageBook($bookID);
            if ($homePageInsertID === false) {
                $strLog = sprintf('save data to home page table fail, book_id[%d]', $bookID);
                Log::warning($strLog);
            } else {
                $strLog = sprintf('save data to home page table success, book_id[%d] insert_id[%d]', $bookID, $homePageInsertID);
                Log::notice($strLog);
            }

            $bookInsertID = $dataService->checkAndSaveBook($bookID, 0);
            if ($bookInsertID === false) {
                $strLog = sprintf('save data to book table fail, book_id[%d]', $bookID);
                Log::warning($strLog);
            } else {
                $strLog = sprintf('save data to book table success, book_id[%d] insert_id[%d]', $bookID, $bookInsertID);
                Log::notice($strLog);
            }
        }
    }

    public function QueryAction() {
        echo "todo\n";
    }
}


