<?php
/**
 * 플러그인에서 사용하는 모든 상수 정의
 */
namespace tistory_writer;

// @codingStandardsIgnoreStart

/*
 * ClassManager에서 각 기능 관련 객체에 접근하기 위한 Dictionary 키 값 정의
 */
define(__NAMESPACE__ . '\FEATURE_KEY\OPTION', 'OPTION');
define(__NAMESPACE__ . '\FEATURE_KEY\PAGE_LOADER', 'PAGE_LOADER');
define(__NAMESPACE__ . '\FEATURE_KEY\SCRIPT', 'SCRIPT');
define(__NAMESPACE__ . '\FEATURE_KEY\AUTH', 'AUTH');
define(__NAMESPACE__ . '\FEATURE_KEY\TISTORY_API', 'TISTORY_API');
define(__NAMESPACE__ . '\FEATURE_KEY\HANDLER', 'HANDLER');

/*
 * 연동 플러그인에서 사용하는 옵션 정의
 */
define(__NAMESPACE__ . '\OPTION_KEY\CLIENT_ID', 'CLIENT_ID');
define(__NAMESPACE__ . '\OPTION_KEY\SECRET_KEY', 'SECRET_KEY');
define(__NAMESPACE__ . '\OPTION_KEY\CALLBACK_URL', 'CALLBACK_URL');
define(__NAMESPACE__ . '\OPTION_KEY\BLOG_NAME', 'BLOG_NAME');
define(__NAMESPACE__ . '\OPTION_KEY\AUTH_KEY', 'AUTH_KEY');
define(__NAMESPACE__ . '\OPTION_KEY\ACCESS_TOKEN', 'ACCESS_TOKEN');

/*
 * 페이지 리다이렉션 시 상황 판단 위해 사용하는 상수 정의
 */
define(__NAMESPACE__ . '\REDIRECT_EVENT\SETTINGINFO', 'SETTINGINFO');
define(__NAMESPACE__ . '\REDIRECT_EVENT\SETTINGINFO_RESET', 'SETTINGINFO_RESET');
define(__NAMESPACE__ . '\REDIRECT_EVENT\SETTING_ACCESSCODE', 'SETTING_ACCESSCODE');

// @codingStandardsIgnoreEnd
