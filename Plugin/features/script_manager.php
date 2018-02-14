<?php
namespace tistory_writer;

/**
 * 플러그인에서 사용할 스크립트 파일들을 관리한다.
 */

class ScriptManager
{
    /* Members */
    public $files_css = array (
        "main" => "css\main.css",
    );

    public $files_javascript = array (

    );

    /* Constructor */
    public function __construct()
    {
    }

    /**
     * 플러그인에서 사용하는 모든 스크립트 파일을 로드한다.
     */
    public function loadFiles()
    {
        $this->registerStyleFiles();
        $this->registerScriptFiles();

        $this->enqueueStyleFiles();
        $this->enqueueScriptFiles();
    }

    public function registerScriptFiles()
    {
        $path = join('/', array(PLUGIN_URL, 'assets', 'scripts', 'set_config.js'));
        wp_register_script('tistory-writer-set_config', $path);
    }

    public function enqueueScriptFiles()
    {
        Logger::log("ScriptManager::enqueueScriptFiles: ");
        wp_enqueue_script('tistory-writer-set_config');
    }

    public function registerStyleFiles()
    {
        $path = join('/', array(PLUGIN_URL,'assets', 'css','main.css'));
        wp_register_style('tistory-writer-main', $path);
    }

    public function enqueueStyleFiles()
    {
        Logger::log("ScriptManager::enqueueScriptFiles: ");
        wp_enqueue_style('tistory-writer-main');
    }
}
