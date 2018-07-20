<?php

// 爬虫
class Spider {

    protected static $_user_agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36';

    // 下载首页
    public static function getHomePage() {
        $url      = 'http://china-pub.com';
        $dataFile = DATA_PATH.'china-pub_index.html';

        return self::_dl($url, $dataFile);
    }

    // 下载书页
    public static function getBookPage($bookID) {
        $url      = sprintf('http://product.china-pub.com/%s', $bookID);
        $dataFile = sprintf('%sbook_%s.html', DATA_PATH, $bookID);

        return self::_dl($url, $dataFile);
    }

    // 使用 curl 下载
    protected static function _dl($url, $dataFile) {
        if (file_exists($dataFile) && filesize($dataFile) && time()-filectime($dataFile)<3600) {
            $htmlContents = file_get_contents($dataFile);
            Log::notice('use local file');
            return $htmlContents;
        }

        // mv 超时的文件
        if (file_exists($dataFile)) {
            rename($dataFile, $dataFile.'_'.date('YmdHis'));
        }

        Log::notice('download ['.$url.'] from china-pub');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, self::$_user_agent);
        curl_setopt($ch, CURLOPT_URL, $url);

        $contents = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno != 0) {
            $strLog = sprintf('curl fail, url[%s]', $url);
            Log::warning($strLog);
            return false;
        }

        $htmlContents = mb_convert_encoding($contents, 'UTF-8', 'gbk');
        file_put_contents($dataFile, $htmlContents);

        return $htmlContents;
    }

}
