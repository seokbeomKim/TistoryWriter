<?php
/**
 * Plugin Name: Tistory Writer
 * Plugin URI: https://github.com/seokbeomKim/TistoryWriter/
 * Description: 티스토리, 워드프레스 연동 플러그인
 * Author: 김석범 (Sukbeom Kim)
 * Author URI: https://chaoxifer.tistory.com
 * Contributors:None
 * Version: 1.0.6
 * Text Domain: TistoryWriter

 * The MIT License
 *
 *  Copyright (c) 2018 Sukbeom Kim
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

namespace tistory_writer;

defined('ABSPATH') or die('No script kiddies please!');

define(__NAMESPACE__ . '\VERSION',          '1.0.6');
define(__NAMESPACE__ . '\PLUGIN_PREFIX',    'TistoryWriter');
define(__NAMESPACE__ . '\PLUGIN_MENU_SLUG', 'tistory-writer-setting');
define(__NAMESPACE__ . '\MAIN_URL',         $_SERVER['HTTP_HOST'] . '/wp-admin/options-general.php?page=tistory_writer');
define(__NAMESPACE__ . '\PLUGIN_URL',       plugin_dir_url(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_FILE_URL',  plugins_url());
define(__NAMESPACE__ . '\PLUGIN_DIR',       plugin_dir_path(__FILE__));
define(__NAMESPACE__ . '\PAGE_DIR',         PLUGIN_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'pages');
define(__NAMESPACE__ . '\CSS_DIR',          PLUGIN_DIR . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css');

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'constants.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'fs_require.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'errors.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'requests.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'def' . DIRECTORY_SEPARATOR . 'strings.php');

add_action('admin_menu',                        array('tistory_writer\TistoryWriter', 'addAdminOptionMenu'));
add_action('admin_enqueue_scripts',             array('tistory_writer\TistoryWriter', 'initStyle'));
add_action('add_meta_boxes',                    array('tistory_writer\TistoryWriter', 'addMetaBoxes'));
add_action('init',                              array('tistory_writer\TistoryWriter', 'initSession'));
add_action('plugins_loaded',                    array('tistory_writer\TistoryWriter', 'init'));
add_action('wp_insert_post',                    array('tistory_writer\TistoryWriter', 'insertPost'), 10, 3);
add_action('wp_ajax_changeSelectedBlog',        array('tistory_writer\TistoryWriter', 'changeSelectedBlog') );
add_action('wp_ajax_getMetaBoxData',            array('tistory_writer\TistoryWriter', 'getMetaBoxData') );
add_action('wp_ajax_getUrlForAccessToken',      array('tistory_writer\TistoryWriter', 'getUrlForAccessToken') );
add_action('wp_ajax_requestAccessCode',         array('tistory_writer\TistoryWriter', 'requestAccessCode') );
add_action('wp_ajax_requestAccessCodeWithAuth', array('tistory_writer\TistoryWriter', 'requestAccessCodeWithAuth') );
add_action('wp_ajax_requestBlogUrl',            array('tistory_writer\TistoryWriter', 'requestBlogUrl') );
add_action('wp_ajax_saveSettings',              array('tistory_writer\TistoryWriter', 'saveSettings'));
add_action('wp_ajax_saveLinkFlag',              array('tistory_writer\TistoryWriter', 'saveLinkFlag'));

register_activation_hook(__FILE__,      array('tistory_writer\TistoryWriter', 'activatePlugin'));
register_deactivation_hook(__FILE__,    array('tistory_writer\TistoryWriter', 'deactivatePlugin'));


