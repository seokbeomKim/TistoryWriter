<?php
namespace tistory_writer;

/**
 */
class Logger
{
    public static function log($arg)
    {
        error_log("[TistoryWriter]" . $arg);
    }
}
