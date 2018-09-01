<?php
namespace tistory_writer;

/**
 * 플러그인에서 사용할 스크립트 파일들을 관리한다.
 */

const DISABLE_MDL = "DISABLE_MDL";

class ScriptManager
{
    /* Members */
    public $files_css = array (
        "main" => "css\main.css",
        "metabox" => "css\metabox.css",
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
    public function loadFiles($pageType)
    {
    	if ($pageType == 'SETTING') {
		    $this->registerStyleFiles();
		    $this->registerScriptFiles();

		    $this->enqueueStyleFiles();
		    $this->enqueueScriptFiles();

		    wp_enqueue_script( 'ajax-script',
			    plugins_url( '../assets/scripts//tw_jquery.js', __FILE__ ),
			    array('jquery')
		    );

		    $title_nonce = wp_create_nonce( 'tistory_writer' );
		    wp_localize_script( 'ajax-script', 'tw_ajax', array(
			    'ajax_url' => admin_url( 'admin-ajax.php' ),
			    'nonce'    => $title_nonce, // It is common practice to comma after
		    ));
	    }
	    else if ($pageType == 'METABOX')
	    {
		    $this->registerStyleFiles();
		    $this->registerScriptFiles();

		    $this->enqueueStyleFiles(true);
		    $this->enqueueScriptFiles();

		    wp_enqueue_script( 'ajax-script',
			    plugins_url( '../assets/scripts//tw_metabox_jquery.js', __FILE__ ),
			    array('jquery')
		    );

		    $title_nonce = wp_create_nonce( 'tistory_writer' );
		    wp_localize_script( 'ajax-script', 'tw_ajax', array(
			    'ajax_url' => admin_url( 'admin-ajax.php' ),
			    'nonce'    => $title_nonce, // It is common practice to comma after
		    ));
	    }

    }

    public function registerScriptFiles()
    {
        $path = join('/', array(PLUGIN_URL, 'assets', 'scripts', 'set_config.js'));
        wp_register_script('tistory-writer-set_config', $path);

	    $path = join('/', array(PLUGIN_URL, 'assets', 'scripts', 'material.min.js'));
	    wp_register_script('tistory-writer-mdl-js', $path);

	    //$path = join('/', array(PLUGIN_URL, 'assets', 'scripts', 'tw_jquery.js'));
	    //wp_register_script('tistory-writer-jquery', $path);
    }

    public function enqueueScriptFiles()
    {
	    wp_enqueue_script('tistory-writer-set_config');
	    wp_enqueue_script('tistory-writer-mdl-js');
	    //wp_enqueue_script('tistory-writer-jquery');
    }

    public function registerStyleFiles()
    {
        $path = join('/', array(PLUGIN_URL,'assets', 'css','main.css'));
        wp_register_style('tistory-writer-main', $path);

        $path = join('/', array(PLUGIN_URL,'assets', 'css','metabox.css'));
        wp_register_style('tistory-writer-metabox', $path);

	    $path = join('/', array(PLUGIN_URL,'assets', 'css','material.css'));
	    wp_register_style('tistory-writer-mdl', $path);
    }

    public function enqueueStyleFiles($disable_mdl = false)
    {
    	if ($disable_mdl == false) {
		    wp_enqueue_style('tistory-writer-mdl');
	    }
	    wp_enqueue_style('tistory-writer-main');
        wp_enqueue_style('tistory-writer-metabox');
    }
}
