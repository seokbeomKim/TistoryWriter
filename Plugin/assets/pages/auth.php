<?php
/**
 * auth.php: tistory api에서 Access Token 얻기 위한 HTML submit form
 */
namespace tistory_writer;

?>
<html>
<body>
    <head>
        <script type="text/javascript">
        function request_auth() {
            window.location = '<?php echo get_admin_url() . "/options-general.php?page=tistory_writer"; ?>';
        }
        </script>
    </head>
    <label>
        Access 토큰 값이 유효하지 않습니다.
    </label>
    <p>
        <a class="button" onClick="request_auth();">계정 설정</a>
    </p>
</body>
</html>
