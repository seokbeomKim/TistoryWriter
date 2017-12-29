function step2_onClick() {
    var client_id = document.getElementById('client_id');
    var secret_key = document.getElementById('secret_key');
    var callback_url = document.getElementById('callback_url');
    var blogname = document.getElementById('blogname');
    var link = "?page=tistory_writer&tw_step2_completed=TRUE&client_id=" + client_id.value + "&secret_key=" + secret_key.value + "&callback_url=" + callback_url.value + "&blogname=" + blogname.value;

    document.location.replace(link);
}

function auth_submit() {
    var client_id = document.getElementById('client_id');
    var secret_key = document.getElementById('secret_key');
    var callback_url = document.getElementById('callback_url');
    
    document.getElementById('form_client_id').value = client_id.value;
    document.getElementById('form_redirect_url').value = callback_url.value;
    //document.getElementById('auth_form').submit();

    var link = "https://www.tistory.com/oauth/authorize?client_id="+client_id.value+"&redirect_uri="+callback_url.value+"&response_type=token";

    document.location.replace(link);
}

function init_auth() {
    var link = "?page=tistory_writer&tw_init_access_token=TRUE";

    document.location.replace(link);    
}