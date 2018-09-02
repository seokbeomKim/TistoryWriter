<?php
namespace tistory_writer;

global $post;

use const tistory_writer\ERRORS\ACCESS_TOKEN_EXPIRED;
use const tistory_writer\FEATURE_KEY\OPTION;
use const tistory_writer\FEATURE_KEY\TISTORY_API;
use const tistory_writer\OPTION_KEY\ACCESS_TOKEN;
use const tistory_writer\OPTION_KEY\CLIENT_ID;
use const tistory_writer\OPTION_KEY\REDIRECT_URI;
use const tistory_writer\OPTION_KEY\SELECTED_BLOG;

$isAccessTokenAvailable = false;

$api_mgr = TistoryWriter::getManager(TISTORY_API);

// 액세스 토큰 유효성 검사
if ($api_mgr->checkAccessToken()) {
	$isAccessTokenAvailable = true;
}

if (!$isAccessTokenAvailable) {
?>
<div id="div_require_token" style="padding: 10px; vertical-align:center;">
    <span style="padding: 5px; vertical-align: center; height: 50px;">
        액세스 토큰이 설정되지 않았습니다. 계정 설정을 먼저 해주세요.</span>
    <div style="padding: 5px; margin-top: 5px;">
        <input type="button" value="액세스 토큰 갱신" id="refresh_access_code" class="button" />
    </div>
</div>

<?php
} else {
?>
<div id="tw_metabox">
    <style>
        .cell-content {
            vertical-align: middle;
        }

        #tw_metabox {
            margin: 5px;
            vertical-align: middle;
        }
    </style>
	<div id="tw-table">
		<div class="tw-row">
			<div class="tw-cell tw-entryname">
				<span class="cell-content" style="margin-left: 3px;">연동 계정</span></div>
			<div class="tw-cell">
				<span id="blogAccount" class="blogAccount">
				<?php
				if ($isAccessTokenAvailable) {
					$account = $api_mgr->getBlogAccount();
					if ( ! is_null($account) && ! empty($account) ) {
						echo esc_html($account);
					}
				} else {
					Logger::log(ACCESS_TOKEN_EXPIRED);
				}
				?>
				</span>
			</div>
		</div>

		<div class="tw-row">
			<div class="tw-entryname tw-cell">블로그 선택</div>
			<div class="tw-cell">
				<select id="select_blogname" name="select_blogname">
					<?php
					$api_mgr = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);
					$blogs   = $api_mgr->getBlogInformation();

					if (is_null($blogs)) {
						echo ACCESS_TOKEN_EXPIRED;
					}
					else {
						// Set default 'SELECTED BLOG' as 0 index
						$optionMgr = TistoryWriter::getManager( OPTION );
						$selectedBlog = $optionMgr->getOption(SELECTED_BLOG);

						if (is_null($selectedBlog) || $selectedBlog == "") {
							$optionMgr->setOption(SELECTED_BLOG, $blogs[0]->name);
							$selectedBlog = $blogs[0]->name;
                        }

						foreach ( $blogs as $blog ) {
						    if ($blog->name == $selectedBlog) {
							    echo "<option value=\"{$blog->name}\" selected>{$blog->name}</option>";
						    }
						    else {
							    echo "<option value=\"{$blog->name}\">{$blog->name}</option>";
						    }
						}
					}
					?>
				</select>
                <label id="loading_post" style="visibility: hidden;">  불러오는중...</label>
			</div>
		</div>



		<div class="tw-row">
			<div class="tw-cell tw-entryname">포스팅 주소</div>
			<div class="tw-cell">
                <label id="lbl_postLink">
                <?php

                $post_info = $api_mgr->getPostInfoWithTitle($post->post_title);

                if (isset($post_info)) {
					?>
					<a href="<?php echo esc_url($post_info['url']); ?>">
						<?php
						echo esc_url($post_info['url']);
						?>
					</a>
					<?php
				} else {
					echo "<label id='lbl_desc'>티스토리 블로그에서 해당 글을 찾을 수 없습니다.</label>";
				}
				echo "<input type=\"hidden\" name=\"postId\" id=\"postId\" value=\"" . esc_html($post_info['id']) . "\"/>";
				?>
                </label>
            </div>
		</div>

		<div class="tw-row">
			<div class="tw-entryname tw-cell">공개 여부</div>
			<div class="tw-cell">
                <span>
                    <?php

                    if (isset($post_info) && $api_mgr->getVisibilityWithPostId($post_info['id']) == 2) {
	                    echo "<input type=\"checkbox\" name=\"checkMakePublic\" value=\"2\" id=\"checkMakePublic\" content=\"공개\" checked />";
                    }
                    else {
	                    echo "<input type=\"checkbox\" name=\"checkMakePublic\" value=\"0\" id=\"checkMakePublic\" content=\"비공개\" />";
                    }
                    ?>
                </span>
            </div>
		</div>

		<div class="tw-row">
			<div class="tw-entryname tw-cell">댓글 허용</div>
			<div class="tw-cell">
                <span>
                    <?php
                    if (isset($post_info) && $api_mgr->getAllowCommentWithPostId($post_info['id']) == 1) {
	                    echo "<input type=\"checkbox\" name=\"checkAllowComment\" value=\"1\" id=\"checkAllowComment\" checked />";
                    }
                    else {
                        echo "<input type=\"checkbox\" name=\"checkAllowComment\" value=\"0\" id=\"checkAllowComment\" />";
                    }
                    ?>
                </span>
			</div>
		</div>

		<div class="tw-row">
			<div class="tw-entryname tw-cell">분류 선택</div>
			<div class="tw-cell">
				<select name="select_category" id="select_category">
					<?php
					$categories = $api_mgr->getCategoryList();

					if (!is_null($categories)) {
						foreach ( $categories as $k => $v ) {

							if ( isset($post_info) && $v['id'] == $post_info['category_id'] ) {
								echo '<option value="' . esc_html( $v['id'] ) . '" selected="selected" >' .
								     esc_html( $v['label'] ) . '</option>';
							} else {
								echo '<option value="' . esc_html( $v['id'] ) . '">' .
								     esc_html( $v['label'] ) . '</option>';
							}
						}
					}
					?>
				</select>
			</div>
		</div>

		<div class="tw-row">
			<div class="tw-entryname tw-cell">태그</div>
			<div class="tw-cell">
				<input type="text" name="input_tag" id="input_tag" value="<?php

                if (isset($post_info)) {
	                $tags = $api_mgr->getTagsWithPostId($post_info['id']);
	                $tValue = "";

	                if (isset($tags['tag'])) {
		                for ($i = 0; $i < count($tags['tag']); $i++) {
			                $tValue .= wp_kses_post($tags['tag'][$i]) . ", ";
		                }
	                }

	                echo substr($tValue, 0, -2);
                }



				?>" />
			</div>
		</div>

        <div class="tw-row">
            <div class="tw-cell tw-entryname">연동 임시해제</div>
            <div class="tw-cell">
			<input type="checkbox" name="turnIntegratationOff" value="off" id="turnIntegratationOff" />
            </div>
		</div>
	</div>

</div>
<?php
}
?>