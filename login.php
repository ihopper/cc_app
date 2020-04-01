<?php
	//Maintain session
	session_start();

	//Include cc-authorize module
	include 'modules/cc-authorize.php';

	//Retrieve cookies, if set.
	$cc_username = $_COOKIE['cc_username'];
	$cc_password = $_COOKIE['cc_password'];
	$cc_remember = $_COOKIE['cc_remember'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
	<title><?php echo $_SESSION['app_title']; ?></title>

<link href="defaultTheme.css" rel="stylesheet" media="screen" />
<link href="js/css/jquery-ui-1.8.20.custom.css" rel="stylesheet" media="screen" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link href="js/css/jquery_notification.css" type="text/css" rel="stylesheet"/>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>

<script type="text/javascript" src="js/jquery.fixedheadertable.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="js/cc-functions.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/jquery_notification_v.1.js"></script>


<script type="text/javascript">
function showError() {
	showNotification({
		message: "<?php echo $_SESSION['err_msg']; ?>",
		type: "error",
		autoClose: true,
		duration: 5
	});
	
};
</script>
</head>

<body>
<div id="messages"></div>

<?php 

	if($_SESSION['err_msg'] != '') {
		echo "<script type='text/javascript'>";
			echo "showError();";
		echo "</script>";

		//Unset the error message
		$_SESSION['err_msg'] = '';
	}
?>


<div id="cc-login">
	<div id="login-prompt">
		<div class="float-rt" style="margin-top: 30px; margin-right: 50px;">
			<div id="cc-login-top" style="text-align: center;">
				<a href="login.php?action=forgot_pass" class="text-small" style="color: #666; text-decoration: underline; font-weight: normal;">Forgot Password?</a>
				<a href="register.php" class="text-small" style="color: #666; text-decoration: underline; font-weight: normal;">Create <i>Free</i> Account</a>
			</div><!-- .cc-login-top -->
			<div class="clear" style="height: 30px;"></div>
			<div id="cc-login-mid">
			<form method="post" action="login.php" id="frmLogin">
				
				<?php if ($action == 'forgot_pass') { ?>
					<input type="hidden" name="action" value="reset_pass">
				<?php } else { ?>
					<input type="hidden" name="action" value="login">
				<?php } ?>

				<div class="clear" style="height: 15px;"></div>
				<label for="username" class="label text-med" style="width: 75px;">Email:</label>
				<input id="username" name="username" class="input-dark input-150 required" value="<?php echo $cc_username; ?>">
				<?php if ($action == 'forgot_pass') { ?>
					<div class="clear" style="height: 10px;"></div>
					<div style="text-align: right;">
						<button class="btn-green text-small">Reset Password</button><br />
					</div>
				<?php } else { ?>
					<div class="clear" style="height: 6px;"></div>
					<label for="pass" class="label text-med" style="width: 75px;">Password:</label>
					<input type="password" id="password" name="password" class="input-dark input-150 required" value="<?php echo $cc_password; ?>">
					<div class="clear" style="height: 10px;"></div>
					<div style="text-align: left;">
						<input type="checkbox" id="remember" name="remember" class="input-dark" <?php if($cc_remember=='true'){echo 'checked';} ?>><span class="text-small">Remember Me</span>
						<button class="btn-green text-small">Login</button><br />
					</div>
				<?php } ?>
			</div><!-- .cc-login-mid -->
			</form><!-- .frmLogin -->
			<div class="clear" style="height: 15px;"></div>
			<!--<div id="cc-login-bottom">
				<p class="text-small">Or sign in using another account:</p>
				<button class="btn-fb"></button>
				<button class="btn-openid"></button>
			</div><!-- .cc-login-bottom -->
		</div><!-- .float-rt -->
	</div><!-- .login-prompt -->
</div><!-- .cc-login -->
</body>
</html>