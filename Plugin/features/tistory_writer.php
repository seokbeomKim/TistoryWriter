<?php

namespace tistory_writer;

use const tistory_writer\FEATURE_KEY\OPTION;
use const tistory_writer\FEATURE_KEY\TISTORY_API;
use const tistory_writer\OPTION_KEY\ACCESS_TOKEN;
use const tistory_writer\OPTION_KEY\BLOG_NAME;
use const tistory_writer\OPTION_KEY\CLIENT_ID;
use const tistory_writer\OPTION_KEY\REDIRECT_URI;
use const tistory_writer\OPTION_KEY\SECRET_KEY;
use const tistory_writer\OPTION_KEY\SELECTED_BLOG;

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

	public static function registerSession()
	{
		if (!session_id()) {
			session_start();
		}
	}

	public static function initPlugin()
	{
		self::checkGetMethodValue();
	}

	public static function initStyle($hook = null)
	{

		if ($hook == 'settings_page_tistory_writer') {
			self::loadFiles('SETTING');
		}
		else {
			self::loadFiles('METABOX');
		}

		// else
		return;
	}

	public static function checkSessionAndStart()
	{
		if (!session_id()) {
			session_start();
		}
	}

	public static function addMetaboxes()
	{
		add_meta_box('tw_meta_box', '티스토리 연동',
			array(self::$instance->metabox, 'getContent'), 'post', 'normal', "high");
	}

	/**
	 * 사용자 버튼에 의해 Get Value가 전달됐는지 확인
	 */
	public static function checkGetMethodValue()
	{
		self::checkSessionAndStart();

		$current_url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		if (isset($_GET['code'])) {
			self::$instance->option_mgr->setOption($_GET['code'], \tistory_writer\OPTION_KEY\AUTH_KEY);

			// ACCESS CODE 갱신

		}
	}

	public static function addAdminOptionMenu()
	{
		self::$instance->page_mgr->addOptionPage();
	}

	public static function loadFiles($pageType)
	{
		self::$instance->script_mgr->loadFiles($pageType);
	}

	public static function getManager($managerName)
	{
		return self::$instance->class_mgr->getManager($managerName);
	}

	public static function handlerSubmit()
	{
		$handlerMgr = self::getManager(FEATURE_KEY\HANDLER);

		// Validate in handle method
		$redirectDef = wp_kses_post($_POST['redirect_def']);
		$handlerMgr->handle($redirectDef);
	}

	public static function redirectPage()
	{
		global $pagenow;
		exit(wp_redirect(admin_url('options-general.php?page=tistory_writer')));
	}

	public static function getMetaboxData()
	{
		$apiMgr   = self::getManager(FEATURE_KEY\TISTORY_API);
		$postId = "";
		$title = $_POST['title'];

		$categories = $apiMgr->getCategoryList();

		$postInfo = $apiMgr->getPostInfoWithTitle($title, $postId);

		if (!is_null($postInfo)) {
			$detail = $apiMgr->getDetailInfoWithPostId( $postInfo['id'] );

			$rvalue = array (
				'detail' => $detail,
				'category' => $categories,
			);
		}
		else {
			$rvalue = array (
				'detail' => null,
				'category' => $categories,
			);
		}

		wp_die(json_encode($rvalue));
	}

	public static function postUpdate()
	{
		if (isset($_POST['post_title']) && isset($_POST['content'])) {
			$apiMgr     = self::getManager(FEATURE_KEY\TISTORY_API);

			$title      = stripslashes(wp_kses_post($_POST['post_title']));
			$content    = stripslashes(wp_kses_post($_POST['post_content']));
			$content    = nl2br($content);
			$tag        = wp_kses_post($_POST['input_tag']);

			$category_id    = isset($_POST['select_category']) ? wp_kses_post($_POST['select_category']) : "";
			$visibility     = isset($_POST['select_visibility']) ? wp_kses_post($_POST['select_visibility']) : "0";
			$isAllowComment = isset($_POST['checkAllowComment']) ? wp_kses_post($_POST['checkAllowComment']) : "0";

			$apiMgr->insertPost($title, $content, $visibility, $category_id, "", $isAllowComment, $tag);
		}
	}

	public static function editPost()
	{
		if (isset($_POST['post_title']) && isset($_POST['content'])) {
			$apiMgr     = self::getManager(FEATURE_KEY\TISTORY_API);

			$title      = stripslashes(wp_kses_post($_POST['post_title']));
			$content    = stripslashes(wp_kses_post($_POST['post_content']));
			$content    = nl2br($content);
			$tag        = wp_kses_post($_POST['input_tag']);

			$category_id    = isset($_POST['select_category']) ? wp_kses_post($_POST['select_category']) : "";
			$visibility     = isset($_POST['checkMakePublic']) ? wp_kses_post($_POST['checkMakePublic']) : "0";
			$isAllowComment = isset($_POST['checkAllowComment']) ? wp_kses_post($_POST['checkAllowComment']) : "0";
			$postId         = wp_kses_post($_POST['postId']);

			if (!wp_is_post_autosave($postId)) {
				$apiMgr->updatePost($title, $content, $visibility, $category_id, "", $isAllowComment, $tag, $postId);
			}
		}
	}

	public static function decodeCharacters($data)
	{
		return mb_convert_encoding($data, 'UTF-8', 'HTML-ENTITIES');
	}

	public static function saveSettings()
	{
		check_ajax_referer( 'tistory_writer' );

		$optionMgr = self::getManager(FEATURE_KEY\OPTION);

		$optionMgr->setOption(CLIENT_ID, $_POST['client_id']);
		$optionMgr->setOption(SECRET_KEY, $_POST['secret_key']);
		$optionMgr->setOption(BLOG_NAME, $_POST['blog_name']);

		$apiMgr = self::getManager(TISTORY_API);
		$blogInfo = $apiMgr->getBlogInformation();

		wp_die(json_encode($blogInfo));
	}

	public static function changeSelectedBlog()
	{
		check_ajax_referer( 'tistory_writer' );

		$optionMgr = self::getManager(OPTION);
		$optionMgr->setOption(SELECTED_BLOG, $_POST['selected_blog']);

		wp_die("VALUE");
	}

	public static function requestAccessCode()
	{
		check_ajax_referer( 'tistory_writer' );

		$optionMgr = self::getManager(FEATURE_KEY\OPTION);
		$optionMgr->setOption(ACCESS_TOKEN, $_POST['access_code']);

		$blogInfo = null;

		// Access code 갱신 요청 시, 클라이언트 페이지에 블로그 정보로 갱신할 블로그 주소를 송신한다.
		$apiMgr = self::getManager(TISTORY_API);
		$blogInfo = $apiMgr->getBlogInformation();

		wp_die(json_encode($blogInfo));
	}

	public static function requestAccessCodeWithAuth()
	{
		check_ajax_referer( 'tistory_writer' );

		$optionMgr = self::getManager(FEATURE_KEY\OPTION);
		$authorization_code = $_POST['auth_code'];

		$client_id = $optionMgr->getOption(CLIENT_ID);
		$client_secret = $optionMgr->getOption(SECRET_KEY);
		$redirect_uri = $optionMgr->getOption(REDIRECT_URI);
		$grant_type = 'authorization_code';

		$url = 'https://www.tistory.com/oauth/access_token/?code=' . $authorization_code .
		       '&client_id=' . $client_id . '&client_secret=' . $client_secret .
		       '&redirect_uri=' . urlencode($redirect_uri) . '&grant_type=' . $grant_type;

		$access_token = ($url);

		$rvalue = str_replace('access_token=', '', $access_token);
		$optionMgr->setOption(ACCESS_TOKEN, $rvalue);

		wp_die($rvalue);
	}

	public static function requestBlogUrl()
	{
		check_ajax_referer( 'tistory_writer' );

		$apiMgr = self::getManager(TISTORY_API);
		$blogInfo = $apiMgr->getBlogInformation();

		if (is_null($blogInfo)) {
			wp_die("FAIL");
		}

		foreach ($blogInfo as $blog) {
			if ($blog->name == $_POST['blog_name']) {
				$rvalue = array($blog->title, $blog->url);
				wp_die(json_encode($rvalue));
				break;
			}
		}
		wp_die("FAIL");
	}

	/**
	 * 사용자가 포스팅한 글을 티스토리에 업데이트한다.
	 */
	public static function insertPost($post_id, $post, $update)
	{
		$apiMgr = self::getManager(FEATURE_KEY\TISTORY_API);
		$flag   = isset($_POST['turnIntegratationOff']);

		if (!$flag && isset($_POST['postId'])) {
			if (strpos($post->name, 'autosave') != true) {
				$ti = (int)wp_kses_post($_POST['postId']);
				if ($ti <= 0) {
					self::postUpdate();
				} else {
					self::editPost();
				}
			}
		} else {
			if (method_exists('\\tistory_writer\\Logger', 'log')) {
				Logger::log("insertPost, 연동기능 임시 해제");
			}
		}
	}
}