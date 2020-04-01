<?php
/*
Application: Common Change
Module: User Profile
Filename: cc-profiles.php
Version: 1.0
Description: This module manages user profiles and account activity, including account creation, suspension, and deletion.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 05/30/12 - Version 1 created.
- 06/06/12 - Added USER_REDIRECT function

Class Dependencies:
- MySqlDB Class – Database connectivity class.
- PasswordHash Class - provides password encryption algorithms

Function Definitions:
- ACCT_REG – Register a new user account.- ACCT_SUSPEND – Suspend an account for misuse.- ACCT_DEL – Permanently delete an account.- USER_UPDATE – Update the user profile information.- USER_VALIDATE – Validate user profile data entered/displayed.- LIST_USERS – Generate a list of users based on filter criteria.- GET_PERMS – Check user permissions.- GET_SESSION – Check session variables for needed information.
- GET_PROFILE – Get the user profile and return the information as an array of strings.
- USER_REDIRECT – Redirects the user to a specific page based on login success/failure or special scenario (i.e. License Agreement for first time users, special message, etc.)
- USER_CONFIRM - Confirm the user's email address.

Phase 2:

OpenID Authentication
Facebook Authentication

Support for sending registration & suspension emails, once the email module has been written.
*/

//Maintain session
session_start();

/***************************************************************************************/
/* Includes */
/***************************************************************************************/
include_once 'cc-config.inc.php'; //Application Configuration File
include_once 'class.mysqldb.php';
include_once 'class.sendmail.php';
include_once 'class.PasswordHash.php';
include_once 'cc-authorize.php';
/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/


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
	$cc_vars['id']	 		= strip_tags($the_request['id']);
	$cc_vars['user_id']	 	= strip_tags($the_request['user_id']);
	$cc_vars['username']	= addslashes(strip_tags($the_request['username']));
	$cc_vars['password']	= strip_tags($the_request['password']);
	$cc_vars['fname']	 	= addslashes(strip_tags($the_request['fname']));
	$cc_vars['lname']	 	= addslashes(strip_tags($the_request['lname']));
	$cc_vars['email']	 	= addslashes(strip_tags($the_request['email']));
	$cc_vars['phone']	 	= addslashes(strip_tags($the_request['phone']));
	$cc_vars['address1'] 	= addslashes(strip_tags($the_request['address1']));
	$cc_vars['address2'] 	= addslashes(strip_tags($the_request['address2']));
	$cc_vars['city']	 	= addslashes(strip_tags($the_request['city']));
	$cc_vars['state']	 	= addslashes(strip_tags($the_request['state']));
	$cc_vars['zip']	 		= addslashes(strip_tags($the_request['zip']));
	$cc_vars['country'] 	= addslashes(strip_tags($the_request['country']));
	$cc_vars['current_pass']= addslashes(strip_tags($the_request['current_pass']));
	$cc_vars['new_pass'] 	= addslashes(strip_tags($the_request['new_pass']));
	$cc_vars['conf_pass'] 	= addslashes(strip_tags($the_request['conf_pass']));
	$cc_vars['tos']	 		= strip_tags($the_request['tos']);
	$cc_vars['thumbnail'] 	= addslashes($the_request['thumbnail']); //Don't strip tags, since the directory structure may have some.
	$cc_vars['facebook'] 	= addslashes($the_request['facebook']); //Don't strip tags. Content is URL.
	$cc_vars['share_fb'] 	= strip_tags($the_request['share-fb']);
	$cc_vars['twitter'] 	= addslashes($the_request['twitter']); //Don't strip tags. Content is URL.
	$cc_vars['share_twit'] 	= strip_tags($the_request['share-twit']);
	$cc_vars['share_email'] = strip_tags($the_request['share-email']);
	$cc_vars['filter']	 	= addslashes(strip_tags($the_request['filter']));
	$cc_vars['comments']	= addslashes(strip_tags($the_request['comments']));
	$cc_vars['tos'] 		= strip_tags($the_request['tos']);
	$cc_vars['daily_email'] = strip_tags($the_request['daily_email']);

