<?php
namespace tistory_writer;

/*
 * 티스토리 연동 시 사용하는 티스토리 API 관리 클래스
 */
class ApiManager
{
    public $access_token;

    public function __construct()
    {
        Logger::log("Initialize ApiManager");
    }
}
