<?php
namespace tistory_writer;

/* metabox debug */
TistoryWriter::init();
$api_mgr = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);

global $wp;
$current_url = home_url(add_query_arg(array(), $wp->request));

Logger::log("Metabox url = " . $current_url);

if (!$api_mgr->checkAccessToken()) {
    $api_mgr->refreshAccessToken();
} else {
    global $post;
    /* POST 방식으로 Metabox 데이터 전달하기 위한 변수 설정 */
    $post_visibility = get_post_meta($post->ID, '_select_visibility', true);
    $post_category = get_post_meta($post->ID, '_select_category', true);
    $post_protect = get_post_meta($post->ID, '_checkProtected', true);
    $post_allowcomment = get_post_meta($post->ID, '_checkAllowComment', true);
    $post_tag = get_post_meta($post->ID, '_input_tag', true);
    $post_link = get_post_meta($post->ID, '_postId', true);
    $post_switch = get_post_meta($post->ID, '_turnIntegratationOff', true);

    wp_nonce_field('reference_meta_box', 'reference_nonce');
?>
<div id="tw_metabox">
    <table class="table">
        <tr class="tr">
            <td class="td_label">
                연동 기능
            </td>
            <td>
                <input type="checkbox" name="turnIntegratationOff" value="off" id="turnIntegratationOff">
                <label for="turnIntegratationOff">임시로 글 올리기 기능을 끕니다. (체크 시 기능 OFF)</label>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                <label>연동 계정</label>
            </td>
            <td>
                <!-- 사용자 카테고리 선택 -->
                <label id="lbl_desc">  <b>
                <?php
                $account = $api_mgr->getBlogAccount();
                if (empty($account)) {
                    echo "Access 토큰이 정상적으로 발급되지 않았습니다.";
                } else {
                    echo $account;
                }
                ?>
                </b></label>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                포스팅 주소
            </td>
            <td>
                <?php
                $post_info = $api_mgr->getPostInfoWithTitle($post->post_title, get_the_date("Y-m-d h:i:s", $post->ID));

                if (!empty($post_info['id'])) {
                ?>
                <a name="lbl_postLink" href="<?php echo $post_info['url']; ?>">
                <?php
                echo $post_info['url'];
                ?>
                </a>
                <input type="hidden" name="postId" id="postId" value="<?php echo $post_info['id']; ?>"/>
                <?php
                } else {
                    echo "<label id='lbl_desc'>티스토리 블로그에서 해당 글을 찾을 수 없습니다.</label>";
                }
                ?>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                <label>분류 선택</label>
            </td>
            <td>
                <!-- 사용자 카테고리 선택 -->
                <select name="select_category" id="select_category">
                    <?php
                    $categories = $api_mgr->getCategoryList();

                    foreach ($categories as $k => $v) {
                        foreach ($v as $key => $value) {
                            echo '<option value="' . $value['id'] . '">' .
                            $value['label'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                공개 여부
            </td>
            <td>
                <select id="select_visibility" name="select_visibility">
                    <option value="0">비공개</option>
                    <option value="2">공개</option>
                </select>

                <input type="checkbox" name="checkProtected" value="protected"
                 id="checkProtected">
                <label for="checkProtected">글 보호</label>

                <input type="checkbox" name="checkAllowComment" value="protected" id="checkAllowComment">
                <label for="checkAllowComment">댓글 허용</label>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                태그
            </td>
            <td>
                <input size="50" type="text" name="input_tag" id="input_tag" value="" />
            </td>
        </tr>
    </table>

</div>
<?php
} ?>
