<?php
namespace tistory_writer;

/* metabox debug */
TistoryWriter::init();
$api_mgr = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);

global $wp;
$current_url = home_url(add_query_arg(array(), $wp->request));

if (method_exists('\\tistory_writer\\Logger', 'log')) {
    Logger::log("Metabox url = " . $current_url);
}

TistoryWriter::resetCount();

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

        </tr>
        <tr class="tr">
            <td class="td_label">
                <label>연동 계정</label>
            </td>
            <td>
                <!-- 사용자 카테고리 선택 -->
                <label id="lbl_desc">  <b>
                <?php
                // The user account must be 'email' type.
                $account = $api_mgr->getBlogAccount();
                if (!isset($account)) {
                    echo "Access 토큰이 정상적으로 발급되지 않았습니다.";
                } else {
                    echo esc_html($account);
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

                $acceptComment = $api_mgr->getVisibilityWithPostId($post_info['id']);

                if (isset($post_info['id'])) {
                ?>
                <a name="lbl_postLink" href="<?php echo esc_url($post_info['url']); ?>">
                <?php
                echo esc_url($post_info['url']);
                ?>
                </a>
                <input type="hidden" name="postId" id="postId" value="<?php echo esc_html($post_info['id']); ?>"/>
                <?php
                } else {
                    echo "<label id='lbl_desc'>티스토리 블로그에서 해당 글을 찾을 수 없습니다.</label>";
                    echo "<input type='hidden' name='postId' id='postId'  value='-1' />";
                }?>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                공개 여부
            </td>
            <td>
                <select id="select_visibility" name="select_visibility">
                    <?php
                    if ($post_info['visibility'] == 0) {
                    ?>
                    <option value="0" selected>비공개</option>
                    <option value="2">공개</option>
                    <?php
                    } else {
                    ?>
                    <option value="0">비공개</option>
                    <option value="2" selected>공개</option>
                    <?php
                    }
                    ?>
                </select>

                <!-- <input type="checkbox" name="checkProtected" value="protected"
                 id="checkProtected">
                <label for="checkProtected">글 보호</label> -->

                <input type="checkbox" name="checkAllowComment" value="protected" id="checkAllowComment" <?php
                if ($acceptComment == 1) {
                    echo "checked";
                }
                ?>>
                <label for="checkAllowComment" <?php
                if ($acceptComment == 0) {
                    echo "checked";
                }
                ?>>댓글 허용</label>
            </td>
        </tr>
        <tr class="tr">
            <td class="td_label">
                <label>분류 선택</label>
            </td>
            <td>
                <!-- 사용자 카테고리 선택 -->
                <select name="select_category" id="select_category" ?>">
                    <?php
                    $categories = $api_mgr->getCategoryList();

                    foreach ($categories as $k => $v) {
                        foreach ($v as $key => $value) {
                            if ($value['id'] == $post_info['category_id']) {
                                echo '<option value="' . esc_html($value['id']) . '" selected>' .
                                esc_html($value['label']) . '</option>';
                            } else {
                                echo '<option value="' . esc_html($value['id']) . '">' .
                                esc_html($value['label']) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr class="tr">
            <td class="td_label">
                태그
            </td>
            <td>
                <input type="text" name="input_tag" id="input_tag" value="<?php
                $tags = $api_mgr->getTagsWithPostId($post_info['id']);
                $tValue = "";

                if (isset($tags['tag'])) {
                    for ($i = 0; $i < count($tags['tag']); $i++) {
                        $tValue .= wp_kses_post($tags['tag'][$i]) . ", ";
                    }
                }
                echo substr($tValue, 0, -2);
                ?>" />
            </td>
        </tr>
    </table>
    <div>
        <input type="checkbox" name="turnIntegratationOff" value="off" id="turnIntegratationOff" />
        <label id="turnOffLbl" for="turnIntegratationOff">연동 기능을 임시해제합니다.</label>
        </input>
    </div>
</div>
<?php
} ?>
