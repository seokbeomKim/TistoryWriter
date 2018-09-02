jQuery(document).ready(function($) {
    document.getElementById('footer-thankyou').style.display = 'none';
    document.getElementById('footer-upgrade').style.display = 'none';

    var client_id = document.getElementById('textfield_clientid').value;
    var secret_key = document.getElementById('textfield_secretkey').value;
    var callback_url = document.getElementById('tw-callback-url').innerText.trim();

    var access_token;

    // Check GET Values
    var url = new URL(window.location.href);
    var authCode = url.searchParams.get('code');

    if (authCode != null && authCode != "") {
        $(".dim-overlay").show();

        $.post(tw_ajax.ajax_url, {
            _ajax_nonce: tw_ajax.nonce,
            action: "requestAccessCodeWithAuth",
            'auth_code': authCode,
            'client_id': client_id,
            'secret_key': secret_key,
            'callback_url': callback_url,
        }, function (data) {
            access_token = data.replace("accces_token=", "");
            if (access_token != "") {
                opener.document.getElementById('tw-callback-url').innerText = access_token;
            }

            window.close();
        });
    }

    var url = window.location.hash;

    if (url == "") {
        $(".dim-overlay").hide();
        return;
    }

    $(".setting-spinner").css('visibility', 'visible');
    var getValues = url.split("&");

    var requiredAction = getValues[0].split("=")[0];

    if (requiredAction == "#access_token") {

        var access_token = getValues[0].split("=")[1];

        $.post(tw_ajax.ajax_url, {
            _ajax_nonce: tw_ajax.nonce,
            action: "requestAccessCode",
            'access_code': access_token,
        }, function (data) {
            // 액세스 코드 갱신
            if (opener != null) {
                var access_code_span = opener.document.getElementById('span_access_code');

                if (typeof(access_code_span) != "undefined" && access_code_span != null) {
                    access_code_span.innerText = access_token;

                    var json_data = JSON.parse(data);

                    if (json_data != null) {
                        // 블로그 정보 갱신
                        for (var i in json_data) {
                            var blogurl_span = opener.document.getElementById('blog_url_' + json_data[i].name);
                            if (blogurl_span != null) {
                                blogurl_span.innerHTML = "<a href=\"" + json_data[i].url + "\">" + json_data[i].title + "(" + json_data[i].url + ")</a>";
                            }
                        }
                    }
                }
            }

            window.close();
        });
    }
});

jQuery(document).ready(function($) {
    $(".tw-save-setting").click(function () {             //event

        $(".dim-overlay").show();

        var client_id = document.getElementById('textfield_clientid').value.trim();
        var secret_key = document.getElementById('textfield_secretkey').value.trim();
        var callback_url = document.getElementById('tw-callback-url').innerText.trim();
        var blogname = [];

        var ul = document.getElementById('setup-list');
        for (var i = 2; i < ul.childElementCount; i++) {
            var tidx = ul.children[i].id.replace("li_blog_name_", "");
            var target_name = $("#textfield_blogname_" + tidx).get(0).value.trim();

            if (target_name == null || target_name == "") {
                var snackbarContainer = document.querySelector('#toast-message');
                var snackbarMessage = {message: '유효하지 않은 블로그 이름으로 설정 실패'};
                snackbarContainer.MaterialSnackbar.showSnackbar(snackbarMessage);
                $(".dim-overlay").hide();

                return;
            }

            if (blogname.includes(target_name)) {
                var snackbarContainer = document.querySelector('#toast-message');
                var snackbarMessage = {message: '블로그 이름 중복으로 설정 실패'};
                snackbarContainer.MaterialSnackbar.showSnackbar(snackbarMessage);
                $(".dim-overlay").hide();

                return;
            }

            blogname.push(target_name);
        }

        var this2 = this;                      //use in callback
        $.post(tw_ajax.ajax_url, {         //POST request
            _ajax_nonce: tw_ajax.nonce,     //nonce
            action: "saveSettings",            //action
            'client_id': client_id,                  //data
            'secret_key': secret_key,
            'blog_name': blogname,
        }, function (blogs) {                    //callback
            $(".dim-overlay").hide();

            // 블로그 연동 상태 엔트리 수정
            adjustBlogUrlList(blogname, JSON.parse(blogs));

            var snackbarContainer = document.querySelector('#toast-message');
            var snackbarMessage = {message: '연동 계정정보 저장 완료'};
            snackbarContainer.MaterialSnackbar.showSnackbar(snackbarMessage);
        });
    });

});

jQuery(document).ready(function($) {

    $(".refresh_blog_status").click(function () {
        let blogname;
        blogname = this.id.replace("refreshbtn_", "");
        const url_label = document.getElementById('blog_url_' + blogname);

        $.post(tw_ajax.ajax_url, {
            _ajax_nonce: tw_ajax.nonce,
            action: "requestBlogUrl",
            'blog_name': blogname,
        }, function (data) {
            // 액세스 코드 갱신
            if (data == "FAIL") {
                url_label.innerText = "존재하지 않는 블로그입니다. 다시 한번 확인해주세요.";
            }
            else {
                var json_data = JSON.parse(data);
                var title = json_data[0];
                var url = json_data[1];

                url_label.innerHTML = "<a href=\"" + url + "\">" + title + "(" + url + ")</a>";
            }
        });
    });
});

jQuery(document).ready(function($) {

    $(".refresh_access_code").click(function () {
        var client_id = document.getElementById('textfield_clientid').value;
        var secret_key = document.getElementById('textfield_secretkey').value;
        var callback_url = document.getElementById('tw-callback-url').innerText.trim();

        window.open("https://www.tistory.com/oauth/authorize?client_id=" + client_id +
            "&redirect_uri=" + callback_url + "&response_type=token");
    });
});