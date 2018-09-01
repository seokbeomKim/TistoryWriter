function IsIdentity(id) {
    var ul = document.getElementById("setup-list");

    for (var i = 0; i < ul.childElementCount; i++) {
        if (ul.children[i].id == id) {
            return false;
        }
    }
    return true;
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

