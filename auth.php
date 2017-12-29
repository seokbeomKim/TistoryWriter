<?php
echo '

<html>
<head>
        <title>Tistory OAuth 2.0 JSP Sample - Example Authorization Code </TITLE>
</head>
<body>
    <form method="GET" action="https://www.tistory.com/oauth/authorize/">
        <input type="hidden" name="client_id" value="{발급 받은 client_id}"/>
        <input type="hidden" name="redirect_uri" value="./auth_r.php"/>
        <input type="hidden" name="response_type" value="code"/>
        <input type="hidden" name="state" value="someValue"/>
            <button type="submit">Request Athorization Code</button>
    </form>
</body>
</html>

'?>