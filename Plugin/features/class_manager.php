<?php
namespace tistory_writer;

/*
 * 티스토리 연동 플러그인에 사용되는 기능 관련 관리 객체
 * Plugin 로드 시 ClassManager를 통해서 필요한 객체를 모두 관리/접근한다.
 */
class ClassManager
{
    public $features;

    public function __construct()
    {
        $this->features = array (
            FEATURE_KEY\OPTION => new OptionManager(),
            FEATURE_KEY\PAGE_LOADER => new PageManager(),
            FEATURE_KEY\SCRIPT => new ScriptManager(),
            FEATURE_KEY\AUTH => new AuthManager(),
            FEATURE_KEY\TISTORY_API => new ApiManager(),
            FEATURE_KEY\HANDLER => new HandlerManager(),
        );
    }

    public function getManager($mgr_name)
    {
        Logger::log("getManager: " . $mgr_name);
        return $this->features[$mgr_name];
    }
}
