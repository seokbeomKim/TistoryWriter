<?php
namespace tistory_writer;

/*
 * 티스토리 연동 시 사용하는 티스토리 API 관리 클래스
 */

const TRY_NUM = 3;
const CACHE_BLOGINFO = "CACHE_BLOGINFO";

class ApiManager
{
    public $access_token;
    public $option_mgr;

    // 불필요하게 접근하는 blog Info와 같은 것들을 캐시로 관리함
    public $cache;

    public function __construct()
    {
        $this->access_token = get_option(OPTION_KEY\ACCESS_TOKEN);
    }

    public function getAccessToken()
    {
        if (!$this->checkAccessToken()) {
            $this->refreshAccessToken();
        }
        return $this->access_token;
    }

    public function refreshAccessToken()
    {
        $page_mgr = TistoryWriter::getManager(FEATURE_KEY\PAGE_LOADER);
        $page_mgr->getOAuthPage();
    }

    /**
     * 현재 Option으로 설정되어 있는 토큰 값이 유효한지 검사
     * status 값 200이면 true, 그 외의 경우는 false 리턴
     */
    public function checkAccessToken()
    {
        $rvalue = $this->getBlogInformation();

        if (is_null($rvalue)) {
        	return false;
        }
        else {

	        return true;
        }
    }

    public function getBlogAccount()
    {
    	if (!is_null($this->cache[CACHE_BLOGINFO])) {
    		return $this->cache[CACHE_BLOGINFO]->item->id;
	    }

        $url = 'https://www.tistory.com/apis/blog/info';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
	        'output' => 'json'
        );

