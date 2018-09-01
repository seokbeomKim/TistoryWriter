<?php
namespace tistory_writer;

//add_action( 'admin_enqueue_scripts', 'tistory_writer\TistoryWriter', 'loadFiles' );

?>
<html lang="en">
<body id="tistory_writer" class="tistory_writer">
<div class="dim-overlay"></div>
<div class="mdl-layout__container">
<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">


    <div class="mdl-tabs__tab-bar">
        <a href="#intro-panel" class="mdl-tabs__tab is-active">플러그인 소개</a>
        <a href="#setting_main-panel" class="mdl-tabs__tab">계정정보 설정</a>
        <a href="#setting_step1-panel" class="mdl-tabs__tab"><p>STEP 1<br/>OAUTH 등록</p></a>
        <a href="#setting_step2-panel" class="mdl-tabs__tab"><p>STEP 2<br/>Key 설정</p></a>
        <a href="#setting_step3-panel" class="mdl-tabs__tab"><p>STEP 3<br/>연동 상태</p></a>
        <!-- <a href="#setting_step3-panel" class="mdl-tabs__tab">글 내보내기</a> -->
        <a href="#changelog-panel" class="mdl-tabs__tab">업데이트 내역</a>
    </div>


    <!-- 소개 탭 -->
    <?php include "panel_intro.php"; ?>

    <!-- 설정 탭 -->
    <?php include "panel_setting_main.php"; ?>
    <?php include "panel_setting_step1.php"; ?>
	<?php include "panel_setting_step2.php"; ?>
	<?php include "panel_setting_step3.php"; ?>
	<?php include "panel_changelog.php"; ?>

</div>
    <div class="setting-spinner mdl-spinner mdl-js-spinner is-active" style="top: 50%; left: 50%; visibility: hidden;"></div>

</div>
</body>
</html>