/* first things first, make sure the user is logged in and has the proper permissions. */
//$user_status = GET_PERMS(); //Reactivate once hooked up to sessions.
if ($user_status == 'session_failed') {
	//Prompt the user with a login message. We may want to create a redirect page later.
	echo "Error! Your session has expired, please login to proceed.";
} else if ($user_status == 'auth_failed') {
	//Prompt the user with a login message. We may want to create a redirect page later.
	echo "Warning! You are not authorized to view this page. If you believe this is in error, please contact us.";	
} else {
	/* If everything is OK, call the specified function */
	switch($action) {
		case 'new_acct': ACCT_REG($cc_vars); break;
		case 'suspend_acct': ACCT_SUSPEND($cc_vars); break;
		case 'del_acct': ACCT_DEL($cc_vars); break;
		case 'update_acct': USER_UPDATE($cc_vars); break;
		case 'list_users': LIST_USERS($cc_vars); break;
		case 'get_profile': GET_PROFILE($cc_vars); break;
	} //end switch
} //end if-else

/***************************************************************************************/
/* Function Name: ACCT_REG */
/* Description: Register a new user account.
/***************************************************************************************/

function ACCT_REG ($cc_vars) {
	
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE, $SETTINGS_TABLE;

	//Assign variables
	$username	= $cc_vars['email']; //$cc_vars['username'];
	$password	= trim($cc_vars['password']);
	$fname		= $cc_vars['fname'];
	$lname		= $cc_vars['lname'];
	$email		= $cc_vars['email'];
	$tos		= $cc_vars['tos'];
	$roleid		= '2';
	$unique_id	= mt_rand(); //Random number for email confirmation

	//Hash the password
	# Try to use stronger but system-specific hashes, with a possible fallback to
	# the weaker portable hashes.
	$t_hasher = new PasswordHash(8, FALSE);
	
	$hash = $t_hasher->HashPassword($password);
	
	//print 'Hash: ' . $hash . "\n";

	unset($t_hasher);

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to make sure a user with the specified email does not already exist
	$sql = "";
	$sql = "SELECT * from `$USER_TABLE` WHERE email='$email'";
	
	//Run the query
	$result = $account->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//If username or email does not exist, carry on
	if ($num_rows < 1){

		//Free the result set memory
		//$account->free_result($result); //Need to look at this.

		//Build a query to insert a new user record.
		$sql = "";
		$sql = "INSERT into `$USER_TABLE`(userid, username, fname, lname, password, email, roleid, email_uniqueid) VALUES('', '$username', '$fname', '$lname', '$hash', '$email', '$roleid', '$unique_id')";
		

		//Run the query
		$result = $account->query($sql);

		if ($result) {
			$err_msg =  "Thank you for registering. You may now log in to Common Change.";
			//Redirect the user to the login screen.
			//USER_REDIRECT('login');

			//Declare new database object with params
			$message = new MySqlDB($db_user, $db_pass, $db_host, $database);
		
			//Open db connection and capture the resource link as $db2
			$db2 = $message->open();

			//Send a confirmation email
			//Build the query.
			$sql = "";
			$sql = "SELECT * from `$SETTINGS_TABLE` WHERE settingsid='1'";
		
			//Run the query
			$result = $message->query($sql);
			
			//Count the rows
			$num_rows = mysql_affected_rows();
		
			if ($num_rows < 1) { 
				//If the user doesn't exist send an error message.
				$err_msg = "Error: the email template was not found.";
			} else {
				//Get the user data.
				$message_info = mysql_fetch_array($result, MYSQL_BOTH);
				//Assign Variables
				$recipient_name 	= $fname . " " . $lname;
				$recipient_email 	= $email;
		
				$mSubject = "Welcome to Common Change";
				$mSender = "CommonChange@commonchange.com";
				$mReplyTo = "donotreply@commonchange.com";
				$mRecipient = $recipient_email;
				$mCC = '';
				$mBCC = '';
				$mBody = $output = sprintf($message_info['email_registration_confirm'], $email, $unique_id);
				$mRedirect = '';
		
				//If everything looks OK, then send the email.
				$mail_sent = new sendmail($mSubject, $mSender, $mReplyTo, $mRecipient, $mCC, $mBCC, $mBody, $mRedirect);
					
				//Test for success.
				if ($mail_sent) {
					$err_msg = "The email message was sent successfully.";
				} else {
					//If the sendmail function failed.
					$err_msg = "Error: We were unable to send your email message. Please contact the system administrator for further assistance.";
				} //end if
			} //end if

			//Close the DB connection
			$message->close($db2);

			//Log the user in automatically
			$cc_vars['username'] = $username;
			$cc_vars['password'] = $password;
			USER_AUTH($cc_vars);

		} else {
			$err_msg =  "Error adding user to the database!";
		} //end if

		//Free the result set memory
		//$account->free_result($result);
	} else {
		//Get the user data
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//$tmp_username = $row['username'];
		$tmp_email = $row['email'];

		if ($tmp_username == $username) {
			$err_msg =  "That username has already been taken, please choose another!";
		} else if ($tmp_email == $email) {
			$err_msg =  "A user has already registered with the email address you specified! Please choose another address.";
		} //end if

		//Free the result set memory
		//$account->free_result($result);
	} //end if


	//Close the DB connection
	$account->close($db);

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: ACCT_SUSPEND */
/* Description: Suspend an account for misuse.
/***************************************************************************************/

function ACCT_SUSPEND ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Assign variables
	$uid		= $cc_vars['user_id'];
	$comments	= $cc_vars['comments'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE userid='$id'";

	//Run the query
	$result = $account->query($sql);

	//Get the results
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if ($row["account_status"] == "suspended") {
		//If the user is already suspended, throw an error.
		$err_msg =  "The user account you specified has already been suspended!";

		//Free the result set memory
		$account->free_result($result);
	} else {
		//Free the result set memory
		$account->free_result($result);

		//Build a queryto update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET account_status='suspended', comments='$comments' WHERE userid='$uid'";
		
		//Run the query
		$result = $account->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: Suspension of the user account failed!";
		} else {
			$err_msg =  "The user account was successfully suspended!";
		} //end if
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: ACCT_DEL */
/* Description: Permanently delete an account.
/***************************************************************************************/

function ACCT_DEL ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Assign variables
	$id	= $cc_vars['user_id'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "DELETE FROM `$USER_TABLE` WHERE userid='$id'";

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Deletion of the user account failed!";
	} else {
		$err_msg =  "The user account was successfully deleted!";
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: USER_UPDATE */
/* Description: Update the user profile information.
/***************************************************************************************/

function USER_UPDATE ($cc_vars) {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	$uid = $cc_vars['user_id'];
	$user_id		= $cc_vars['user_id'];
	$current_pass 	= $cc_vars['current_pass'];
	$new_pass 		= $cc_vars['new_pass'];
	$conf_pass 		= $cc_vars['conf_pass'];

	if ($cc_vars['facebook'] != '') {
		$share_fb = 1;
	} else {
		$share_fb = 0;
	}
	if ($cc_vars['twitter'] != '') {
		$share_twit = 1;
	} else {
		$share_twit = 0;
	}
	if ($cc_vars['share_email'] == 'on') {
		$share_email = 1;
	} else {
		$share_email = 0;
	}
	if ($cc_vars['daily_email'] == 'on') {
		$daily_email = 1;
	} else {
		$daily_email = 0;
	}

	//If the user submitted a new password
	if ($cc_vars['new_pass'] != '') {
		//Validate password data
		$validated = USER_VALIDATE($user_id, $current_pass, $new_pass, $conf_pass);

		if(!$validated) {
			//Throw an error
			$err_msg = "Error: The value you entered for your current password is incorrect!";
			return $err_msg;
		} //end if
	} //end if

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE userid='$uid'";

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		$err_msg = "Error: The user does not exist!";

		//Free the result set memory
		$account->free_result($result);
	} else {
		//Free the result set memory
		$account->free_result($result);

		//Build a query to update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET fname='" . $cc_vars['fname'] . "', lname='" . $cc_vars['lname'] . "', email='" . $cc_vars['email'] . "', address1='" . $cc_vars['address1'] . "', address2='" . $cc_vars['address2'] . "', city='" . $cc_vars['city'] . "', state='" . $cc_vars['state'] . "', zip='" . $cc_vars['zip'] . "', country='" . $cc_vars['country'] . "', phone='" . $cc_vars['phone'] . "', twitter='" . $cc_vars['twitter'] . "', facebook='" . $cc_vars['facebook'] . "', share_fb='" . $share_fb . "', share_twit='" . $share_twit . "', share_email='" . $share_email . "', comments='" . $cc_vars['comments'] . "', daily_email='" . $daily_email . "'";			

		if ($validated) {
			//Hash the password
			$ok = 0;
			
			# Try to use stronger but system-specific hashes, with a possible fallback to
			# the weaker portable hashes.
			$t_hasher = new PasswordHash(8, FALSE);
			$password = $cc_vars['new_pass'];
			$hash = $t_hasher->HashPassword($password);

			$sql = $sql . ", password='" . $hash . "'";

			unset($t_hasher);
		} //end if

		$sql = $sql . " WHERE userid='$uid'";

		//Run the query
		$result = $account->query($sql);
		

		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg = "The user profile did not change.";
		} else {
			//Update session variables
			$_SESSION['user_fullname'] = $cc_vars['fname'] . " " . $cc_vars['lname'];
			$err_msg = "The user profile was successfully updated!";
		} //end if
	} //end if

	//Close the DB connection
	$account->close($db);

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;
	
	//Return any errors;
	return $err_msg;
}


/***************************************************************************************/
/* Function Name: USER_UPDATE_THUMB */
/* Description: Update the user profile image.
/***************************************************************************************/

function USER_UPDATE_THUMB ($cc_vars) {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	$uid = $cc_vars['user_id'];

	$thumb = $thumb_dir . $cc_vars['thumb'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE userid='$uid'";

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		$err_msg = "Error: The user does not exist!";

		//Free the result set memory
		$account->free_result($result);
	} else {
		//Free the result set memory
		$account->free_result($result);

		//Build a query to update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET thumb='" . $thumb . "'";	
		$sql = $sql . " WHERE userid='$uid'";


		//Run the query
		$result = $account->query($sql);
		

		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg = "The user profile did not change.";
		} else {
			$err_msg = "The user profile was successfully updated!";
		} //end if
	} //end if

	//Close the DB connection
	$account->close($db);

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

	//Return any errors;
	return $err_msg;
}