        $result = $this->requestGet($url, $data);
        if ($result == null || $result->status != 200) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getBlogAccount, Request에 실패했습니다.");
            }
            return null;
        }

        return $result->item->id;
    }

    public function getBlogInformation()
    {
	    if (!is_null($this->cache[CACHE_BLOGINFO])) {
		    return $this->cache[CACHE_BLOGINFO]->item->blogs;
	    }

	    $url = 'https://www.tistory.com/apis/blog/info';
	    $data = array(
		    'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
		    'output' => 'json',
	    );

	    $result = $this->requestGet($url, $data);
	    if ($result == null) {
		    if (method_exists('\\tistory_writer\\Logger', 'log')) {
			    Logger::log("getBlogInformation, Request에 실패했습니다.");
		    }
		    return null;
	    }

	    $this->cache[CACHE_BLOGINFO] = $result;

	    return $result->item->blogs;
    }

    public function requestPost($url, $data)
    {
		$data['output'] = 'json';

	    for ($i = 0; $i < TRY_NUM; $i++) {
		    $response = wp_remote_post( $url, array(
			    'body' => $data,
		    ) );

		    $body   = wp_remote_retrieve_body( $response );
		    $rvalue = json_decode( $body );

		    if (is_null($rvalue) && $i == TRY_NUM - 1) {
	            return $this->requestPostFallback($url, $data);
		    }
		    else {
		    	break;
		    }
	    }

        if ($rvalue != null && $rvalue->tistory->status == 200) {
            return $rvalue->tistory;
        }
    }

    public function requestGetFallback($url, $data)
    {
	    $builtdata = http_build_query($data);
	    $opts = array('http' =>
		                  array(
			                  'method'  => 'GET',
			                  'header'  => 'Content-type: application/x-www-form-urlencoded',
			                  'content' => $builtdata
		                  )
	    );
	    $context  = stream_context_create($opts);
	    $result = file_get_contents($url, false, $context);

        return $result;
    }

	public function requestPostFallback($url, $data)
	{
		$builtdata = http_build_query($data);
		$opts = array('http' =>
			              array(
				              'method'  => 'GET',
				              'header'  => 'Content-type: application/x-www-form-urlencoded',
				              'content' => $builtdata
			              )
		);
		$context  = stream_context_create($opts);
		$result = file_get_contents($url, false, $context);

		return $result;
	}

    public function requestGet($url, $data)
    {
    	// JSON으로 OUTPUT Format 강제
    	$data['output'] = 'json';

        $response = wp_remote_get($url, array(
            'body' => $data,
        ));

        for ($i = 0; $i < TRY_NUM; $i++) {
	        $body   = wp_remote_retrieve_body( $response );
	        $decode = json_decode( $body );

	        if (is_null($decode)) {
	        	return $this->requestGetFallback($url, $data);
	        }

	        $result = $decode->tistory;

	        if ( $result != null && $result->status == 200 ) {
		        return $result;
	        } else {
	        	if ($i == TRY_NUM - 1)
		            return null;
	        	else
	        		;   // TRY AGAIN
	        }
        }
    }

    public function getCategoryList()
    {
        $url = 'https://www.tistory.com/apis/category/list';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
            'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
	        'output' => 'json',
        );

        $result = $this->requestGet($url, $data);
        if (is_null($result) || empty($result) || $result->status != 200) {
            return null;
        }

		if (isset($result->item->categories) && $result->item->categories != null) {
			$array = json_decode( json_encode( (array) $result->item->categories ), true );
			return $array;
		}

		return null;
    }

    public function insertPost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag)
    {
        /* 이미 해당 post가 있는지 확인 */
        $url = 'https://www.tistory.com/apis/post/write';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
            'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
            'title' => $title,
            'visibility' => $visibility,
            'category' => $category_id,
            'content' => $content,
            'acceptComment' => $isAllowComment,
            'tag' => $tag
        );

        $this->requestPost($url, $data);
    }

    public function updatePost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag, $postId)
    {
        $url = 'https://www.tistory.com/apis/post/modify';

        if (!isset($postId)) {
            $this->insertPost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag);
        } else {
            $data = array (
                'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
                'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
                'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
                'title' => $title,
                'postId' => $postId,
                'visibility' => $visibility,
                'category' => $category_id,
                'content' => $content,
                'acceptComment' => $isAllowComment,
                'tag' => $tag,
            );

            $this->requestPost($url, $data);
        }
    }

    public function getPostIdWithTitle($title, &$id)
    {
        $url = 'https://www.tistory.com/apis/post/list';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
            'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
            'sort' => 'date',
        );

        $xml = $this->requestGet($url, $data);
        if ($xml == null) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
                return;
            }
        }

        $posts = json_decode(json_encode((array)$xml->item->posts), true);

        if (is_array($posts) && isset($posts)) {
            foreach ($posts as $k => $v) {
                if (is_array($v) && isset($v)) {
                    foreach ($v as $key => $value) {
                        if (stripslashes($value['title']) === stripslashes($title)) {
                            $id = $value['id'];
                        }
                    }
                } else {
                    if (method_exists('\\tistory_writer\\Logger', 'log')) {
                        Logger::log("getPostIdWithTitle, 얻어온 post id 값 이상");
                    }
                }
            } // foreach ($post as $k => $v) ends here
        } // if statement ends here
    }

    public function getPostInfoWithTitle($title)
    {
    	if (is_null($title) || empty($title)) {
    		return;
	    }
        $url = 'https://www.tistory.com/apis/post/list';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
            'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
            'sort' => 'date',
        );

        $xml = $this->requestGet($url, $data);
        if ($xml == null) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
            }
            return;
        }

        $posts = json_decode(json_encode((array)$xml->item->posts), true);

        if (is_array($posts) && isset($posts)) {
            foreach ($posts as $k => $v) {
                if (is_array($v) && isset($v)) {
	                if ($this->decodeCharacters(stripslashes($v['title'])) === $this->decodeCharacters(stripslashes($title))) {
		                return array(
			                'id' => $v['id'],
			                'url' => $v['postUrl'],
			                'date' => $v['date'],
			                'visibility' => $v['visibility'],
			                'category_id' => $v['categoryId'],
		                );
	                }
                } else {
                    if (method_exists('\\tistory_writer\\Logger', 'log')) {
                        Logger::log("getPostInfoWithTitle, 얻어온 post 반환값 이상");
                    }
                }
            }
        }
    }

    public function decodeCharacters($data)
    {
        return mb_convert_encoding($data, 'UTF-8', 'HTML-ENTITIES');
    }

	public function getDetailInfoWithPostId($post_id)
	{
		if (is_null($post_id) || empty($post_id)) {
			return null;
		}

		$url = 'https://www.tistory.com/apis/post/read';
		$data = array (
			'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
			'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
			'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
			'postId' => $post_id,
		);

		$xml = $this->requestGet($url, $data);
		if ($xml == null) {
			if (method_exists('\\tistory_writer\\Logger', 'log')) {
				Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
			}
			return null;
		}
		return $xml->item;
	}

    public function getVisibilityWithPostId($post_id)
    {
    	if (is_null($post_id) || empty($post_id)) {
    		return null;
	    }

        $url = 'https://www.tistory.com/apis/post/read';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
            'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
            'postId' => $post_id,
        );

        $xml = $this->requestGet($url, $data);
        if ($xml == null) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
            }
            return null;
        }
        return $xml->item->visibility;
    }

	public function getAllowCommentWithPostId($post_id)
	{
		if (is_null($post_id) || empty($post_id)) {
			return null;
		}

		$url = 'https://www.tistory.com/apis/post/read';
		$data = array (
			'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
			'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
			'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
			'postId' => $post_id,
		);

		$xml = $this->requestGet($url, $data);
		if ($xml == null) {
			if (method_exists('\\tistory_writer\\Logger', 'log')) {
				Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
			}
			return null;
		}
		return $xml->item->acceptComment;
	}

    public function getTagsWithPostId($post_id)
    {
    	if (is_null($post_id) || empty($post_id)) {
    		return null;
	    }

        $url = 'https://www.tistory.com/apis/post/read';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\SELECTED_BLOG),
            'targetUrl' => get_option(OPTION_KEY\SELECTED_BLOG),
            'postId' => $post_id,
        );

        $xml = $this->requestGet($url, $data);
        if ($xml == null) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
            }
            return null;
        }

        $tags = json_decode(json_encode((array)$xml->item->tags), true);

        return $tags;
    }

    public function compareTimestamp($t1, $t2)
    {
        $t_t1 = substr($t1, 0, -9);
        $t_t2 = substr($t2, 0, -9);

        if ($t_t1 === $t_t2) {
            return true;
        } else {
            return false;
        }
    }
}
