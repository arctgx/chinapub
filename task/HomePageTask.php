<?php

class HomePageTask extends TaskBase {

    protected $_task_id;

    public function beforTask() {
        $logFile = 'homepage_task.log.'.date('Ymd');
        Log::setLogFile($logFile);
        return parent::beforTask();
    }

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

    // php -f cli.php HomePage Query book_num=20
    public function QueryAction() {
        $bookNum = $this->getParam('book_num', 10);
        $dataService = new DataService();
        $info = $dataService->queryHomePage($bookNum);
        if (empty($info)) {
            printf("list is empty\n");
            return ;
        }

        $cnt = 0;
        foreach ($info as $oneBook) {
            printf("book %d\n", ++$cnt);

            printf("      id: %d\n", $oneBook['book_id']);
            printf("    名称: %s\n", $oneBook['title']);
            printf("     url: %s\n", $oneBook['url']);
            printf("    时间: %s\n", $oneBook['homepage_time']);
            printf("\n");
        }
    }
}