/***************************************************************************************/
/* Function Name: USER_VALIDATE */
/* Description: Validate user profile data entered/displayed.
/***************************************************************************************/

function USER_VALIDATE ($user_id, $current_pass, $new_pass, $conf_pass) {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Assign variables
	$uid		= $user_id;
	$password	= $current_pass;

	//Declare new database object with params
	$checkpass = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $checkpass->open();


	//Build a query to make sure neither the username and email does not already exist
	$sql = "";
	$sql = "SELECT * from `$USER_TABLE` WHERE userid='" . $uid . "'";

	//Run the query
	$result = $checkpass->query($sql);
	
	//Count the rows
	$num_rows = $checkpass->count_rows($result); //mysql_affected_rows();
	
	if ($num_rows < 1) {
		$err_msg = "Error: could not retrieve the user information.";

	} else {
		//Get the user data
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//get the password hash
		$hash = trim($row['password']);

		# Compare the password entered with the hash
		$t_hasher = new PasswordHash(8, FALSE); 

		$check = $t_hasher->CheckPassword($password, $hash);
		if ($check) {
			//Validate the password
			if ($new_pass != '') {
				if ($new_pass == $conf_pass) {
					$validated = true;
				} else {
					$validated = false;
					$err_msg = "Error: The passwords you entered do not match.";
				} //end if
			} //end if
		} else {
			//Return an error
			$err_msg = "Error: The value you entered for your current password is incorrect.";
		} //end if
	} //end if

	//Close the DB connection
	$checkpass->close($db);

	//Return errors
	$_SESSION['err_msg'] = $err_msg;

	//Return results
	return $validated;
}


