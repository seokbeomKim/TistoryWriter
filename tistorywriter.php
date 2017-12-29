<?php 
/* 
Plugin Name: Tistory Writer
Plugin URI: https://github.com/seokbeomKim/TistoryWriter)
Description: 워드프레스와 티스토리를 연동하는 플러그인입니다. 
Version: 1.0
Author: Sukbeom Kim (sukbeom.kim@gmail.com)
Author URI: http://chaoxifer.tistory.com
License: GPLv3

'Tistory Writer' is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Tistory Writer is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Tistory Writer. If not, see http://www.gnu.org/licenses/gpl.html.
*/ 

defined('ABSPATH') or die('No script kiddies please!');

/* 플러그인 Option 메뉴에 추가 */
add_action('admin_menu', 'tw_menu');
add_action('pre_post_update', 'tw_save_post', 10, 2);


/* 자바스크립트 파일 로드 */
wp_register_script('set_config', plugins_url('js/set_config.js', __FILE__));
wp_enqueue_script('set_config');

// 글 작성 시, 티스토리에 글 작성
function tw_menu() {
	//add_menu_page( 'Tistory Writer', 'Tistory Writer', 'activate_plugins', 'tistory_writer', 'tw_options', '');
	add_options_page( 'Tistory Writer', 'Tistory Writer', 'activate_plugins', 'tistory_writer', 'tw_options', '');
}

function tw_save_post($post_id, $post) {
	if (get_option('tw_access_token') == null) {
		return;
	}
	$post_title = get_the_title( $post_id );
	$post_url = get_permalink( $post_id );
	$subject = 'A post has been updated';

	$message = "A post has been updated on your website:\n\n";
	$message .= $post_title . ": " . $post_url;

	// Send email to admin.
	wp_mail( 'sukbeom.kim@gmail.com', $subject, $content );

	// 실제 메일 전송을 확인했으므로 티스토리 API를 이용해서 사용자가 원하는 
	// 블로그에 글을 포스팅한다.
	$url = "https://www.tistory.com/apis/post/write";
	$blogName = get_option('tw_blogname');
	$title = $post['post_title'];
	$content = $post['post_content'];

	$post_data = array(
		'blogName' => $blogName,
		'title' => $title,
		'content' => $content,
		'access_token' => get_option('tw_access_token'),
		'visibility' => 2
	);

	$result = wp_remote_post($url, array(
		'method' => 'POST',
		'body' => $post_data
	));
}

