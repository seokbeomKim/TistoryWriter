<?php
/**
 * PHP version 5
 *
 * @category Wordpress_Plugin
 * @package  Tistory_Writer
 * @author   Sukbeom Kim <sukbeom.kim@gmail.com>
 * @license  GPL v2
 * @version  GIT: 80eacec40cd5b8054447e83c9b8b286e505d4c69
 * @link     https://github.com/seokbeomKim/TistoryWriter
 */

namespace tistory_writer;

/**
 * 각 기능별 클래스 구현 위한 Abstract Class
 * @category Wordpress_Plugin
 * @package  Tistory_Writer
 * @author   Sukbeom Kim <sukbeom.kim@gmail.com>
 * @license  GPL v2
 * @version  Release: 0.1
 * @link     https://github.com/seokbeomKim/TistoryWriter
 */
class OptionManager
{
    public function __construct()
    {
        Logger::log("Initialize OptionManager");
    }

    public function getName()
    {
        return "OptionManager";
    }

    /**
     * 옵션 반환 함수
     *
     * @param string $name    옵션 이름
     * @param string $default 옵션 기본 값
     *
     * @return $name에 해당하는 옵션 값 반환
     */
    public function getOption($name)
    {
        TistoryWriter::checkSessionAndStart();
        $var = get_option($name);
        Logger::log("getOption: " . $name . " : " .$var);
        return $var;
    }

    /**
     * 옵션 반환 함수
     *
     * @param string $name  옵션 이름
     * @param string $value 설정하고자 하는 옵션값
     *
     * @return $name에 해당하는 옵션 값 반환
     */
    public function setOption($name, $value)
    {
        TistoryWriter::checkSessionAndStart();

        Logger::log("setOption: " . $name . " as " . $value);
        if (add_option($name, $value) == false) {
            update_option($name, $value);
        } else {
            Logger::log("add option completed");
        }


        if (!empty(get_option($name))) {
            Logger::log("update_option called" . get_option($name));
        } else {
            Logger::log("add_option called: " . $name . ', value = ' . $value);
        }
    }
}
