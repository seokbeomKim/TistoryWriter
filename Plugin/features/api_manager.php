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
        Logger::log("ApiManager is initialized");
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
        Logger::log("Access token 갱신 필요");
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
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));
        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);

        if ($xml->status == 200) {
            return true;
        } else {
            return false;
        }
    }

    public function getBlogAccount()
    {
        $url = 'https://www.tistory.com/apis/blog/info';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN)
        );
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));
        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);

        return $xml->item->id;
    }

    public function getCategoryList()
    {
        $url = 'https://www.tistory.com/apis/category/list';
        $data = array(
            'access_token' => get_option(OPTION_KEY\ACCESS_TOKEN),
            'blogName' => get_option(OPTION_KEY\BLOG_NAME),
            'targetUrl' => get_option(OPTION_KEY\BLOG_NAME),
        );
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));
        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);
        $array = json_decode(json_encode((array)$xml->item->categories), true);
        return $array;
    }

    public function insertPost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag)
    {
        /* 이미 해당 post가 있는지 확인 */
        Logger::log("updatePost(): category_id = " . $category_id);
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
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));
    }

    public function updatePost($title, $content, $visibility, $category_id, $isProtected, $isAllowComment, $tag, $postId)
    {
        $url = 'https://www.tistory.com/apis/post/modify';

        Logger::log("updatePost, postId = ", $postId);

        if (empty($postId)) {
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
            $response = wp_remote_post($url, array(
                'body' => $data,
                'output' => 'xml',
            ));
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
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));
        $rValue = "";

        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);
        $posts = json_decode(json_encode((array)$xml->item->posts), true);

        foreach ($posts as $k => $v) {
            foreach ($v as $key => $value) {
                Logger::log("타이틀로 찾기 asdf: " . $value['title']);
                if ($value['title'] == $title) {
                    Logger::log($title . ", id 반환값: " . $value['id']);
                    $id = $value['id'];
                }
            }
        }
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
        $response = wp_remote_post($url, array(
            'body' => $data,
            'output' => 'xml',
        ));
        $rValue = "";

        $body = wp_remote_retrieve_body($response);
        $xml = simplexml_load_string($body);
        $posts = json_decode(json_encode((array)$xml->item->posts), true);

        foreach ($posts as $k => $v) {
            foreach ($v as $key => $value) {
                Logger::log("타이틀로 찾기 asdf: " . $value['title'] . $value['date'] . " vs " . $date);
                if ($value['title'] === $title) {
                    Logger::log($title . ", id 반환값: " . $value['id']);
                    return array(
                        'id' => $value['id'],
                        'url' => $value['postUrl'],
                        'date' => $value['date'],
                    );
                }
            }
        }
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