/***************************************************************************************/
/* Function Name: LIST_USERS */
/* Description: Generate a list of users based on filter criteria.
/***************************************************************************************/

function LIST_USERS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	//Assign variables
	$id	= $cc_vars['user_id'];
	$filter	= $cc_vars['filter'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE userid='$id'";
	if ($filter != '') {
		$sql = $sql . " order by " . $cc_vars['filter'];
	}

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: User not found!";
	} else {
		$err_msg =  "success";

		//Print the results
		$iCount = 1;
		echo "<div id='users' style='margin-bottom:10px;float:left;'>";
		echo "<table id='tblListUsers'>";
		echo "<thead>";
		echo "<tr>";
			echo "<td>Username</td>";
			echo "<td>First Name</td>";
			echo "<td>Last Name</td>";
			echo "<td>Email</td>";
			echo "<td>Phone</td>";
			echo "<td>Address1</td>";
			echo "<td>Address2</td>";
			echo "<td>City</td>";
			echo "<td>State</td>";
			echo "<td>Zip</td>";
			echo "<td>Country</td>";
			echo "<td>Facebook</td>";
			echo "<td>Twitter</td>";
			echo "<td>Status</td>";
			echo "<td>Comments</td>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";

	  	while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			echo "<tr>";
				echo "<td>" . $row['username'] . "</td>";
				echo "<td>" . $row['fname'] . "</td>";
				echo "<td>" . $row['lname'] . "</td>";
				echo "<td>" . $row['email'] . "</td>";
				echo "<td>" . $row['phone'] . "</td>";
				echo "<td>" . $row['address1'] . "</td>";
				echo "<td>" . $row['address2'] . "</td>";
				echo "<td>" . $row['city'] . "</td>";
				echo "<td>" . $row['state'] . "</td>";
				echo "<td>" . $row['zip'] . "</td>";
				echo "<td>" . $row['country'] . "</td>";
				echo "<td>" . $row['facebook'] . "</td>";
				echo "<td>" . $row['twitter'] . "</td>";
				echo "<td>" . $row['account_status'] . "</td>";
				echo "<td>" . $row['comments'] . "</td>";
			echo "</tr>";
			$iCount++;
		} //end while	
		echo "</tbody>";
		echo "</table><!-- .tblListUsers -->";
		echo "</div>";
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: GET_PERMS */
/* Description: Check user permissions.
/***************************************************************************************/

