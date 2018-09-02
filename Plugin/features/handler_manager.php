<?php
namespace tistory_writer;

use const tistory_writer\REQUEST_KEY\REQUEST_SAVE_SETTINGS;
use const tistory_writer\REQUEST_KEY\HANDLER_SUFFIX;

/**
 * 플러그인 이벤트 처리 핸들러 관리 클래스
 */
class HandlerManager
{
    public $handlers;

    public function __construct()
    {
    	// Initiailize request handlers
        $this->handlers = array(
	        REQUEST_SAVE_SETTINGS => REQUEST_SAVE_SETTINGS . HANDLER_SUFFIX,
        );
    }

    public function handleRequest($requestName)
    {
        // Validate $redirectDef value
        if (array_key_exists($requestName, $this->handlers)) {
            $this->handlers[$requestName]();
        }
    }

	public static function REQUEST_SAVE_SETTINGS_HANDLER()
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
}
