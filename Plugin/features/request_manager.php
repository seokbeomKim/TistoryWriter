<?php

namespace tistory_writer;

use const tistory_writer\FEATURE_KEY\OPTION;
use const tistory_writer\FEATURE_KEY\TISTORY_API;
use const tistory_writer\OPTION_KEY\ACCESS_TOKEN;
use const tistory_writer\OPTION_KEY\BLOG_NAME;
use const tistory_writer\OPTION_KEY\CLIENT_ID;
use const tistory_writer\OPTION_KEY\EXPIRE_TIMESTAMP;
use const tistory_writer\OPTION_KEY\REDIRECT_URI;
use const tistory_writer\OPTION_KEY\SECRET_KEY;
use const tistory_writer\OPTION_KEY\SELECTED_BLOG;

class RequestManager {
	public function getMetaBoxData()
	{
		$apiMgr = TistoryWriter::getManager(TISTORY_API);
		$title = $_POST['title'];

		$categories = $apiMgr->getCategoryList();
		$postInfo = $apiMgr->getPostInfoWithTitle($title);

		if (!is_null($postInfo)) {
			$detail = $apiMgr->getDetailInfoWithPostId($postInfo['id']);
			$linkFlag = $apiMgr->getLinkFlagWithPostId($_POST['wp_postId']);

			$rvalue = array (
				'detail' => $detail,
				'category' => $categories,
				'linkFlag' => $linkFlag,
			);
		}
		else {
			$rvalue = array (
				'detail' => null,
				'category' => $categories,
				'linkFlag' => false,
			);
		}

		return json_encode($rvalue);
	}
	public function postUpdate() {
		if (isset($_POST['post_title']) && isset($_POST['content'])) {
			$apiMgr     = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);

			$title      = stripslashes(wp_kses_post($_POST['post_title']));
			$content    = stripslashes(wp_kses_post($_POST['post_content']));
			$content    = nl2br($content);
			$tag        = wp_kses_post($_POST['input_tag']);

			$category_id    = isset($_POST['select_category']) ? wp_kses_post($_POST['select_category']) : "";
			$visibility     = isset($_POST['select_visibility']) ? wp_kses_post($_POST['select_visibility']) : "0";
			$isAllowComment = isset($_POST['checkAllowComment']) ? wp_kses_post($_POST['checkAllowComment']) : "0";

			$apiMgr->insertPost($title, $content, $visibility, $category_id, "", $isAllowComment, $tag);
		}
	}
	public function editPost() {
		if (isset($_POST['post_title']) && isset($_POST['content'])) {
			$apiMgr     = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);

			$title      = stripslashes(wp_kses_post($_POST['post_title']));
			$content    = stripslashes(wp_kses_post($_POST['post_content']));
			$content    = nl2br($content);
			$tag        = wp_kses_post($_POST['input_tag']);

			$category_id    = isset($_POST['select_category']) ? wp_kses_post($_POST['select_category']) : "";
			$visibility     = isset($_POST['checkMakePublic']) ? wp_kses_post($_POST['checkMakePublic']) : "0";
			$isAllowComment = isset($_POST['checkAllowComment']) ? wp_kses_post($_POST['checkAllowComment']) : "0";
			$postId         = wp_kses_post($_POST['postId']);

			if (!wp_is_post_autosave($postId)) {
				$apiMgr->updatePost($title, $content, $visibility, $category_id, "", $isAllowComment, $tag, $postId);
			}
		}
	}
	public function insertPost($post_id, $post, $update)
	{
		$apiMgr = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);
		$flag   = isset($_POST['turnIntegratationOff']);

		$post_info = $apiMgr->getPostInfoWithTitle($post->post_title);
		$postMetaKey = $apiMgr->getPostMetaKey();

		// 연동 체크 박스 정보 저장
		if (is_null(get_post_meta($post_id, $postMetaKey))) {
			add_post_meta($post_id, $postMetaKey, $flag);
		}
		else {
			update_post_meta($post_id, $postMetaKey, $flag);
		}

		if (!$flag) {
			if (strpos($post->name, 'autosave') != true) {
				$ti = (int)wp_kses_post($post_info['id']);
				if ($ti <= 0) {
					self::postUpdate();
				} else {
					self::editPost();
				}
			}
		} else {
			if (method_exists('\\tistory_writer\\Logger', 'log')) {
				Logger::log("insertPost, 연동기능 임시 해제");
			}
		}
	}
	public function saveSettings() {
		$optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);

		$optionMgr->setOption(CLIENT_ID, $_POST['client_id']);
		$optionMgr->setOption(SECRET_KEY, $_POST['secret_key']);
		$optionMgr->setOption(BLOG_NAME, $_POST['blog_name']);

		$apiMgr = TistoryWriter::getManager(TISTORY_API);
		$blogInfo = $apiMgr->getBlogInformation();

		return json_encode($blogInfo);
	}
	public function changeSelectedBlog() {
		$optionMgr = TistoryWriter::getManager(OPTION);
		$optionMgr->setOption(SELECTED_BLOG, $_POST['selected_blog']);
		return "VALUE";
	}
	public function requestAccessCode() {
		$optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
		$optionMgr->setOption(ACCESS_TOKEN, $_POST['access_code']);
		$optionMgr->setOption(EXPIRE_TIMESTAMP, time() - 3590);

		$blogInfo = null;

		// Access code 갱신 요청 시, 클라이언트 페이지에 블로그 정보로 갱신할 블로그 주소를 송신한다.
		$apiMgr = TistoryWriter::getManager(TISTORY_API);
		$blogInfo = $apiMgr->getBlogInformation();

		return json_encode($blogInfo);
	}
	public function requestAccessCodeWithAuth() {
		$optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
		$authorization_code = $_POST['auth_code'];

		$client_id = $optionMgr->getOption(CLIENT_ID);
		$client_secret = $optionMgr->getOption(SECRET_KEY);
		$redirect_uri = $optionMgr->getOption(REDIRECT_URI);
		$grant_type = 'authorization_code';

		$url = 'https://www.tistory.com/oauth/access_token/?code=' . $authorization_code .
		       '&client_id=' . $client_id . '&client_secret=' . $client_secret .
		       '&redirect_uri=' . urlencode($redirect_uri) . '&grant_type=' . $grant_type;

		$access_token = ($url);

		$rvalue = str_replace('access_token=', '', $access_token);
		$optionMgr->setOption(ACCESS_TOKEN, $rvalue);

		return $rvalue;
	}
	public function requestBlogUrl() {
		$apiMgr = TistoryWriter::getManager(TISTORY_API);
		$blogInfo = $apiMgr->getBlogInformation();

		if (is_null($blogInfo)) {
			return "FAIL";
		}

		foreach ($blogInfo as $blog) {
			if ($blog->name == $_POST['blog_name']) {
				$rvalue = array($blog->title, $blog->url);
				return json_encode($rvalue);
				break;
			}
		}
		return "FAIL";
	}
	public function getUrlForAccessToken() {
		$optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
		$rvalue = "https://www.tistory.com/oauth/authorize?client_id=" . $optionMgr->getOption(CLIENT_ID) .
		          "&redirect_uri=" . $optionMgr->getOption(REDIRECT_URI) . "&response_type=token";
		return $rvalue;
	}
}