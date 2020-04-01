<?php
/*
Application: Common Change
Module: User Authentication
Filename: cc-authorize.php
Version: 1.0
Description: This module manages user profiles and account activity, including account creation, suspension, and deletion.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 06/01/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.
- PasswordHash Class - provides password encryption algorithms

Function Definitions:- USER_AUTH – Authenticate a user. This can be done with useris/password combination or other approved authentication protocols, such as Facebook authentication and Open ID.- USER_VALID – If a user properly authenticates, this function sets permissions and session variables.- USER_INVALID – If a user does not authenticate, errors are displayed and session variables (ipaddress, failedattempts) are set which track failed login attempts. Option to suspend an account after repeated failed attempts.- USER_FORGOT_ID – If a user forgets the login id, this function collects information and displays id (or email) on validation.- USER_FORGOT_PASS – If a user forgets the login id, this function collects information and allows a password reset on validation.- USER_REDIRECT – Redirects the user to a specific page based on login success/failure or special scenario (i.e. License Agreement for first time users, special message, etc.)

- GET_PERMS – Found unnecessary for this module, since we set perms here.- GET_SESSION – Found unnecessary at present, since functions are handled elsewhere.
*/


/***************************************************************************************/
/* Includes */
/***************************************************************************************/
include_once 'cc-config.inc.php'; //Application Configuration File
include_once 'class.mysqldb.php';
include_once 'class.sendmail.php';
include_once 'class.PasswordHash.php';

/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/

/* Constants */
$USER_TABLE		= 'CC_USERS';
$PERMS_TABLE	= 'CC_ROLES';


/* Variables passed from GET/POST */

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET': $the_request = &$_GET; break;
		case 'POST': $the_request = &$_POST; break;
		default: $the_request = &$_POST; break;
	}
	
	//Get the page parameters and strip tags where necessary, then reassign to the variable array.
	$action			 		= strip_tags($the_request['action']); //update_acct, del_acct, new_acct, sus_acct.
	$cc_vars['username']	= addslashes(strip_tags($the_request['username']));
	$cc_vars['email']	 	= addslashes(strip_tags($the_request['email']));
	$cc_vars['password'] 	= addslashes(strip_tags($the_request['password']));
	$cc_vars['remember'] 	= strip_tags($the_request['remember']);
	$cc_vars['tos'] 		= strip_tags($the_request['tos']);
	

	//Call the specified function.
	//No need to check permissions here, since a user doesn't need to be logged in to use the module.
	switch($action) {
		case 'login': USER_AUTH($cc_vars); break;
		case 'forgot_id': USER_FORGOT_ID($cc_vars); break;
		case 'reset_pass': USER_FORGOT_PASS($cc_vars); break;
	} //end switch


/***************************************************************************************/
/* Function Name: USER_AUTH */
/* Description: Authenticate a user. Not for users logging in with OpenID or Facebook */
/***************************************************************************************/

