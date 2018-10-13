<?php
namespace tistory_writer;
?>

<div class="mdl-tabs__panel" id="setting_step3-panel">
    <div class="intro_div">
        <main class="mdl-layout__content" style="width:100%;">
            <div class="page-content">
                <div class="mdl-card__supporting-text">
                    <h4>연동상태 확인</h4>
                    <ul id="status-list" class='mdl-list' style="width:700px;">
                        <li class="mdl-list__item mdl-list__item--two-line">
                            <span class="mdl-list__item-primary-content">
                                액세스 코드
                                <span class="mdl-list__item-sub-title" id="span_access_code">
                                <?php

                                use const tistory_writer\FEATURE_KEY\TISTORY_API;
                                use const tistory_writer\OPTION_KEY\BLOG_NAME;

                                $optionMgr    = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                                $access_token = $optionMgr->getOption(OPTION_KEY\ACCESS_TOKEN);
                                $apiMgr       = TistoryWriter::getManager(FEATURE_KEY\TISTORY_API);

                                if (isset($access_token) && $apiMgr->checkAccessToken()) {
                                    echo $access_token;
                                } else {
                                    echo "계정 연동이 필요합니다.";
                                }
                                ?></span>
                            </span>

                            <span class="mdl-list__item-secondary-content">
                                <a class="refresh_access_code mdl-list__item-secondary-action"><i class="material-icons">refresh</i>
                                </a>
                            </span>
                        </li>

	                    <?php
	                    $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
	                    $apiMgr = TistoryWriter::getManager(TISTORY_API);

	                    $blogInfo = $apiMgr->getBlogInformation();
	                    $blogs = $optionMgr->getOption(BLOG_NAME);

                        if (!is_null($blogs) && !empty($blogs)) {
	                        foreach ( $blogs as $blogitem ) {
		                        if ( is_null( $blogInfo ) ) {
			                        echo "<li id=\"li_blog_status_{$blogitem}\" class=\"mdl-list__item mdl-list__item--two-line\">
                                        <span class=\"mdl-list__item-primary-content\">
                                        <span>연동 블로그: {$blogitem}</span>
                                        <span class=\"mdl-list__item-sub-title\" id=\"blog_url_{$blogitem}\">";
			                        echo "액세스 코드를 받아오지 못하여 블로그 정보를 받아올 수 없습니다.";

			                        echo "
                                </span>
                            </span>
                            
                            <span class=\"mdl-list__item-secondary-content\">
                                <a id=\"refreshbtn_{$blogitem}\" class=\"refresh_blog_status mdl-list__item-secondary-action\"><i class=\"material-icons\">error</i>
                                </a>
                            </span>
                        </li>";
		                        } else {
			                        $found_url = null;

			                        foreach ( $blogInfo as $info ) {
				                        if ( $info->name == $blogitem ) {
					                        $found_url = $info->url;
					                        break;
				                        }
			                        }

			                        if ( ! is_null( $found_url ) ) {
				                        echo "<li id=\"li_blog_status_{$blogitem}\" class=\"mdl-list__item mdl-list__item--two-line\">
                                        <span class=\"mdl-list__item-primary-content\">
                                        <span>연동 블로그: {$blogitem}</span>
                                        <span class=\"mdl-list__item-sub-title\" id=\"blog_url_{$blogitem}\">";

				                        echo "<a href=\"{$found_url}\">{$info->title}({$found_url})</a>";

				                        echo "
                                </span>
                            </span>
                            
                            <span class=\"mdl-list__item-secondary-content\">
                                <a id=\"refreshbtn_{$blogitem}\" class=\"refresh_blog_status mdl-list__item-secondary-action\"><i class=\"material-icons\">check_circle</i>
                                </a>
                            </span>
                        </li>";
			                        } else {
				                        echo "<li id=\"li_blog_status_{$blogitem}\" class=\"mdl-list__item mdl-list__item--two-line\">
                                        <span class=\"mdl-list__item-primary-content\">
                                        <span>연동 블로그: {$blogitem}</span>
                                        <span class=\"mdl-list__item-sub-title\" id=\"blog_url_{$blogitem}\">";

				                        echo "유효하지 않은 블로그입니다. 연동 계정 설정에서 올바른 블로그 이름으로 설정해주세요.";

				                        echo "
                                </span>
                            </span>
                            
                            <span class=\"mdl-list__item-secondary-content\">
                                <a id=\"refreshbtn_{$blogitem}\" class=\"refresh_blog_status mdl-list__item-secondary-action\"><i class=\"material-icons\">error</i>
                                </a>
                            </span>
                        </li>";
			                        }
		                        }


	                        }
                        }
	                    ?>
                    </ul>
                </div>

            </div>
        </main>
    </div>
</div>