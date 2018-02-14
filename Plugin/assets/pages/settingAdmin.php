<?php
/**
 * settingAdmin.php 페이지 파일에서 사용하는 php 코드의 네임스페이스 설정
 */
namespace tistory_writer;

?>
<html>
<body id="tistory_writer" onLoad="checkAuthCode()">
    <div>
    <p>
        Tistory Writer 플러그인을 사용해주셔서 감사합니다. <br />
        Tistory Writer는 워드프레스로 작성한 글을 티스토리 블로그에 자동으로 등록하는 플러그인입니다.
    </p>

    <div class="div_setting_step">
        <h3 class="header">#1. 클라이언트 ID 등록</h3>
        <p>티스토리 API를 이용하기 위해, 클라이언트 ID 등록이 필요합니다.</p>
        <ol>
            <li>티스토리 내 <a href="http://www.tistory.com/guide/api/manage/register">OpenAPI 페이지</a>에 접속합니다.</li>
            <li>아래와 같이 입력합니다.</li>
            <div>
            <ul style="list-style-type: circle;">
                <li>서비스 명: TistoryWriter </li>
                <li>설명: 원하는대로 적으시면 됩니다.</li>
                <li>서비스 URL: <?php print("http:// " . $_SERVER['HTTP_HOST']); ?></li>
                <li>서비스 권한: 읽기/쓰기</li>
                <li>Callback 경로: 플러그인이 설치된 경로(아래 텍스트박스에 있는 경로를 입력합니다.)</li>
            </ul>
            </div>
        </ol>
    </div>
    <div class="div_setting_step">
        <h3 class="header">#2. 클라이언트 ID 정보 설정</h3>
        <p>1단계에서 설정한 클라이언트 정보를 입력합니다. (참고. 블로그 이름 란에는 블로그 주소 https://xxx.tistory.com 의 xxx 부분만 입력해주시면 됩니다.)</p>

        <table class="table">
            <tr>
                <td class="td_label">
                    <label>Client ID</label>
                </td>
                <td>
                    <input size="50" type="text" id="input_client_id" value="<?php
                    $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                    echo $optionMgr->getOption(OPTION_KEY\CLIENT_ID);
                    ?>">
                </td>
            </tr>

            <tr>
                <td>
                    <label>Secret Key</label>
                </td>
                <td>
                    <input size="50" type="text" id="input_secret_key" value="<?php
                    $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                    echo $optionMgr->getOption(OPTION_KEY\SECRET_KEY);
                    ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label>블로그 이름</label>
                </td>
                <td>
                    <input size="50" type="text" id="input_blogname" value="<?php
                    $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                    echo $optionMgr->getOption(OPTION_KEY\BLOG_NAME);
                    ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Callback URL</label>
                </td>
                <td>
                    <label id="input_callback_url">
                        <?php
                        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                        $v = $optionMgr->getOption(OPTION_KEY\CALLBACK_URL);
                        if (empty($v)) {
                            echo get_admin_url() . 'options-general.php?page=tistory_writer';
                        } else {
                            echo $v;
                        }
                        ?>
                    </label>
                </td>
            </tr>

        </table>
        <p>
            <a class="button" onClick="set_userinfo();">ID정보 저장</a>
        </p>
    </div>

    <div class="div_setting_step">
        <h3 class="header">#3. 설정 완료</h3>
        <p>
        아래의 '계정 연동' 버튼을 클릭하여 설정을 마무리합니다. 기존 연동은 연동 해제 버튼으로 해제할 수 있습니다.
        </p>
        <p>
            <a class="button" onClick="set_auth();">계정 연동</a>
            <a class="button" onClick="init_auth();">연동 해제</a>
        </p>



        <!-- 사용자 입력 정보 설정 위한 Hidden Form -->
        <form id="auth_form" method="post" action="<?php echo get_admin_url() .
        'admin-post.php'; ?>">
            <input type='hidden' name='action' value='submit-tw-info' />
            <input type="hidden" id="form_client_id" name="client_id" value="{발급받은 client_id를 입력}"/>
            <input type="hidden" id="form_secret_key" name="secret_key" value="{발급받은 secret_key 입력}" />
            <input type="hidden" id="form_callback_url" name="callback_url" value="" />
            <input type="hidden" id="form_blogname" name="blogname" value="{연동하고자 하는 블로그 이름 입력}" />
            <input type="hidden" id="form_redirect_url" name="redirect_uri" value="{등록시 입력한 redirect uri를 입력}"/>
            <input type="hidden" id="form_response_type" name="response_type" value="token"/>
            <input type="hidden" id="form_access_code" name="access_code" value=""/>
            <input type="hidden" id="form_redirect_def" name="redirect_def" value="SETTINGINFO" />
        </form>

        <form id="auth_request_form" method="GET" action="https://www.tistory.com/oauth/authorize/">
            <input type="hidden" name="client_id" id="req_client_id" value="{발급 받은 client_id}"/>
            <input type="hidden" name="redirect_uri" id="req_redirect_uri" value="{설정한 redirect uri}" />
            <input type="hidden" name="response_type" id="req_response_type" value="token"/>
        </form>
    </div>
    <div class="div_setting_step">
        <h3 header="header">#4. 연동 정보</h3>
        <p>설정이 정상적으로 완료되면 엑세스 토큰 정보가 활성화 됩니다.</p>
        <table class="table">
            <tr>
                <td>
                    <label class="lbl_access_token">Access Token</label>
                </td>
                <td>
                    <label class="lbl_access_token">
                        <?php
                        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
                        $access_token = $optionMgr->getOption(OPTION_KEY\ACCESS_TOKEN);

                        Logger::log("access token: " . $access_token);
                        if (!empty($access_token)) {
                            echo $access_token;
                        } else {
                            echo "계정 연동이 필요합니다.";
                        }
                        ?>
                    </label>
                </td>
            </tr>
        </table>
    </div>
    </div>
    </body>
</html>
<?php
