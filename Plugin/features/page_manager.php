<?php /** @noinspection PhpIncludeInspection */

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

class PageManager
{
    public $setting_page;

    /**
    * 옵션 페이지 내 플러그인 메뉴 추가
    */
    public function addOptionPage()
    {
        if (current_user_can('manage_options')) {
            add_options_page('Tistory Writer',
	            'Tistory Writer', 'manage_options', 'tistory_writer',
	            array($this, 'getAdminSettingPage'));
        }
    }

    public function getAdminSettingPage()
    {
        $pagePath = PAGE_DIR . DIRECTORY_SEPARATOR . 'setting.php';
        require_once($pagePath);
    }

    public function getUserSettingPage()
    {
        $pagePath = PAGE_DIR . DIRECTORY_SEPARATOR. 'settingUser.php';
        require_once($pagePath);
    }

    public function getMetaBoxPage()
    {
        $pagePath = PAGE_DIR . DIRECTORY_SEPARATOR . 'metabox_tw_widget.php';
        require_once($pagePath);
    }
}
