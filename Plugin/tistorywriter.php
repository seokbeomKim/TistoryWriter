<?php
/**
 * Plugin Name: Tistory Writer
 * Plugin URI: https://github.com/seokbeomKim/TistoryWriter
 * Description: 티스토리, 워드프레스 연동 플러그인
 * Author: 김석범 (Sukbeom Kim)
 * Author URI: https://chaoxifer.tistory.com
 * Contributors:None
 * Version: 1.0.0
 * Text Domain: TistoryWriter
 * Domain Path: /languages/

 *
 *  Copyright 2018 Sukbeom Kim
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions andlimitations under the License.
 *
 * @package TistoryWriter
 * @category Core
 * @author René Hermenau, Ilgıt Yıldırım
 */

namespace tistory_writer;

define(__NAMESPACE__ . '\VERSION', '0.1');
define(__NAMESPACE__ . '\MAIN_URL', $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=tistory_writer');
define(__NAMESPACE__ . '\PLUGIN_DIR', plugin_dir_path(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_FILE_URL', plugins_url());

define(__NAMESPACE__ . '\PAGE_DIR', PLUGIN_DIR . '\assets\pages');
define(__NAMESPACE__ . '\CSS_DIR', PLUGIN_DIR . '\assets\css');
define(__NAMESPACE__ . '\PLUGIN_PREFIX', 'TistoryWriter');
define(__NAMESPACE__ . '\PLUGIN_MENU_SLUG', 'tistory-writer-setting');

require_once(dirname(__FILE__) . '\def\constants.php');
require_once(dirname(__FILE__) . '\def\fs_require.php');

defined('ABSPATH') or die('No script kiddies please!');

/* 플러그인 Option 메뉴에 추가 */
add_action('admin_menu', array('tistory_writer\TistoryWriter', 'addAdminOptionMenu'));

add_action('init', array('tistory_writer\TistoryWriter', 'registerSession'));

/* 플러그인에서 사용하는 css, script 추가 */
add_action('admin_init', array('tistory_writer\TistoryWriter', 'initPlugin'));

/* 플러그인에서 submit 하는 이벤트 처리 핸들러 등록 */
add_action('admin_post_submit-tw-info', array('tistory_writer\TistoryWriter', 'handlerSubmit'));

add_action('check_tistory_auth', array('tistory_writer\TistoryWriter', 'checkAuthCode'));

/**
 * 플러그인 메인 클래스
 *
 * @category Wordpress_Plugin
 * @package  Tistory_Writer
 * @author   Sukbeom Kim <sukbeom.kim@gmail.com>
 * @license  GPL v2
 * @version  Release: 0.1
 * @link     https://github.com/seokbeomKim/TistoryWriter
 */
class TistoryWriter
{
    private static $instance;
    private static $mutex;

    private $class_mgr;
    private $page_mgr;
    private $script_mgr;
    private $option_mgr;

    /**
     * 싱글톤 객체 리턴 함수
     * @return 싱글톤 객체
     */
    public static function init()
    {
        if (!self::$instance) {
            load_plugin_textdomain(PLUGIN_PREFIX, false, PLUGIN_DIR, 'languages');
            self::$instance = new TistoryWriter();
        }
        return self::$instance;
    }

    /**
     * 클래스 생성자
     *
     * @return 객체 반환
     */
    public function __construct()
    {
        $this->class_mgr = new ClassManager();
        $this->page_mgr = $this->class_mgr->getManager(FEATURE_KEY\PAGE_LOADER);
        $this->script_mgr = $this->class_mgr->getManager(FEATURE_KEY\SCRIPT);
        $this->option_mgr = $this->class_mgr->getManager(FEATURE_KEY\OPTION);
    }

    public static function checkAuthCode($code)
    {
        Logger::log("checkAuthCode is called." . $code . ", url = " . get_admin_url());
        self::$instance->option_mgr->setOption(OPTION_KEY\AUTH_KEY, $code);
        wp_safe_redirect(get_admin_url() . "/options-general.php?page=tistory_writer");
    }

    /**
     * 플러그인 활성화 후킹 함수
     *
     * @static
     * @hook   register_activation_hook
     * @return void
     */
    public static function activatePlugin()
    {
        Logger::log("TistoryWriter::Activate plugin.");
    }

    /**
     * 플러그인 비활성화 후킹 함수
     *
     * @static
     * @hook   register_deactivation_hook
     * @return void
     */
    public static function deactivatePlugin()
    {
        Logger::log("TistoryWriter::Deactivate plugin.");
    }

    public static function registerSession()
    {
        if (!session_id()) {
            session_start();
        }
    }

    public static function initPlugin()
    {
        self::loadFiles();
        self::checkGetMethodValue();
    }

    public static function checkSessionAndStart()
    {
        if (!session_id()) {
            session_start();
        }
    }
    /**
     * 사용자 버튼에 의해 Get Value가 전달됐는지 확인
     */
    public static function checkGetMethodValue()
    {
        self::checkSessionAndStart();

        $current_url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        Logger::log($current_url);

        if (!empty($_GET['code'])) {
            do_action('check_tistory_auth', $_GET['code']);
        }
    }

    public static function addAdminOptionMenu()
    {
        self::$instance->page_mgr->addOptionPage();
    }

    public static function loadFiles()
    {
        self::$instance->script_mgr->loadFiles();
    }

    public static function getManager($managerName)
    {
        return self::$instance->class_mgr->getManager($managerName);
    }

    public static function handlerSubmit()
    {
        Logger::log('handleSubmitInformation: ' . $_POST['redirect_def']);
        $handlerMgr = self::getManager(FEATURE_KEY\HANDLER);
        $handlerMgr->handle($_POST['redirect_def']);
        // self::redirectPage();
    }

    public static function redirectPage()
    {
        global $pagenow;

        Logger::log("redirectPage's pageNow : " . $pagenow . ", post value = " . $_POST['redirect_def']);

        exit(wp_redirect(admin_url('options-general.php?page=tistory_writer')));
    }
}

add_action('plugins_loaded', array('tistory_writer\TistoryWriter', 'init'));

/*
 * 1. 워드프레스 api로 인한 코딩규칙 경고 무시
 * 2. 플러그인 Active/Deactive 후커 등록
 */
// @codingStandardsIgnoreStart
register_activation_hook(__FILE__, array('tistory_writer\TistoryWriter', 'activatePlugin'));
register_deactivation_hook(__FILE__, array('tistory_writer\TistoryWriter', 'deactivatePlugin'));
// @codingStandardsIgnoreEnd
