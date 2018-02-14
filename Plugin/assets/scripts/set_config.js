/*
 * set_config.js
 *
 * 플러그인 설정 페이지에서 사용하는 자바 스크립트 파일
 *
 */
function init_auth() {
    document.getElementById('form_redirect_def').value = "SETTINGINFO_RESET";
    document.getElementById('auth_form').submit();
}

/**
 * 사용자가 입력한 정보를 바탕으로 플러그인의 기본 정보를 submit form에
 * 설정하고 WP 플러그인 API 통해 POST로 넘김
 */
function set_userinfo() {
    var client_id = document.getElementById('input_client_id');
    var secret_key = document.getElementById('input_secret_key');
    var callback_url = document.getElementById('input_callback_url');
    var blogname = document.getElementById('input_blogname');
    document.getElementById('form_client_id').value = client_id.value;
    document.getElementById('form_secret_key').value = secret_key.value;
    document.getElementById('form_callback_url').value = callback_url.textContent;
    document.getElementById('form_blogname').value = blogname.value;

    document.getElementById('auth_form').submit();
}

function set_auth() {
    var request_form = document.getElementById('auth_request_form');
    var callback_url = document.getElementById('input_callback_url');

    document.getElementById('req_client_id').value = document.getElementById('input_client_id').value;
    document.getElementById('req_redirect_uri').value = callback_url.textContent.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    document.getElementById('form_redirect_def').value = "SETTINGINFO";
    request_form.submit();
}

function setAccessCode(v) {
    var access_code = document.getElementById('form_access_code');
    var redirect_def = document.getElementById('form_redirect_def');
    access_code.value = v;
    redirect_def.value = "SETTING_ACCESSCODE";
    document.getElementById('auth_form').submit();
}

function checkAuthCode()
{
    if(window.location.hash) {
        var hash = window.location.hash.substring(1);
        var n = hash.replace(/access_token=/, "");
        var final = n.replace(/&state=/,"");
        setAccessCode(final);
    }
}
