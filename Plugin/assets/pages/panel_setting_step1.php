<div class="mdl-tabs__panel" id="setting_step1-panel">
	<div class="intro_div">
		<main class="mdl-layout__content" style="width:100%;">
			<div class="page-content">
				<h4>OAUTH 인증 클라이언트 등록</h4>

                <div class="mdl-cell mdl-cell--8-col mdl-card__supporting-text no-padding">
                    <p>
                        <a href="http://www.tistory.com/guide/api/manage/register" target="_blank">
                            OAUTH 인증 등록페이지</a>로 접속한 뒤 아래의 정보들을 입력합니다.</p>


                    <ul class='mdl-list'>
                        <li class="mdl-list__item mdl-list__item--two-line">
                                <span class="mdl-list__item-primary-content">
                                    서비스명
                                    <span class="mdl-list__item-sub-title">
                                        티스토리에서 제공하는 API를 사용할 서비스명을 적습니다.
                                        TistoryWriter(또는 임의 이름)를 입력합니다.</span>
                                </span>
                        </li>
                        <li class="mdl-list__item mdl-list__item--two-line">
                                <span class="mdl-list__item-primary-content">
                                    설명
                                    <span class="mdl-list__item-sub-title">
                                    임의대로 작성합니다.</span>
                                </span>
                        </li>
                        <li class="mdl-list__item mdl-list__item--two-line">
                                <span class="mdl-list__item-primary-content">
                                    서비스 URL
                                    <span class="mdl-list__item-sub-title">
                                    본 워드프레스의 URL 주소, <?php print("http://" . $_SERVER['HTTP_HOST']); ?>를 입력합니다.
                                    </span>
                                </span>
                        </li>
                        <li class="mdl-list__item mdl-list__item--two-line">
                                <span class="mdl-list__item-primary-content">
                                    서비스 형태
                                    <span class="mdl-list__item-sub-title">
                                    웹 서비스를 선택합니다.
                                    </span>
                                </span>
                        </li>
                        <li class="mdl-list__item mdl-list__item--two-line">
                                <span class="mdl-list__item-primary-content">
                                    서비스 권한
                                    <span class="mdl-list__item-sub-title">
                                    읽기/쓰기 권한을 선택합니다.
                                    </span>
                                </span>
                        </li>
                        <li class="mdl-list__item mdl-list__item--two-line">
                                <span class="mdl-list__item-primary-content">
                                    Callback 경로
                                    <span class="mdl-list__item-sub-title" id="tw-callback-url">
                                    <?php echo get_admin_url() . 'options-general.php?page=tistory_writer'; ?>
                                    </span>
                                </span>
                        </li>
                    </ul>
                </div>

            </div>
		</main>
	</div>
</div>
