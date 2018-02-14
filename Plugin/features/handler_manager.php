<?php
namespace tistory_writer;

/**
 * 플러그인 이벤트 처리 핸들러 관리 클래스
 */
class HandlerManager
{
    public $handlers;

    public function __construct()
    {
        $this->handlers = array (
            REDIRECT_EVENT\SETTINGINFO => array($this, 'handlerSettingInfo'),
            REDIRECT_EVENT\SETTINGINFO_RESET => array($this, 'handlerSettingInfoReset'),
            REDIRECT_EVENT\SETTING_ACCESSCODE => array($this, 'handlerAccessCode'),
        );
    }

    public function handle($redirectDef)
    {
        Logger::log("handle:" . $redirectDef);
        $this->handlers[$redirectDef]();
    }

    /**
     * 사용자가 설정창에서 #2 설정단계를 SUbmit했을 경우에 호출되는 핸들러
     */
    public function handlerSettingInfo()
    {
        Logger::log("HandlerManager::handlerSettingInfo()");
        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);

        $callback_uri_temp = get_admin_url() . 'options-general.php?page=tistory_writer';

        /* 옵션 설정 */
        Logger::log("client id = " . $_POST['client_id'] . ", secret_key = " . $_POST['secret_key']);
        Logger::log("blog_name = " . $_POST['blogname']);
        $optionMgr->setOption(OPTION_KEY\CLIENT_ID, $_POST['client_id']);
        $optionMgr->setOption(OPTION_KEY\SECRET_KEY, $_POST['secret_key']);
        $optionMgr->setOption(OPTION_KEY\BLOG_NAME, $_POST['blogname']);
        $optionMgr->setOption(OPTION_KEY\CALLBACK_URL, $callback_uri_temp);

        wp_safe_redirect(get_admin_url() . "/options-general.php?page=tistory_writer");
    }

    /**
     * 사용자가 Access Token을 얻어왔을 때 처리 핸들러
     */
    public function handlerAccessCode()
    {
        Logger::log("handlerAccessCode is called: " . $_POST['access_code'] . ", " . OPTION_KEY\ACCESS_TOKEN);
        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
        $optionMgr->setOption(OPTION_KEY\ACCESS_TOKEN, $_POST['access_code']);

        Logger::log("handlerAccessCode is called: " . $_POST['access_code']);

        wp_safe_redirect(get_admin_url() . "/options-general.php?page=tistory_writer");
    }

    public function handlerSettingInfoReset()
    {
        Logger::log("HandlerManager::handlerSettingInfoReset()");
        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);

        $optionMgr->setOption(OPTION_KEY\AUTH_KEY, "");
        $optionMgr->setOption(OPTION_KEY\ACCESS_TOKEN, "");
    }
}
