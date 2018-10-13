<?php
namespace tistory_writer;

/*
 * 티스토리 연동 시 인증 토큰 관리하는 클래스 정의
 */
class AuthManager
{
    public $access_token;

    public function getAccessToken($return_url = null)
    {
        if (method_exists('\\tistory_writer\\Logger', 'log')) {
            Logger::log("getAccessToken is called with " . $return_url);
        }

        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);

        $authorization_code = $optionMgr->getOption(OPTION_KEY\AUTH_KEY);

        $client_id = $optionMgr->getOption(OPTION_KEY\CLIENT_ID);
        $client_secret = $optionMgr->getOption(OPTION_KEY\SECRET_KEY);
        $redirect_uri = $optionMgr->getOption(OPTION_KEY\CALLBACK_URL);
        $grant_type = 'authorization_code';

        $url = 'https://www.tistory.com/oauth/access_token/?code=' . $authorization_code .
                    '&client_id=' . $client_id . '&client_secret=' . $client_secret .
                    '&redirect_uri=' . urlencode($redirect_uri) . '&grant_type=' . $grant_type;

        $access_token = file_get_contents($url);
        $final_token = str_replace("access_token=", "", $access_token);
        $optionMgr->setOption(OPTION_KEY\ACCESS_TOKEN, $final_token);
    }

    public function getAuthToken()
    {
        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);

        $getUrl = 'https://www.tistory.com/oauth/authorize/' .'?';
        $getUrl .= 'client_id=' . $optionMgr->getOption(OPTION_KEY\CLIENT_ID) . '&';
        $getUrl .= 'redirect_uri=' . $optionMgr->getOption(OPTION_KEY\CALLBACK_URL) . '&response_type=token';

        wp_redirect($getUrl);
    }
}
