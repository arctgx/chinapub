<?php

// 解析器
class Parser {

    // 获取所有bookID
    public static function extractBookIDs($htmlContents, $filterID=0) {
        $tmp = array();
        $matchRet = preg_match_all('/http:\/\/product\.china-pub\.com\/([0-9]+)/', $htmlContents, $tmp);
        if (!$matchRet) {
            return array();
        }

        $matchRes = array_unique($tmp[1]);
        if ($filterID==0) {
            return $matchRes;
        }

        $returnRet = array();
        if (!empty($matchRes)) {
            foreach ($matchRes as $oneID) {
                if ($oneID==$filterID) {
                    continue;
                }
                $returnRet[] = $oneID;
            }
        }
        return $returnRet;
    }

    public static function extractBookInfo($htmlContents) {
        $title = self::_get_title($htmlContents);
        if ($title===false) {
            $strLog = sprintf('get title fail');
            Log::warning($strLog);
            return false;
        }

        $coverUrl = self::_get_cover_url($htmlContents);
        if ($coverUrl===false) {
            $strLog = sprintf('get cover_url fail');
            Log::warning($strLog);
            return false;
        }

        $category = self::_get_category($htmlContents);
        if ($category === false) {
            $strLog = sprintf('get category fail');
            Log::warning($strLog);
            return false;
        }

        $author = self::_get_author($htmlContents);
        if ($author === false) {
            $strLog = sprintf('get author fail');
            Log::warning($strLog);
            // 有些图书没有作者
            // return false;
            $author = '';
        }

        $press = self::_get_press($htmlContents);
        if ($press === false) {
            $strLog = sprintf('get press fail');
            Log::warning($strLog);
            return false;
        }

        $ISBN = self::_get_ISBN($htmlContents);
        if ($ISBN === false) {
            $strLog = sprintf('get isbn fail');
            Log::warning($strLog);
            return false;
        }

        return array(
            'title'     => $title,
            'cover_url' => $coverUrl,
            'category'  => $category,
            'author'    => $author,
            'press'     => $press,
            'ISBN'      => $ISBN,
        );
    }

    protected static function _get_title($htmlContents) {
        $startTag = '<h1>';
        $startIdx = strpos($htmlContents, $startTag);
        if ($startIdx === false) {
            $strLog = sprintf('get title fail, can not find start_tag[%s]', $startTag);
            Log::warning($strLog);
            return false;
        }

        $startIdx += strlen($startTag);

        $endTag = '</h1>';
        $endIdx = strpos($htmlContents, $endTag, $startIdx);
        if (false === $endIdx) {
            $strLog = sprintf('get title fail, can not find end_tag[%s]', $endTag);
            Log::warning($strLog);
            return false;
        }

        $title = substr($htmlContents, $startIdx, $endIdx - $startIdx);
        $title = trim($title);
        return $title;
    }

    protected static function _get_cover_url($htmlContents) {

        $startTag = '<dt class="book_s">';
        $startIdx = strpos($htmlContents, $startTag);
        if ($startIdx === false) {
            $strLog = sprintf('get cover_url fail, can not find start_tag[%s]', $startTag);
            Log::warning($strLog);
            return false;
        }

        $startIdx += strlen($startTag);

        $endTag = '/>';
        $endIdx = strpos($htmlContents, $endTag, $startIdx);
        if (false === $endIdx) {
            $strLog = sprintf('get cover_url fail, can not find end_tag[%s]', $endTag);
            Log::warning($strLog);
            return false;
        }

        $contents = substr($htmlContents, $startIdx, $endIdx - $startIdx);
        $tmp = array();

        $matchRet = preg_match('/img src=\'([^\']+)\'/', $contents, $tmp);
        // var_dump($matchRet); var_dump($tmp);
        if (!$matchRet) {
            $strLog = sprintf('match fail, contents[%s]', $contents);
            Log::warning($strLog);
            return false;
        }
        $coverUrl = $tmp[1];
        return $coverUrl;
    }

    // todo
    protected static function _get_category($htmlContents) {
        return 'category';
    }

    protected static function _get_author($htmlContents) {
        $startTag = '<li>作者：';
        $startIdx = strpos($htmlContents, $startTag);
        if ($startIdx === false) {
            $strLog = sprintf('get author fail, can not find start_tag[%s]', $startTag);
            Log::warning($strLog);
            return false;
        }

        $startIdx += strlen($startTag);

        $endTag = '</li>';
        $endIdx = strpos($htmlContents, $endTag, $startIdx);
        if (false === $endIdx) {
            $strLog = sprintf('get author fail, can not find end_tag[%s]', $endTag);
            Log::warning($strLog);
            return false;
        }

        $contents = substr($htmlContents, $startIdx, $endIdx - $startIdx);
        $tmp = array();

        $matchRet = preg_match('/<strong>([^<]+)<\/strong>/', $contents, $tmp);
        // var_dump($matchRet); var_dump($tmp);
        if (!$matchRet) {
            $strLog = sprintf('match fail, contents[%s]', $contents);
            Log::warning($strLog);
            return false;
        }
        $author = $tmp[1];
        return $author;
    }

    protected static function _get_press($htmlContents) {
        $startTag = '<li>出版社：';
        $startIdx = strpos($htmlContents, $startTag);
        if ($startIdx === false) {
            $strLog = sprintf('get press fail, can not find start_tag[%s]', $startTag);
            Log::warning($strLog);
            return false;
        }

        $startIdx += strlen($startTag);

        $endTag = '</li>';
        $endIdx = strpos($htmlContents, $endTag, $startIdx);
        if (false === $endIdx) {
            $strLog = sprintf('get press fail, can not find end_tag[%s]', $endTag);
            Log::warning($strLog);
            return false;
        }

        $contents = substr($htmlContents, $startIdx, $endIdx - $startIdx);
        $tmp = array();

        $matchRet = preg_match('/<a .*>([^<]+)<\/a>/', $contents, $tmp);
        // var_dump($matchRet); var_dump($tmp);
        if (!$matchRet) {
            $strLog = sprintf('match fail, contents[%s]', $contents);
            Log::warning($strLog);
            return false;
        }
        $press = $tmp[1];
        return $press;
    }

    protected static function _get_ISBN($htmlContents) {
        $startTag = '<li>ISBN：';
        $startIdx = strpos($htmlContents, $startTag);
        if ($startIdx === false) {
            $strLog = sprintf('get ISBN fail, can not find start_tag[%s]', $startTag);
            Log::warning($strLog);
            return false;
        }

        $startIdx += strlen($startTag);

        $endTag = '</li>';
        $endIdx = strpos($htmlContents, $endTag, $startIdx);
        if (false === $endIdx) {
            $strLog = sprintf('get ISBN fail, can not find end_tag[%s]', $endTag);
            Log::warning($strLog);
            return false;
        }

        $contents = substr($htmlContents, $startIdx, $endIdx - $startIdx);
        $tmp = array();

        $matchRet = preg_match('/<strong>([^<]+)<\/strong>/', $contents, $tmp);
        // var_dump($matchRet); var_dump($tmp);
        if (!$matchRet) {
            $strLog = sprintf('match fail, contents[%s]', $contents);
            Log::warning($strLog);
            return false;
        }
        $isbn = $tmp[1];
        return $isbn;
    }


}
