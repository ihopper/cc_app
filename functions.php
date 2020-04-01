<?php
//Maintain session
session_start();

include_once 'modules/cc-config.inc.php';
include_once 'modules/cc-profiles.php';


//Check to see if the functions.php file has been called with JQuery.ajax()
$action			= strip_tags($_POST['action']);
$id				= strip_tags($_POST['id']);


if ($action == "star") {
	star($id);
} else {
	//do nothing
}


//Initialize application settings, if needed
function cc_init() {

	$a = $_SESSION['cc_init'];

	if ($a != 'true') { //Check if the init has already run. If not, proceed.

		$cc_vars['id'] = $_SESSION['user_id'];
		$cc_vars['user_id'] = $_SESSION['user_id'];

		//Get the user info
		$user_profile = GET_PROFILE($cc_vars);

		//Load settings from the CC_SETTINGS table into session variables
		$_SESSION['app_title'] = 'Common Change v1.0 Beta';
		$_SESSION['user_fname'] = $user_profile['fname'];
		$_SESSION['user_lname'] = $user_profile['lname'];
		$_SESSION['user_fullname'] = $user_profile['fname'] . " " . $user_profile['lname'];
		$_SESSION['home_url']	=	'http://www.isaachopper.com/dev/cc/';
		$_SESSION['auth'] = '10'; //Auth level < 1 will fail all checks.

		$_SESSION['group_id'] = $user_profile['groupid'];
		//$_SESSION['group_admin'] = True;

		//Set switch to indicate init has run.		
		$_SESSION['cc_init'] = 'true'; 
	} 

}

//Flag an item as important
function star($id) {
	//echo "Debug: " . $id;
	echo "Response Good!";
}

//Destroy the current session, logging the user out of the system.
function logout() {
	global $home_url;

	session_destroy();
	echo "<script type='text/javascript'>window.location.href='" . $home_url . "login.php'; </script>";
}

?>