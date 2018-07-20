<?php

// 配置读取

class Config {

    protected static $_is_init = false;

    // 数据库配置
    protected static $_db_conf = array();

    // 通用配置
    protected static $_config = array();

    protected static function _init() {
        if (self::$_is_init) {
            return ;
        }

        self::$_db_conf = include CONF_PATH . 'db.php';
        self::$_config  = include CONF_PATH . 'app_conf.php';

        self::$_is_init = true;
    }

    public static function getDBConf($dbName) {
        self::_init();
        if (!isset(self::$_db_conf[$dbName])) {
            throw new Exception('can not find db conf of "'.$dbName.'"', 1);
        }
        return self::$_db_conf[$dbName];
    }

    public static function getConfig($key, $defaultValue = '') {
        self::_init();
        if (isset(self::$_config[$key])) {
            return self::$_config[$key];
        }
        $strLog = 'get a not defined config "'.$key.'"';
        Log::warning($strLog);

        return $defaultValue;
    }
}

