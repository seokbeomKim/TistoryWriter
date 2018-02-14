<?php
/**
 * auth.php: tistory api에서 Access Token 얻기 위한 HTML submit form
 */
namespace tistory_writer;

?>
<html>
<body>
    <form method="GET" action="https://www.tistory.com/oauth/authorize/">
        <input type="hidden" name="client_id" value="<?php
        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
        echo $optionMgr->getOption(OPTION_KEY\CLIENT_ID);
        ?>"/>
        <input type="hidden" name="redirect_uri" value="<?php
        $optionMgr = TistoryWriter::getManager(FEATURE_KEY\OPTION);
        //echo $optionMgr->getOption(OPTION_KEY\CALLBACK_URL);
        echo "127.0.0.1";
        ?>"/>
        <input type="hidden" name="response_type" value="code"/>
        <input type="hidden" name="state" value="someValue"/>
        <button type="submit">Request Athorization Code</button>
    </form>
</body>
</html>
