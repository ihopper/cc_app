<?php

require 'modules/class.openid.php';
include_once "modules/cc-config.inc.php";


# Logging in with Google accounts requires setting special identity, so this example shows how to do it.


try {

    # Change 'localhost' to your domain name.

    $openid = new LightOpenID($home_url);

    if(!$openid->mode) {

        if(isset($_GET['login'])) {

            $openid->identity = 'https://www.google.com/accounts/o8/id';

            header('Location: ' . $openid->authUrl());

        }

?>

<form action="?login" method="post">

    <button>Login with Google</button>

</form>

<?php

    } elseif($openid->mode == 'cancel') {

        echo 'User has canceled authentication!';

    } else {

        echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';

    }

} catch(ErrorException $e) {

    echo $e->getMessage();

}