function USER_AUTH($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Start the session
	session_start();

	//Assign variables
	$username	= $cc_vars['username'];
	$password	= $cc_vars['password'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to make sure neither the username and email does not already exist
	$sql = "";
	$sql = "SELECT * from `$USER_TABLE` WHERE username='" . $username . "'";

	//Run the query
	$result = $account->query($sql);
	
	//Count the rows
	$num_rows = $account->count_rows($result); //mysql_affected_rows();
	
	if ($num_rows < 1) {
		$err_msg = "Error: we did not find an account with the username you entered!";
	} else {
		//Get the user data
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//get the password hash
		$hash = trim($row['password']);

		# Compare the password entered with the hash
		$t_hasher = new PasswordHash(8, FALSE); 

		$check = $t_hasher->CheckPassword($password, $hash);
		if ($check) {
			//print "Check correct: '" . $check . "' (should be '1')\n";
			//$err_msg = "Password correct!";


			//Assign some more variables, since the user is valid
			$cc_vars['user_id'] = $row['userid'];
			$cc_vars['email'] = $row['email'];
			$cc_vars['lname'] = $row['lname'];
			$cc_vars['fname'] = $row['fname'];
			$cc_vars['roleid'] = $row['roleid'];
			$cc_vars['account_status'] = $row['account_status'];

			//Validate the user
			$err_msg = USER_VALID($cc_vars);
			USER_REDIRECT('valid');

		} else if (!$check) {
			//print "Check wrong: '" . $check . "' (should be '0' or '')\n";
			$err_msg = "Password incorrect!";

			//Invalidate the user
			$err_msg = USER_INVALID($cc_vars);
		} //end if

		unset($t_hasher);
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;
}


/***************************************************************************************/
/* Function Name: USER_VALID */
/* Description: */
/***************************************************************************************/

function USER_VALID($cc_vars) {
	
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;


	//Since the user has been validated, set the session variables.
	//For testing, sessions are currently started in functions.php, start_session().
	$_SESSION['user_id'] = $cc_vars['user_id'];
	$_SESSION['username'] = $cc_vars['username'];
	$_SESSION['email'] = $cc_vars['email'];
	$_SESSION['lname'] = $cc_vars['lname'];
	$_SESSION['fname'] = $cc_vars['fname'];
	$_SESSION['account_status'] = $cc_vars['account_status'];


	/* Determine and set the user's authorization level. */

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();

	//Build a query to check the user's authorization level.
	$sql = "";
	$sql = "SELECT * from `CC_ROLES` WHERE roleid='" . $cc_vars['roleid'] . "'";


	//Run the query
	$result = $account->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1) {
		$err_msg = "Error: unable to set permissions! Please contact the system administrator for assistance.";
	} else {
		//Get the user data
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Assign the session variable
		$_SESSION['access_level'] = $row['access_level']; //For testing, the $_SESSION['auth'] variable is used.
	} //end if
	
	//If the user clicked the 'remember me' checkbox, set cookies.
	if ($cc_vars['remember']) {
		setcookie ("cc_username", $cc_vars['username'], time()+3600*24*30); //, "/", "commonchange.com");
		setcookie ("cc_password", $cc_vars['password'], time()+3600*24*30); //, "/", "commonchange.com");
		setcookie ("cc_remember", "true", time()+3600*24*30); //, "/", "commonchange.com");
	} else {
		//If the box is not checked, destroy the cookies.
		setcookie ("cc_username", "", time()+3600*24*30); //, "/", "commonchange.com");
		setcookie ("cc_password", "", time()+3600*24*30); //, "/", "commonchange.com");
		setcookie ("cc_remember", "", time()+3600*24*30); //, "/", "commonchange.com");
	}//end if

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;
}


/***************************************************************************************/
/* Function Name: USER_INVALID */
/* Description: */
/***************************************************************************************/

function USER_INVALID($cc_vars) {

	global $err_msg;

	/* Since the user was not validated */

	//update the failed attempts flag.
	$_SESSION['failedattempts'] = $_SESSION['failedattempts']++;

	//Log the ipaddress in case we need to ban someone for abuse.
	$SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];

	//Place code to suspend the account or ban the user here if needed.

	//Return any messages.
	return $err_msg;
}


/***************************************************************************************/
/* Function Name: USER_FORGOT_ID */
/* Description: */
/***************************************************************************************/

function USER_FORGOT_ID($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Assign variables.
	$email = $cc_vars['email'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();

	//Check to see if there is a user with the email provided.
	//Build the query.
	$sql = "";
	$sql = "SELECT * from `CC_USERS` WHERE email='$email'";
	
	//Run the query
	$result = $account->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1) {
		//If the email is not registered, send an error message.
		$err_msg = "Error: the email address you entered was not found.";
	} else {
		//If the email address was found, send the username and prompt the user to check thier email.

		//Get the user data
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Get the username associated with the email address.
		$username = $row['username'];

		//Send the username to the email address on record.
		$mSubject = 'Common Change Username Reminder';
		$mSender = 'donotreply@commonchange.com';
		$mRecipient = $email;
		$mCC = '';
		$mBCC = '';
		$mBody = 'Your Common Change username is $username'; //The message body will pull from the administrative email templates.
		$mRedirect = '';

		$mail_sent = new sendmail($mSubject, $mSender, $mRecipient, $mCC, $mBCC, $mBody, $mRedirect);
		echo $mail_sent; //Debug.

		//Test for success.
		if ($mail_sent) {
			$err_msg = "Your username reminder has been sent to your email address. Please check your email before attempting to log in again.";
		} else {
			//If the sendmail function failed.
			$err_msg = "Error: We were unable to send a reminder to your email address. Please contact the system administrator for further assistance.";
		} //end if
	} //end if
	
	//Close the DB connection
	$account->close($db);

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;

	//If the user exists, send a reminder email to the email address. 
	//If not, send an error message.

}

