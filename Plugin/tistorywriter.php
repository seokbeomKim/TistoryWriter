<?php
/**
 * Plugin Name: Tistory Writer
 * Plugin URI: https://github.com/seokbeomKim/TistoryWriter/
 * Description: 티스토리, 워드프레스 연동 플러그인
 * Author: 김석범 (Sukbeom Kim)
 * Author URI: https://chaoxifer.tistory.com
 * Contributors:None
 * Version: 1.0.0
 * Text Domain: TistoryWriter

 * The MIT License
 *
 *  Copyright (c) 2018 Sukbeom Kim
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

namespace tistory_writer;

define(__NAMESPACE__ . '\VERSION', '0.1');
define(__NAMESPACE__ . '\MAIN_URL', $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=tistory_writer');
define(__NAMESPACE__ . '\PLUGIN_DIR', plugin_dir_path(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_FILE_URL', plugins_url());

define(__NAMESPACE__ . '\PAGE_DIR', PLUGIN_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'pages');
define(__NAMESPACE__ . '\CSS_DIR', PLUGIN_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css');
define(__NAMESPACE__ . '\PLUGIN_PREFIX', 'TistoryWriter');
define(__NAMESPACE__ . '\PLUGIN_MENU_SLUG', 'tistory-writer-setting');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'constants.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'fs_require.php');

defined('ABSPATH') or die('No script kiddies please!');

/* 플러그인 Option 메뉴에 추가 */
add_action('admin_menu', array('tistory_writer\TistoryWriter', 'addAdminOptionMenu'));

add_action('init', array('tistory_writer\TistoryWriter', 'registerSession'));

/* 플러그인에서 사용하는 css, script 추가 */
add_action('admin_init', array('tistory_writer\TistoryWriter', 'initPlugin'));

/* 플러그인에서 submit 하는 이벤트 처리 핸들러 등록 */
add_action('admin_post_submit-tw-info', array('tistory_writer\TistoryWriter', 'handlerSubmit'));

add_action('check_tistory_auth', array('tistory_writer\TistoryWriter', 'checkAuthCode'));

add_action('add_meta_boxes', array('tistory_writer\TistoryWriter', 'addMetaBoxes'));

add_action('wp_insert_post', array('tistory_writer\TistoryWriter', 'insertPost'), 10, 3);

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
    private $metabox;

    public static $count = 0;

    /**
     * 싱글톤 객체 리턴 함수
     * @return 싱글톤 객체
     */
    public static function init()
    {
        if (!self::$instance) {
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
        $this->metabox = new TistoryMetabox();
    }

    public static function checkAuthCode($code)
    {
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

    public static function addMetaboxes()
    {
        add_meta_box('tw_meta_box', '티스토리 연동', array(self::$instance->metabox, 'getContent'), 'post', 'normal', "high");
    }

    /**
     * 사용자 버튼에 의해 Get Value가 전달됐는지 확인
     */
    public static function checkGetMethodValue()
    {
        self::checkSessionAndStart();

        $current_url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        if (isset($_GET['code'])) {
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
        $handlerMgr = self::getManager(FEATURE_KEY\HANDLER);
        $handlerMgr->handle($_POST['redirect_def']);
    }

    public static function redirectPage()
    {
        global $pagenow;
        exit(wp_redirect(admin_url('options-general.php?page=tistory_writer')));
    }

    public static function postUpdate()
    {
        if (isset($_POST['post_title']) && isset($_POST['content'])) {
            $apiMgr = self::getManager(FEATURE_KEY\TISTORY_API);
            Logger::log("content is " . $_POST['content']);


            // 글 정보
            $title = stripslashes($_POST['post_title']);
            $content = stripslashes($_POST['post_content']);
            $category_id = $_POST['select_category'];
            $visibility = $_POST['select_visibility'];
            $isProtected = isset($_POST['checkProtected']) ? true : false;
            $isAllowComment = isset($_POST['checkAllowComment']) ? true : false;
            $tag = $_POST['input_tag'];

            $apiMgr->insertPost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag);
        }
    }

    public static function editPost()
    {
        if (isset($_POST['post_title']) && isset($_POST['content'])) {
            $apiMgr = self::getManager(FEATURE_KEY\TISTORY_API);

            // 글 정보
            $title = stripslashes($_POST['post_title']);
            $content = stripslashes($_POST['post_content']);
            $category_id = $_POST['select_category'];
            $visibility = $_POST['select_visibility'];
            $isProtected = isset($_POST['checkProtected']) ? true : false;
            $isAllowComment = isset($_POST['checkAllowComment']) ? true : false;
            $tag = $_POST['input_tag'];
            $postId = $_POST['postId'];

            $apiMgr->updatePost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag, $postId);
        }
    }

    public static function decodeCharacters($data)
    {
        return mb_convert_encoding($data, 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     * 사용자가 포스팅한 글을 티스토리에 업데이트한다.
     */
    public static function insertPost($post_id, $post, $update)
    {
        $apiMgr = self::getManager(FEATURE_KEY\TISTORY_API);
        $flag = isset($_POST['turnIntegratationOff']);

        if (!$flag && isset($_POST['postId'])) {
            if (self::$count == 0) {
                self::$count++;
                if ($_POST['postId'] == -1) {
                    /* 새로운 포스트 업로드 */
                    self::postUpdate();
                } else {
                    self::editPost();
                }
            }
        } else {
            Logger::log("insertPost, 연동기능 임시 해제");
        }
    }

    public static function resetCount()
    {
        self::$count = 0;
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