// 플러그인 메인
function tw_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} else { 
		?>
	
	<?php 
	if ($_GET["tw_step2_completed"] == true) {
		echo 'alert("2단계 설정 완료")';
	}
	?>
	</script>
    <!-- Output for Plugin Options Page -->
	<div class="wrap">
        <h2 id="">Tistory Writer</h2>

        <p>Tistory Writer 플러그인을 사용해주셔서 감사합니다. 
		Tistory Writer는 워드프레스로 작성한 글을 티스토리 블로그에 자동으로 등록하는 플러그인입니다.</p>

		<h3>사용법</h3>
		<p>아래의 절차에 따라 티스토리 계정 연동을 설정합니다.</p>
		<ol>
			<!-- 1단계 설정. OpenAPI 서비스 등록 안내 -->
			<li><b>티스토리 OAuth 인증 사용을 위해서 신규 클라이언트 아이디를 설정합니다. </b>
			<p>
			<a href="http://www.tistory.com/guide/api/manage/register">여기</a>를 눌러 티스토리 OpenAPI를 사용하기 위해 
			클라이언트 ID를 등록하세요.
			</p>
			<ul style="list-style-type: circle; margin-left: 30px; padding-bottom: 20px;">
				<li>서비스 명: 'TistoryWriter' </li>
				<li>설명: 원하는대로 적으시면 됩니다.</li>
				<li>서비스 URL: <?php print("http:// " . $_SERVER['HTTP_HOST']); ?></li>
				<li>서비스 권한: 읽기/쓰기</li>
				<li>Callback 경로: 플러그인이 설치된 경로(예: <?php print(plugin_dir_url(__FILE__) . "oauth_callback.php"); ?>)</li>
			</ul>
			</li>

			<!-- 2단계. 1단계에서 발급받은 Client Id 관련 설정 -->
			<li style="margin-bottom:20px; padding-bottom:10px;">
			<b>
			<a href="http://www.tistory.com/guide/api/manage/list">서비스 관리 페이지</a>로 접속하여 단계 1에서 등록한 서비스의 관리버튼을 클릭합니다.<br/><br/></b>
			등록한 서비스의 상세 내용 중 Client ID, Secret Key, Callback URL을 입력하고 아래의 설정 버튼을 클릭합니다.
			
			<div style="margin-bottom:10px;">
				<label>Client ID</label><br/>
				<input size="50" type="text" id="client_id" name="client_id" value="<?php if($_GET['tw_step2_completed'] == true) {echo $_GET['client_id'];} else { $t = get_option('client_id'); if ($t != "null") echo $t; } ?>">
			</div>
			<div style="margin-bottom:10px;">
				<label>Secret Key</label><br />
				<input size="50" type="text" id="secret_key" name="secret_key" value="<?php if($_GET['tw_step2_completed'] == true) {echo $_GET['secret_key'];} else{ $t = get_option('secret_key'); if ($t != "null") echo $t; } ?>"/>
			</div>
			<div style="margin-bottom:10px;">
				<label>Callback URL</label><br />
				<input size="50" type="text" id="callback_url" name="callback_url" value="<?php if($_GET['tw_step2_completed'] == true){echo $_GET['callback_url'];} else {$t = get_option('callback_url'); if ($t != "null") echo $t; }?>" />
			</div>
			<div style="margin-bottom:10px;">
				<label>블로그 이름(https://xxxx.tistory.com)에서 xxxx 입력</label><br />
				<input size="50" type="text" id="blogname" name="blogname" value="<?php if($_GET['tw_step2_completed'] == true){echo $_GET['blogname'];} else {$t = get_option('tw_blogname'); if ($t != "null") echo $t; }?>" />
			</div>
			<div>
				<a onClick="step2_onClick();" class="button">2단계 설정 완료</a>
			</div>
			</li>

			<!-- 인증 요청 및 Authorization Code 발급 -->
			<li>
			<p>
			아래의 '계정 연동' 버튼을 클릭하여 설정을 마무리합니다. 기존 연동은 연동 해제 버튼으로 해제할 수 있습니다.
			</p>
			<div>
			<form id="auth_form" method="get" action="https://www.tistory.com/oauth/authorize/">
			    <input type="hidden" id="form_client_id" name="client_id" value="{발급받은 client_id를 입력}"/>
                <input type="hidden" id="form_redirect_url" name="redirect_uri" value="{등록시 입력한 redirect uri를 입력}"/>
                <input type="hidden" id="form_response_type" name="response_type" value="token"/>
            </form>
			<div>
			<label id="lbl_access_token">Access Token: 
			<script type="text/javascript">
			 if(window.location.hash) {
                 var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
				 var link = "?page=tistory_writer&"+hash;
		         document.location.replace(link);
			 }
			</script>
			<?php 
			
			if($_GET['access_token'] != null) {
				echo $_GET['access_token'];
				echo "<br/>";
				update_option('tw_access_token', $_GET['access_token']);
			} 
			else {
				$t = get_option('tw_access_token'); 
				if ($t != "null") echo $t; 
				else {echo "계정연동이 필요합니다.";} 
			} 
			?>
			</label>
			</div>
	        <a class="button" onClick="auth_submit();">계정 연동</a>
	        <a class="button" onClick="init_auth();">연동 해제</a>
			</div>
			</li>
		</ol>


	</div>


	<!-- End Output for Plugin Options Page -->
<?php 
	if ($_GET["tw_step2_completed"] == true){
		/// 2단계에서 저장한 사용자 데이터를 전역으로 저장함
		update_option('client_id', $_GET['client_id']);
		update_option('secret_key', $_GET['secret_key']);
		update_option('callback_url', $_GET['callback_url']);
		update_option('tw_blogname', $_GET['blogname']);
	};

	if ($_GET["tw_init_access_token"] == true) {
		update_option('tw_access_token', null);
	}
}}; ?>