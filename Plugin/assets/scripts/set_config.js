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
    alert('set_userinfo');

    var list = document.getElementById('setup-list');

    var client_id = document.getElementById('textfield_clientid');
    var secret_key = document.getElementById('textfield_secretkey');

    //document.getElementById('form_blogname').value = blogname.value;

    //document.getElementById('auth_form').submit();

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
           alert(this.responseText);
        }
    };
    xmlhttp.open("GET", "options-general.php?page=tistory_writer?q=" + client_id, true);
    xmlhttp.send();
}

function clickedbutton(buttonid,orderid){



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
        var div = document.getElementById('main_div');
        var dd = document.getElementById('debug_div');
        div.style.visibility = 'hidden';
        dd.style.visibility = 'visible';

        var hash = window.location.hash.substring(1);
        var n = hash.replace(/access_token=/, "");
        var final = n.replace(/&state=/,"");
        setAccessCode(final);
    }
}

function IsIdentity(id) {
    var ul = document.getElementById("setup-list");

    for (var i = 0; i < ul.childElementCount; i++) {
        if (ul.children[i].id == id) {
            return false;
        }
    }
    return true;
}

function disableFooter() {
    document.getElementById('footer-thankyou').style.display = 'none';
    document.getElementById('footer-upgrade').style.display = 'none';
}

function addBlogNameEntry() {
    var ul = document.getElementById("setup-list");
    var li = document.createElement("li");
    var idx = Math.floor((Math.random() * 100) + 1);
    var newId = "li_blog_name_" + idx;

    while (!IsIdentity(newId)) {
        var idx = Math.floor((Math.random() * 100) + 1);

        var newId = "li_blog_name_" + idx;
    }

    li.setAttribute("id", "li_blog_name_"+idx);
    li.setAttribute("class", "listitem mdl-list__item");

    li.innerHTML = "<span class=\"mdl-list__item-primary-content\">\n" +
        "                                <div class=\"mdl-textfield mdl-js-textfield mdl-textfield--floating-label\">\n" +
        "                                    <input class=\"mdl-textfield__input\" pattern=\"[A-Z,a-z,0-9]*\" required type=\"text\" id=\"textfield_blogname_"+idx+"\">\n" +
        "                                    <label class=\"mdl-textfield__label\" for=\"textfield_blogname_"+idx+"\">BLOG NAME...</label>\n" +
        "                                </div>\n" +
        "                                </span>\n" +
        "                                <span class=\"mdl-list__item-secondary-content\">\n" +
        "                                    <button class=\"mdl-button mdl-js-button mdl-button--accent\"\n" +
        "                                            type=\"button\"\n" +
        "                                            id=\"del_blogname_" + idx + "\"\n" +
        "                                            onclick=\"deleteBlogNameEntry(this);\">삭제</button>\n" +
        "                                </span>";

    componentHandler.upgradeElement(li);

    ul.appendChild(li);

    componentHandler.upgradeDom();
}

function deleteBlogNameEntry(element) {
    var ul = document.getElementById("setup-list");
    var idx = element.id.replace("del_blogname_", "");
    var target_li = document.getElementById("li_blog_name_" + idx);

    ul.removeChild(target_li);
}

function adjustBlogUrlList(blogList, blogInformation) {
    var ul = document.getElementById("status-list");

    // Remove entires first
    while (ul.children.length != 1) {
        ul.removeChild(ul.lastChild);
    }

    for (var idx in blogList) {
        var li = document.createElement("li");
        var blogname = blogList[idx];
        var newId = "li_blog_status_" + blogname;

        li.setAttribute("id", newId);
        li.setAttribute("class", "mdl-list__item mdl-list__item--two-line");

        var blogExist = false;
        for (var j in blogInformation) {
            if (blogInformation[j].name == blogname) {
                blogExist = true;
                break;
            }
        }

        if (blogExist) {
            li.innerHTML =
                "<span class=\"mdl-list__item-primary-content\">" +
                "<span>연동 블로그: " + blogname + "</span>" +
                "<span class=\"mdl-list__item-sub-title\" id=\"blog_url_" + blogname + "\">" +
                "<a href=\"" + blogInformation[j].url + "\">" + blogInformation[j].title + "(" + blogInformation[j].url + ")</a>" +
                "</span></span>" +
                "<span class=\"mdl-list__item-secondary-content\">" +
                "<a id=\"refreshbtn_" + blogname + "\" class=\"refresh_blog_status mdl-list__item-secondary-action\"><i class=\"material-icons\">check_circle</i></a>" +
                "</span>";
        }
        else {
            li.innerHTML =
                "<span class=\"mdl-list__item-primary-content\">" +
                "<span>연동 블로그: " + blogname + "</span>" +
                "<span class=\"mdl-list__item-sub-title\" id=\"blog_url_" + blogname + "\">" +
                "존재하지 않는 블로그입니다. 다시 한번 확인해주세요." +
                "</span></span>" +
                "<span class=\"mdl-list__item-secondary-content\">" +
                "<a id=\"refreshbtn_" + blogname + "\" class=\"refresh_blog_status mdl-list__item-secondary-action\"><i class=\"material-icons\">error</i></a>" +
                "</span>";
        }

        componentHandler.upgradeElement(li);
        ul.appendChild(li);
    }
    componentHandler.upgradeDom();
}