function GET_PERMS () {

	$err_msg = '';

	//Check to make sure the session is active and the user is logged in
	$a = session_id();
	if (!isset($a)) {
		$err_msg = 'session_failed';
	}

	//Check to make sure the user has the proper permissions.
	$access_level	= $_SESSION['access_level'];
	if ($auth_level < 1) {
		$err_msg = 'auth_failed';
	}

	//Return any errors;
	return $err_msg;		

}


/***************************************************************************************/
/* Function Name: GET_SESSION */
/* Description: Check session variables for needed information.
/***************************************************************************************/

function GET_SESSION () {
	//We do not currently need any session variables that we have not collected elsewhere.
	//This function may be superfluous.
}




/***************************************************************************************/
/* Function Name: GET_PROFILE */
/* Description: Get the user profile information from the DB.
/***************************************************************************************/

function GET_PROFILE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;
	global $user_profile;

	//Assign variables
	$uid	= $cc_vars['user_id'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE userid='$uid'";

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: User not found!";
	} else {
		$err_msg =  "success";

		//Store the results in an array
	  	$user_profile = mysql_fetch_array($result, MYSQL_BOTH);
	} //end if

	//Close the DB connection
	$account->close($db);

	if ($num_rows < 1){
		return $err_msg;
	} else {
		//Return the array
		return $user_profile;
	}
}


/***************************************************************************************/
/* Function Name: USER_REDIRECT */
/* Description: */
/***************************************************************************************/

function USER_REDIRECT2($type) {
	
	//On successful login, redirect to default page.
	if ($type == 'login') {
		echo "<script type='text/javascript'>window.location.href='" . $_SESSION['home_url'] . "login.php'; </script>";
	} //end if
}



/***************************************************************************************/
/* Function Name: USER_CONFIRM */
/* Description: Update the user profile information.
/***************************************************************************************/

function USER_CONFIRM ($cc_vars) {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $PERMS_TABLE;

	$email 		= $cc_vars['confirm'];
	$uniqueid	= $cc_vars['uniqueid'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();

	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE email='$email' AND email_uniqueid='$uniqueid'";

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		$err_msg = "Error: The user does not exist!";

		//Free the result set memory
		$account->free_result($result);
	} else {
		//Free the result set memory
		$account->free_result($result);

		//Build a query to update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET email_confirmed='yes' WHERE email='$email'";			

		//Run the query
		$result = $account->query($sql);		

		//Count the rows
		$num_rows = mysql_affected_rows();
			
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg = "Your email could not be confirmed. Please contact a system administrator for assistance.";
		} else {
			//Update messages
			$err_msg = "Your email address has been confirmed!";
		} //end if
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	return $err_msg;
}


?>