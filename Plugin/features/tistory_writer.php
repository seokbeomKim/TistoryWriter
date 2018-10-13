<?php
namespace tistory_writer;

//region A list of constants
use const tistory_writer\FEATURE_KEY\OPTION;
use const tistory_writer\FEATURE_KEY\PAGE_LOADER;
use const tistory_writer\FEATURE_KEY\SCRIPT;
use const tistory_writer\FEATURE_KEY\TISTORY_API;
use const tistory_writer\OPTION_KEY\ACCESS_TOKEN;
use const tistory_writer\OPTION_KEY\BLOG_NAME;
use const tistory_writer\OPTION_KEY\CLIENT_ID;
use const tistory_writer\OPTION_KEY\EXPIRE_TIMESTAMP;
use const tistory_writer\OPTION_KEY\REDIRECT_URI;
use const tistory_writer\OPTION_KEY\SECRET_KEY;
use const tistory_writer\OPTION_KEY\SELECTED_BLOG;
use const tistory_writer\PAGE_TYPE\PAGE_META_BOX;
use const tistory_writer\PAGE_TYPE\PAGE_SETTING;
use const tistory_writer\STRINGS\TITLE_META_BOX;

//endregion

/**
 * Plugin main class
 *
 * @category Wordpress_Plugin
 * @package  Tistory_Writer
 * @author   Sukbeom Kim <sukbeom.kim@gmail.com>
 * @license  MIT License
 * @version  Release: 1.0.7
 * @link     https://github.com/seokbeomKim/TistoryWriter
 */
class TistoryWriter
{
	private static $instance;

	private $class_mgr;
	private $metaBox;

	public function __construct()
	{
		$this->class_mgr = new ClassManager();
		$this->metaBox = new TistoryMetaBox();
	}

	public static function init()
	{
		if (!self::$instance) {
			self::$instance = new TistoryWriter();
		}
		return self::$instance;
	}

	//region action:activate / deactivate handler
	/**
	 * 플러그인 활성화 후킹 함수
	 *
	 * @static
	 * @hook   register_activation_hook
	 * @return void
	 */
	public static function activatePlugin()
	{
		if (method_exists('\\tistory_writer\\Logger', 'log')) {
			Logger::log("TistoryWriter::Activate plugin.");
		}
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
		if (method_exists('\\tistory_writer\\Logger', 'log')) {
			Logger::log("TistoryWriter::Deactivate plugin.");
		}
	}
	//endregion
	//region action:admin_menu handler
	public static function addAdminOptionMenu()
	{
		self::$instance->class_mgr->getManager(PAGE_LOADER)->addOptionPage();
	}
	//endregion
	//region action:init handler
	public static function initSession()
	{
		if (!session_id())
			session_start();
	}
	//endregion
	//region action: admin_enqueue_scripts handler
	public static function initStyle($hook = null)
	{
		if ($hook == 'settings_page_tistory_writer') {
			self::loadFiles(PAGE_SETTING);
		}
		else {
			self::loadFiles(PAGE_META_BOX);
		}
	}
	//endregion
	//region action:add_meta_boxes handler
	public static function addMetaBoxes()
	{
		add_meta_box('tw_meta_box', TITLE_META_BOX,
			array(self::$instance->metaBox, 'getContent'), 'post', 'normal', "high");
	}
	//endregion

	public static function checkSessionAndStart()
	{
		if (!session_id()) {
			session_start();
		}
	}

	/**
	 * Return managers used in plugins
	 *
	 * <code>
	 * $this->features = array (
	 * FEATURE_KEY\OPTION => new OptionManager(),
	 * FEATURE_KEY\PAGE_LOADER => new PageManager(),
	 * FEATURE_KEY\SCRIPT => new ScriptManager(),
	 * FEATURE_KEY\AUTH => new AuthManager(),
	 * FEATURE_KEY\TISTORY_API => new ApiManager(),
	 * FEATURE_KEY\HANDLER => new HandlerManager(),
	 * );
	 * </code>
	 *
	 * @param $managerName string
	 *
	 * @return ApiManager|AuthManager|ClassManager|HandlerManager|OptionManager|PageManager|ScriptManager|RequestManager
	 */
	public static function getManager($managerName)
	{
		return self::$instance->class_mgr->getManager($managerName);
	}

	public static function loadFiles($pageType)
	{
		self::$instance->class_mgr->getManager(SCRIPT)->loadFiles($pageType);
	}

	//region post:insert/edit/update
	public static function postUpdate()
	{
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		$requestMgr->postUpdate();
	}

	public static function editPost()
	{
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		$requestMgr->editPost();
	}

	public static function insertPost($post_id, $post, $update)
	{
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		$requestMgr->insertPost($post_id, $post, $update);
	}
	//endregion

	//region GET/POST request handlers
	public static function getMetaBoxData()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->getMetaBoxData());
	}

	public static function getUrlForAccessToken()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->getUrlForAccessToken());
	}

	public static function saveSettings()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->saveSettings());
	}

	public static function changeSelectedBlog()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->changeSelectedBlog());
	}

	public static function requestAccessCode()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->requestAccessCode());
	}

	public static function requestAccessCodeWithAuth()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->requestAccessCodeWithAuth());
	}

	public static function requestBlogUrl()
	{
		check_ajax_referer( 'tistory_writer' );
		$requestMgr = self::getManager(FEATURE_KEY\REQUEST);
		wp_die($requestMgr->requestBlogUrl());
	}
	//endregion
}