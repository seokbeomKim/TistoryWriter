<?php
namespace tistory_writer;
?>
<div class="tistory_writer intro_div mdl-tabs__panel" id="setting_step2-panel">
    <style>
        .listitem {
            width: 430px;
            padding: 0px;
        }
    </style>
    <main class="mdl-layout__content" style="width:100%;">
        <div class="page-content">
            <h4 id="step2">인증 정보 설정</h4>
            <div class="mdl-cell mdl-cell--8-col mdl-card__supporting-text no-padding" id="tistory_writer">
                <p>위 단계에서 등록한 클라이언트 정보를 아래에 입력한 후 저장 버튼을 클릭하여 인증 정보를 저장합니다.<br/>
                    티스토리 내 인증 관리페이지에 접속하기 위해서는 <a href="https://www.tistory.com/guide/api/manage/list">여기</a>를 클릭하세요.</p>
                    <form action="#" style="margin-left: 30px;">
                        <ul class="listitem mdl-list" id="setup-list">
                            <li class="listitem mdl-list__item">
                                <span class="mdl-list__item-primary-content">

                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" id="textfield_clientid"
                                    value="<?php

                                    use const tistory_writer\OPTION_KEY\BLOG_NAME;

                                    $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                                        if (!is_null($optionMgr)) {
                                            echo $optionMgr->getOption(OPTION_KEY\CLIENT_ID);
                                        }
                                    ?>">
                                    <label class="mdl-textfield__label" for="textfield_clientid">CLIENT ID...</label>
                                </div>
                                </span>
                            </li>
                            <li class="listitem mdl-list__item">
                                <span class="mdl-list__item-primary-content">
                                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                    <input class="mdl-textfield__input" type="text" id="textfield_secretkey"
                                           value="<?php
                                                $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                                                if (!is_null($optionMgr)) {
	                                                echo $optionMgr->getOption(OPTION_KEY\SECRET_KEY);
                                                }
                                                ?>">
                                    <label class="mdl-textfield__label" for="textfield_secretkey">SECRET KEY...</label>
                                </div>
                                </span>
                            </li>

	                        <?php
	                        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
	                        if (is_null($optionMgr)) {
		                        Logger::log("Option Manager를 얻어오는데 실패하였습니다.");
	                        }

	                        $blogs = $optionMgr->getOption(BLOG_NAME);
	                        if (!is_array($blogs)) {
	                            $blogs = (array)$blogs;
                            }

	                        for ($i = (int)0; $i < count($blogs); $i++) {
	                            $entry_idx = $i + 1;
	                            echo "<li class=\"listitem mdl-list__item\" id=\"li_blog_name_{$entry_idx}\">
                                <span class=\"mdl-list__item-primary-content\">
                                    <div class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\">
                                        <input class=\"mdl-textfield__input\" type=\"text\"
                                               value=\"{$blogs[$i]}\" id=\"textfield_blogname_{$entry_idx}\" pattern=\"[A-Z,a-z,0-9]*\" required >
                                        <label class=\"mdl-textfield__label\" for=\"textfield_blogname_{$entry_idx}\">BLOG NAME...</label>
                                    </div>
                                </span>
                                <span class=\"mdl-list__item-secondary-content\">
                                    <button class=\"mdl-button mdl-js-button mdl-button--accent\"
                                            type=\"button\"
                                            id=\"del_blogname_{$entry_idx}\"
                                            onclick=\"deleteBlogNameEntry(this);\">삭제
                                    </button>
                                </span>
                            </li>";
	                        }
	                        ?>

                        </ul>


                        <div>
                            <span style="width:100%">
                            <button class="tw-save-setting mdl-button mdl-js-button mdl-button--raised"
                                    type="button">
                                설정 저장
                            </button>
                            <button class="mdl-button mdl-js-button mdl-button--accent"
                                    type="button"
                                    onclick="addBlogNameEntry(1);">블로그 추가</button>
                            </span>
                        </div>
                    </form>

                <div id="toast-message" class="mdl-js-snackbar mdl-snackbar">
                    <div class="mdl-snackbar__text"></div>
                    <button class="mdl-snackbar__action" type="button"></button>
                </div>
                <p>
                    <br/>
                </p>
            </div>
        </div>
    </main>
</div>