/***************************************************************************************/
/* Function Name: USER_FORGOT_PASS */
/* Description: */
/***************************************************************************************/

function USER_FORGOT_PASS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Assign variables.
	$username = $cc_vars['username'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();

	//Check to see if there is a user with the username provided.
	//Build the query.
	$sql = "";
	$sql = "SELECT * from `CC_USERS` WHERE username='$username'";

	//Run the query
	$result = $account->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1) { 
		//If the user doesn't exist send an error message.
		$err_msg = "Error: the username you entered was not found.";
	} else {
		//Get the user data.
		$row = mysql_fetch_array($result, MYSQL_BOTH);
		$userid = $row['userid'];
		$email = $row['email'];

		/* Generate the temp password */
		//Limit the character set, excluding lower case L and 1 to avoid confusion.
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";

		//Generate the random seed.
	    srand((double)microtime()*1000000);

	    $i = 0;
	    $temp_pass = '' ;

		//Make the password 7 randomly generated alphanumeric characters.
	    while ($i <= 7) {
	        $num = rand() % 33;
	        $tmp = substr($chars, $num, 1);
	        $temp_pass = $temp_pass . $tmp;
	        $i++;
	    }

		//Hash the password
		# Try to use stronger but system-specific hashes, with a possible fallback to
		# the weaker portable hashes.
		$t_hasher = new PasswordHash(8, FALSE);
		
		$hash = $t_hasher->HashPassword($temp_pass);
	
		unset($t_hasher);
		
		/* Store it as the new password in the database. */
		//Build the query.
		$sql = "";
		$sql = $sql = "UPDATE `$USER_TABLE` SET password='" . $hash . "' WHERE userid='" . $userid . "'";
		
		//Run the query
		$result = $account->query($sql);
		
		//Count the rows
		$num_rows = mysql_affected_rows();

		//Check to make sure the password was updated.
		if ($num_rows < 1) {
			//If there was a problem, return an error.
			$err_msg = "Error: we could not create your temporary password.";
		} else {
			//If everything looks OK, then send the email.

			/* Send the temp password to the email address on record. */
			$mSubject = 'Common Change Password Reset';
			$mSender = "common-change@commonchange.com";
			$mReplyTo = "donotreply@commonchange.com";
			$mRecipient = $email;
			$mCC = '';
			$mBCC = '';
			$mBody = "We temporarily reset the password for your account to " . $temp_pass . ". Please log in at www.commonchange.com and change your password at your earliest convenience."; //The message body will pull from the administrative email templates.
			$mRedirect = '';
	
			$mail_sent = new sendmail($mSubject, $mSender, $mReplyTo, $mRecipient, $mCC, $mBCC, $mBody, $mRedirect);
				
			//Test for success.
			if ($mail_sent) {
				$err_msg = "A temporary password has been sent to your email address. Please check your email before attempting to log in again.";
			} else {
				//If the sendmail function failed.
				$err_msg = "Error: We were unable to send your temp password to your email address. Please contact the system administrator for further assistance.";
			} //end if
		} //end if
	} //end if
	
	//Close the DB connection
	$account->close($db);

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: USER_REDIRECT */
/* Description: */
/***************************************************************************************/

function USER_REDIRECT($type) {
	global $home_url;

	$_SESSION['home_url'] = $home_url;
	$url = $_SESSION['home_url'];

	//On successful login, redirect to default page.
	if ($type == 'valid') {
		header("Location: $url");
        exit();
	} //end if
}


?>
