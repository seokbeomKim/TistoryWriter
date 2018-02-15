<?php

/*
 * fs_require.php: php 파일 로드
 *
 * Tistroy Writer 플러그인에 사용되는 파일 로드 관리 파일
 */

namespace tistory_writer;

require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'option_manager.php');
require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'page_manager.php');
require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'class_manager.php');
require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'script_manager.php');
require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'api_manager.php');
require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'auth_manager.php');
require_once(PLUGIN_DIR . '\features' . DIRECTORY_SEPARATOR . 'handler_manager.php');
require_once(PLUGIN_DIR . '\widgets' . DIRECTORY_SEPARATOR . 'TistoryMetabox.php');

require_once(PLUGIN_DIR . '\etc' . DIRECTORY_SEPARATOR . 'logger.php');
