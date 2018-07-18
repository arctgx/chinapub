<?php

class DbManager {

    protected static $dbMap = array();

    public static function getDB($dbName) {
        if (!isset(self::$dbMap[$dbName])) {
            self::getPdo($dbName);
        }
        return self::$dbMap[$dbName];
    }

    protected static function getPdo($dbName) {
        $dbConf = config::getDBConf($dbName);
        $dsn = sprintf(
            "mysql:host=%s;port=%d;dbname=%s;charset=%s",
            $dbConf['host'], $dbConf['port'], $dbConf['db'], $dbConf['charset']
        );
        $tmpDB = new pdo($dsn, $dbConf['user'], $dbConf['pwd']);
        $tmpDB->query('set names utf8mb4');

        self::$dbMap[$dbName] = $tmpDB;
    }
}

