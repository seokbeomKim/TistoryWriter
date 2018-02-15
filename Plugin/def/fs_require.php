<?php

/*
 * fs_require.php: php 파일 로드
 *
 * Tistroy Writer 플러그인에 사용되는 파일 로드 관리 파일
 */

namespace tistory_writer;

require_once(PLUGIN_DIR . '\features\option_manager.php');
require_once(PLUGIN_DIR . '\features\page_manager.php');
require_once(PLUGIN_DIR . '\features\class_manager.php');
require_once(PLUGIN_DIR . '\features\script_manager.php');
require_once(PLUGIN_DIR . '\features\api_manager.php');
require_once(PLUGIN_DIR . '\features\auth_manager.php');
require_once(PLUGIN_DIR . '\features\handler_manager.php');
require_once(PLUGIN_DIR . '\widgets\TistoryMetabox.php');

require_once(PLUGIN_DIR . '\etc\logger.php');
