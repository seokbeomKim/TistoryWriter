<?php 
/* 
Tistory Writer(https://github.com/seokbeomKim/TistoryWriter)

Description: 워드프레스와 티스토리를 연동하는 플러그인입니다. 
Version: 1.0
Author: 김석범, Sukbeom Kim (sukbeom.kim@gmail.com)
*/ 

// Start up the engine 
add_action('admin_menu', 'tw_menu');

// Define new menu page parameters
function tw_menu() {
	add_menu_page( 'Tistory Writer', 'Tistory Writer', 'activate_plugins', 'tistory-writer', 'tw_options', '');
}

// Define new menu page content
function tw_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} else { 
		?>
	
    <!-- Output for Plugin Options Page -->
	<div class="wrap">
        <h2 id="">Tistory Writer</h2>

        <?php if ($_GET["add_bundle"] == true){ ?>
            <div class="updated below-h2" id="message">
                <p>Example Post Bundle Added!</p>
            </div>
        <?php } elseif ($_GET["remove_all"] == true){;?>
            <div class="updated below-h2" id="message">
                <p>All Example Posts Removed!</p>
            </div>
        <?php }; // endif ?> 

        <p>Tistory Writer 플러그인을 사용해주셔서 감사합니다. Tistory Writer는 워드프레스로 작성한 글을 티스토리 블로그에 자동으로 등록하는 플러그인입니다.</p>

        <h3 id="">사용법</h3>
        <p>연동하기 버튼을 클릭하여 티스토리 계정을 설정하면 티스토리 연동기능이 활성화됩니다. 플러그인이 제공하는 연동 기능은 아래와 같습니다.</p>
        <ol>
            <li>티스토리 계정 연결 등록 또는 해제</li>
            <li>티스토리 블로그 내 카테고리 선택</li>
            <li>자동 포스팅 기능</li>
        </ol>
		<p>버그나 기능 결함 발견 시 <a href='https://github.com/seokbeomKim/TistoryWriter'>https://github.com/seokbeomKim/SmartMailParser</a> 에 접속하시어 
        이슈를 등록해주시거나 sukbeom.kim@gmail.com로 메일주시기 바랍니다.</p>
        <br><br><br>

        <a href="?page=wp-example-content&amp;remove_all=true" class="button">계정 연동</a>
        <a href="?page=wp-example-content&amp;remove_all=true" class="button">연동 해제</a>

	</div>
	<!-- End Output for Plugin Options Page -->
	
<?php 

	// Add Posts -------------------------
	if ($_GET["add_bundle"] == true){
		global $wpdb;
	    // Get content for all posts and pages, then insert posts
	    include 'content.php';
	    foreach ($add_posts_array as $post){
	        wp_insert_post( $post );
	    };
		
		// Add Child Page
		$page_name = 'Image Page';
		$page_name_id = $wpdb->get_results("SELECT ID FROM " . $wpdb->base_prefix . "posts WHERE post_title = '". $page_name ."'");
        foreach($page_name_id as $page_name_id){
        	$imagepageid = $page_name_id->ID;
			include 'content.php';
        	wp_insert_post( $childpage );
        };
		
		// Add Grandchild Page
		$page_name = 'Child Page';
		$page_name_id = $wpdb->get_results("SELECT ID FROM " . $wpdb->base_prefix . "posts WHERE post_title = '". $page_name ."'");
        foreach($page_name_id as $page_name_id){
        	$childpageid = $page_name_id->ID;
			include 'content.php';
        	wp_insert_post( $grandchildpage );
        };
	};
	// ---------------------------------------

	//  Remove Posts -------------------------
	if ($_GET["remove_all"] == true){
	    // Get content for all posts and pages, then remove them
	    include 'content.php';
	    foreach($remove_posts_array as $array){
	        $page_name = $array["post_title"];
			global $wpdb;
	        $page_name_id = $wpdb->get_results("SELECT ID FROM " . $wpdb->base_prefix . "posts WHERE post_title = '". $page_name ."'");
	        foreach($page_name_id as $page_name_id){
	        	$page_name_id = $page_name_id->ID;
	        	wp_delete_post( $page_name_id, true );
	        };
	    };
	};
	// ---------------------------------------
	
}}; ?>