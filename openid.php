<?php

require 'modules/class.openid.php';
include_once "modules/cc-config.inc.php";

try {

    # Change 'localhost' to your domain name.

    $openid = new LightOpenID($home_url);

    if(!$openid->mode) {

        if(isset($_POST['openid_identifier'])) {

            $openid->identity = $_POST['openid_identifier'];

            # The following two lines request email, full name, and a nickname

            # from the provider. Remove them if you don't need that data.

            $openid->required = array('contact/email');

            $openid->required = array('namePerson');

            header('Location: ' . $openid->authUrl());

        }

?>

<form action="" method="post">

    OpenID: <input type="text" name="openid_identifier" /> <button>Submit</button>

</form>

<?php

    } elseif($openid->mode == 'cancel') {

        echo 'User has canceled authentication!';

    } else {

        echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';

	print_r($openid->getAttributes());

    }

} catch(ErrorException $e) {

    echo $e->getMessage();

}