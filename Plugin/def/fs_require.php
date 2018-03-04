<?php

/*
 * fs_require.php: php 파일 로드
 *
 * Tistroy Writer 플러그인에 사용되는 파일 로드 관리 파일
 */

namespace tistory_writer;

if (!class_exists('\\tistory_writer\\OptionManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'option_manager.php');
}

if (!class_exists('\\tistory_writer\\PageManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'page_manager.php');
}

if (!class_exists('\\tistory_writer\\ClassManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'class_manager.php');
}

if (!class_exists('\\tistory_writer\\ScriptManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'script_manager.php');
}

if (!class_exists('\\tistory_writer\\ApiManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'api_manager.php');
}

if (!class_exists('\\tistory_writer\\AuthManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'auth_manager.php');
}

if (!class_exists('\\tistory_writer\\HandlerManager')) {
    require_once(PLUGIN_DIR . 'features' . DIRECTORY_SEPARATOR . 'handler_manager.php');
}

if (!class_exists('\\tistory_writer\\TistoryMetabox')) {
    require_once(PLUGIN_DIR . 'widgets' . DIRECTORY_SEPARATOR . 'TistoryMetabox.php');
}

/* Class name 'Logger' could be used in other plugins.
 */
if (!class_exists('\\tistory_writer\\Logger')) {
    require_once(PLUGIN_DIR . 'etc' . DIRECTORY_SEPARATOR . 'logger.php');
}
