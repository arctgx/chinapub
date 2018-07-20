<?php
// 日志打印库
class Log {

    const LEVEL_TRACE   = 1;
    const LEVEL_DEBUG   = 2;
    const LEVEL_NOTICE  = 4;
    const LEVEL_WARNING = 8;
    const LEVEL_FATAL   = 16;

    const LEVEL_ALL     = self::LEVEL_TRACE | self::LEVEL_DEBUG | self::LEVEL_NOTICE | self::LEVEL_WARNING | self::LEVEL_FATAL;
    const LEVEL_DEFAULT = self::LEVEL_NOTICE | self::LEVEL_WARNING | self::LEVEL_FATAL;

    const PREFIX_TRACE   = 'TRACE';
    const PREFIX_DEBUG   = 'DEBUG';
    const PREFIX_NOTICE  = 'NOTICE';
    const PREFIX_WARNING = 'WARNING';
    const PREFIX_FATAL   = 'FATAL';

    protected static $_log_level = 0;

    protected static $_log_file = '';

    public static function init($logFile, $logLevel = 0) {
        self::$_log_file = LOG_PATH. $logFile;
        if ($logLevel>0) {
            self::$_log_level = $logLevel & self::LEVEL_ALL;
        }
    }

    public static function setLogLevel($logLevel) {
        if ($logLevel>0) {
            self::$_log_level = $logLevel & self::LEVEL_ALL;
        }
    }

    public static function setLogFile($logFile) {
        self::$_log_file = LOG_PATH . $logFile;
    }

    public static function trace($strLog) {
        if (self::$_log_level & self::LEVEL_TRACE) {
            return self::_write_log($strLog, self::PREFIX_TRACE);
        }
        return 0;
    }

    public static function debug($strLog) {
        if (self::$_log_level & self::LEVEL_DEBUG) {
            return self::_write_log($strLog, self::PREFIX_DEBUG);
        }
        return 0;
    }

    public static function notice($strLog) {
        if (self::$_log_level & self::LEVEL_NOTICE) {
            return self::_write_log($strLog, self::PREFIX_NOTICE);
        }
        return 0;
    }

    public static function warning($strLog) {
        if (self::$_log_level & self::LEVEL_WARNING) {
            return self::_write_log($strLog, self::PREFIX_WARNING);
        }
        return 0;
    }

    public static function fatal($strLog) {
        if (self::$_log_level & self::LEVEL_FATAL) {
            return self::_write_log($strLog, self::PREFIX_FATAL);
        }
        return 0;
    }

    protected static function _write_log($strLog, $prefix) {
        $realLog = sprintf('[%s] [%s] %s', date('Y-m-d H:i:s'), $prefix, $strLog);
        return file_put_contents(self::$_log_file, $realLog."\n", FILE_APPEND);
    }

}

