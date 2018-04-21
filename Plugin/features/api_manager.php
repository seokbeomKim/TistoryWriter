<?php
namespace tistory_writer;

/*
 * 티스토리 연동 시 사용하는 티스토리 API 관리 클래스
 */
class ApiManager
{
    public $access_token;
    public $option_mgr;

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
        $url = 'https://www.tistory.com/apis/blog/info';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN)
        );

        $xml = $this->requestPost($url, $data);
        if ($xml == null) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
                return false;
            }
        }

        return true;
    }

    public function getBlogAccount()
    {
        $url = 'https://www.tistory.com/apis/blog/info';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN)
        );

        $xml = $this->requestPost($url, $data);
        if ($xml == null) {
            if (method_exists('\\tistory_writer\\Logger', 'log')) {
                Logger::log("getPostInfoWithTitle, Request에 실패했습니다.");
            }
            return null;
        }

        return $xml->item->id;
    }

    public function requestPost($url, $data)
    {
        // Step 1. POST
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));

        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);

        if ($xml != null && $xml->status == 200) {
            return $xml;
        }

        // Try 2. GET
        $xml = $this->requestGet($url, $data);
        if ($xml != null && $xml->status == 200) {
            return $xml;
        }

        // Try 3. Request - RAW
        $xml = $this->requestFallback($url, $data);
        if ($xml != null && $xml->status == 200) {
            return $xml;
        } else {
            return null;
        }
    }

    public function requestFallback($url, $data)
    {
        $response = wp_remote_request($url, array(
            'body' => $data,
            'output' => 'xml',
        ));

        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);

        if ($xml != null && $xml->status == 200) {
            return $xml;
        } else {
            return null;
        }
    }

    public function requestGet($url, $data)
    {
        $response = wp_remote_get($url, array(
            'body' => $data,
            'output' => 'xml',
        ));

        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);

        if ($xml != null && $xml->status == 200) {
            return $xml;
        } else {
            return null;
        }
    }

    public function getCategoryList()
    {
        $url = 'https://www.tistory.com/apis/category/list';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
        );

        $xml = $this->requestPost($url, $data);
        if ($xml == null) {
            return null;
        }

        $array = json_decode(json_encode((array)$xml->item->categories), true);
        return $array;
    }

    public function insertPost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag)
    {
        /* 이미 해당 post가 있는지 확인 */
        $url = 'https://www.tistory.com/apis/post/write';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
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
                'blogName' => get_option(OPTION_KEY\BLOG_NAME),
                'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
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
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
            'sort' => 'date',
        );

        $xml = $this->requestPost($url, $data);
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

    public function getPostInfoWithTitle($title, $date)
    {
        $url = 'https://www.tistory.com/apis/post/list';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
            'sort' => 'date',
        );

        $xml = $this->requestPost($url, $data);
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
                    foreach ($v as $key => $value) {
                        if ($this->decodeCharacters(stripslashes($value['title'])) === $this->decodeCharacters(stripslashes($title))) {
                            return array(
                                'id' => $value['id'],
                                'url' => $value['postUrl'],
                                'date' => $value['date'],
                                'visibility' => $value['visibility'],
                                'category_id' => $value['categoryId'],
                            );
                        }
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

    public function getVisibilityWithPostId($post_id)
    {
        $url = 'https://www.tistory.com/apis/post/read';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
            'postId' => $post_id,
        );

        $xml = $this->requestPost($url, $data);
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
        $url = 'https://www.tistory.com/apis/post/read';
        $data = array (
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
            'postId' => $post_id,
        );

        $xml = $this->requestPost($url, $data);
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
