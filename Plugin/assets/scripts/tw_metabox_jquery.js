// global variables
var timer, subwin;

$(document).ready(function() {

    $("#refresh_access_code").click(function () {

        $.post(tw_ajax.ajax_url, {
            _ajax_nonce: tw_ajax.nonce,
            action: "getUrlForAccessToken"
        }, function(url) {
            subwin = window.open(url);
            timer = setInterval(checkChild, 500);
        })
    });

    $('#checkMakePublic').click(function() {
        if ($('#checkMakePublic').attr('value') == '2') {
            $('#checkMakePublic').attr('value', '0');
        }
        else {
            $('#checkMakePublic').attr('value', '2');
        }
    });

    $('#checkAllowComment').click(function() {
        if ($('#checkAllowComment').attr('value') == '2') {
            $('#checkAllowComment').attr('value', '0');
        }
        else {
            $('#checkAllowComment').attr('value', '1');
        }
    });

    $('#select_blogname').change(function() {
        $("#loading_post").innerText = "불러오는 중...";
        $("#loading_post").css('visibility', 'visible');

        var id = $(this).find(':selected')[0].value;

        $.post(tw_ajax.ajax_url, {
            _ajax_nonce: tw_ajax.nonce,
            action: "changeSelectedBlog",
            'selected_blog': id,
        }, function(data) {
            GetMetaBoxData();
        });
    });
});

function checkChild() {
    if (subwin.closed) {
        clearInterval(timer);
        location.reload();
    }
}

function GetMetaBoxData() {
    $.post(tw_ajax.ajax_url, {
        _ajax_nonce: tw_ajax.nonce,
        action: "getMetaBoxData",
        'title': $('#title').attr('value'),
        'wp_postId': new URL(window.location).searchParams.get("post"),
    }, function(data) {
        if (data != null) {
            ReflectMetadata(data);
        }
        else {
            $("#loading_post").innerText = "정보를 얻어오는데 실패했습니다.";
        }

        $("#loading_post").css('visibility', 'hidden');
    });

}

function ReflectMetadata(data) {
    var metadata = JSON.parse(data);

    var postvalue = metadata['detail'];
    var categories = metadata['category'];
    var linkFlag = metadata['linkFlag'];

    // 초기화
    $("#select_category").empty();
    $("#input_tag").attr('value', '');

    for(var item in categories) {
        $("#select_category")
            .append($('<option>', { value : categories[item]['id'] })
            .text(categories[item]['name']));
    }

    // 포스팅 주소 변경
    if (postvalue == null || postvalue['url'] == null) {
        $("#lbl_postLink").prop('innerHTML', '<label id="lbl_desc">티스토리 블로그에서 해당 글을 찾을 수 없습니다.</label>');
        $("#lbl_postLink").prop('text', "티스토리 블로그에서 해당 글을 찾을 수 없습니다.");
    }
    else {
        var a_tag = "<a href=\"" + postvalue['postUrl'] + "\">" + postvalue['postUrl'] + "</a>";
        $("#lbl_postLink").prop('innerHTML', a_tag);
    }

    // 공개 여부 변경
    if (postvalue == null || postvalue['visibility'] == null) {
        $("#checkMakePublic").prop('checked', false);
    }
    else {
        if (postvalue['visibility'] == 2) {
            $("#checkMakePublic").prop('checked', true);
        }
        else
        {
            $("#checkMakePublic").prop('checked', false);
        }
    }

    // 댓글 허용 옵션 변경
    if (postvalue == null || postvalue['acceptComment'] == null) {
        $("#acceptComment").prop('checked', false);
    } else {
        if (postvalue['acceptComment'] == 1) {
            $("#checkAllowComment").prop('checked', true);
        }
        else  {
            $("#checkAllowComment").prop('checked', false);
        }
    }

    if (postvalue != null && postvalue['categoryId'] != null) {
        $("#select_category").val(postvalue['categoryId']);
    }

    if (postvalue != null && postvalue['tags'] != null && postvalue['tags']['tag'] != null) {
        $("#input_tag").attr('value', postvalue['tags']['tag'].toString());
    }

    // 저장된 연동 여부 체크박스 불러옴
    $("#turnIntegratationOff").prop('checked', linkFlag);
}