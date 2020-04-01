<?php
	//Maintain session
	session_start();

	//Include cc-profiles module
	include 'modules/cc-profiles.php';

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET': $the_request = &$_GET; break;
		case 'POST': $the_request = &$_POST; break;
		default: $the_request = &$_POST; break;
	}

	//General variables
	$cc_vars['email']	 		= strip_tags($the_request['email']);
	$cc_vars['unique_id']	 	= strip_tags($the_request['r']);
	$cc_vars['fname']	 		= strip_tags($the_request['fname']);
	$cc_vars['lname']	 		= strip_tags($the_request['lname']);

	//Set a session variable to hold the unique key for first login.
	$_SESSION['unique_id'] = $cc_vars['unique_id'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
	<title><?php echo $_SESSION['app_title']; ?></title>

<link href="defaultTheme.css" rel="stylesheet" media="screen" />
<link href="js/css/jquery-ui-1.8.20.custom.css" rel="stylesheet" media="screen" />
<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
<link href="js/css/jquery_notification.css" type="text/css" rel="stylesheet"/>
<link href="js/css/colorbox.css" type="text/css" rel="stylesheet"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script src="http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"></script>

<script type="text/javascript" src="js/jquery.fixedheadertable.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="js/cc-functions.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script type="text/javascript" src="js/jquery_notification_v.1.js"></script>
<script type="text/javascript" src="js/jquery.validate.js"></script>
<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>

<script type="text/javascript">

$(document).ready(function() {
    // Validate registration form
    $("#frmRegister").validate({
   		messages: {
     		"fname": {required: "Required"},
			"lname": {required: "Required"},
     		"email": {
       			required: "Required",
       			email: "Please use the format name@domain.com."
     		},
			"password": "Passwords must be at least 6 characters.",
			"tos": "You must agree to the Terms of Service in order to register."
   		}
	});

	//Modal Invitation Form
	$("a[rel]").colorbox({href:"tos.html", width: "900px", height: "500px", close: "Close X"});
});


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

<div id="cc-register">
	<div id="register-prompt">
		<div style="width: 480px; margin: 15px auto;">
			<div id="cc-register-mid">
				<div class="clear" style="height: 10px;"></div>
				<form method="post" action="register.php" id="frmRegister"> 
					<input type="hidden" name="tab" value="register">
					<input type="hidden" name="action" value="new_acct">
					<input type="hidden" name="unique_id" value="<?php echo $cc_vars['unique_id']; ?>">
					<div style="margin: 0 auto; width: 300px;">
						<h3>Register for a New Common Change Account</h3>
						<!--<label for="username" class="label text-med" style="width: 75px;">Username:</label>
						<input id="username" name="username" class="input-dark input-150 required">-->

						<div class="clear" style="height: 6px;"></div>
						<label for="fname" class="label text-med" style="width: 75px;">First Name:</label>
						<input id="fname" name="fname" class="input-dark input-150 required" minlength="2" value="<?php echo $cc_vars['fname']; ?>">

						<div class="clear" style="height: 6px;"></div>
						<label for="lname" class="label text-med" style="width: 75px;">Last Name:</label>
						<input id="lname" name="lname" class="input-dark input-150 required" minlength="2" value="<?php echo $cc_vars['lname']; ?>">
	
						<div class="clear" style="height: 6px;"></div>
						<label for="email" class="label text-med" style="width: 75px;">Email:</label>
						<input id="email" name="email" class="input-dark input-150 required email" value="<?php echo $cc_vars['email']; ?>">
	
						<div class="clear" style="height: 6px;"></div>
						<label for="password" class="label text-med" style="width: 75px;">Password:</label>
						<input type="password" id="password" name="password" class="input-dark input-150 required" minlength="6">
							
						<div class="clear" style="height: 10px;"></div>
						<div style="text-align: left; margin-left: 72px;">
							<input type="checkbox" id="tos" name="tos" class="input-dark required"><span class="text-small">Yes, I agree to the <a href="tos.html" rel="#overlay" class="text-small">Terms of Use</a></span><br />
							<button type="submit" class="btn-green text-small">Register</button><br />
						</div>
					</div><!-- .float-lt -->
				</form><!-- .frmRegister -->
				<!--<div class="float-rt">
					<p class="text-small">Register with another <br />account:</p>
					<button class="btn-fb"></button><br /><br />
					<button class="btn-openid"></button>
				</div><!-- .float-rt -->
			</div><!-- .cc-register-mid -->
	
			<div class="clear" style="height: 15px;"></div>
			<div id="cc-register-bottom" style="text-align: center;">
				<span class="text-small">Already have an account? <a href="login.php" class="text-small" style="color: #666; text-decoration: underline; font-weight: normal;">Sign in now!</a></span>
			</div><!-- .cc-register-bottom -->

		</div><!-- .float-rt -->
	</div><!-- .register-prompt -->
</div><!-- .cc-register -->

</body>
</html>