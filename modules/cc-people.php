<?php
/*
Application: Common Change
Module: People
Filename: cc-people.php
Version: 1.0
Description: This module allows users to view, search, and invite people to CC and/or groups. Also allows for the creation & management of recipients.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 07/02/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
- GET_USERS – Display users listed in the system with accompanying group / voting information.- PEOPLE_SEARCH – Fetch and display a selection of people based on filters (i.e. name, state, etc.).- PEOPLE_INVITE – Allows a user to invite a person to a group. Email invitations will be grouped with the email module.- GET_DETAIL – Displays details of a person, such as picture, location, voting history, current groups, threads, etc.

- GET_RECIPIENTS - Display a list recipients in the system
- SHOW_RECIPIENT - Displays information about a specified recipient.
- RECIPIENT_ADD - Add a new recipient to the database.
- RECIPIENT_DEL - Delete a recipient from the database.
- RECIPIENT_UPDATE - Update a recipient's information in the database.
- RECIPIENT_SUSPEND - Suspend a recipient account.

*/


/***************************************************************************************/
/* Includes */
/***************************************************************************************/

include_once 'cc-config.inc.php'; //Application Configuration File
include_once 'class.mysqldb.php';



/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/

/* Constants */



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
	$cc_vars['group_id']	= strip_tags($the_request['group_id']);
	$cc_vars['member_id']	= strip_tags($the_request['member_id']);
	$cc_vars['role']		= addslashes(strip_tags($the_request['role']));

	//Variables for recipients
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
	$cc_vars['new_pass'] 	= strip_tags($the_request['new_pass']);
	$cc_vars['conf_pass'] 	= strip_tags($the_request['conf_pass']);
	$cc_vars['tos']	 		= strip_tags($the_request['tos']);
	$cc_vars['thumbnail'] 	= addslashes($the_request['thumbnail']); //Don't strip tags, since the directory structure may have some.
	$cc_vars['facebook'] 	= addslashes($the_request['facebook']); //Don't strip tags. Content is URL.
	$cc_vars['share_fb'] 	= strip_tags($the_request['share-fb']);
	$cc_vars['twitter'] 	= addslashes($the_request['twitter']); //Don't strip tags. Content is URL.
	$cc_vars['share_twit'] 	= strip_tags($the_request['share-twit']);
	$cc_vars['filter']	 	= addslashes(strip_tags($the_request['filter']));
	$cc_vars['comments']	= addslashes(strip_tags($the_request['comments']));


	//Get session variables
	//$cc_vars['group_id']	= $_SESSION['group_id'];
	

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
		case 'add_recipient': RECIPIENT_ADD($cc_vars); break;
		case 'update_recipient': RECIPIENT_UPDATE($cc_vars); break;
	} //end switch
} //end if-else




/***************************************************************************************/
/* Function Name: GET_USERS */
/* Description: Display users listed in the system with accompanying group / voting information. */
/***************************************************************************************/

function GET_USERS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;
	global $user_info, $users;

	//Assign variables
	$sort	= $cc_vars['sort'];
	$search	= $cc_vars['search'];

	//Declare new database object with params
	$getusers = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getusers->open();

	//Build a query to list users, with sort option. The left join looks for matching user data in the groups table.
	//If the user does not belong to a group the user information is still produced, minus group info.
	$sql = "";

	if ($sort == 'group_members') {
		$sql = "SELECT `$USER_TABLE`.*, `$GROUP_TABLE`.groupid, `$GROUP_TABLE`.name FROM `$USER_TABLE`";
		$sql = $sql . " LEFT JOIN `$GROUP_TABLE`";
		$sql = $sql . " ON $USER_TABLE.groupid=$GROUP_TABLE.groupid";
		$sql = $sql . " WHERE $USER_TABLE.groupid='" . $_SESSION['group_id'] . "'";
	} else {
		$sql = "SELECT `$USER_TABLE`.*, `$GROUP_TABLE`.groupid, `$GROUP_TABLE`.name FROM `$USER_TABLE`";
		$sql = $sql . " LEFT JOIN `$GROUP_TABLE`";
		$sql = $sql . " ON $USER_TABLE.groupid=$GROUP_TABLE.groupid";
		if($sort != ''){
			$sql = $sql . " ORDER BY `$USER_TABLE`.$sort"; //Sort order
		} //end if
	} //end if

	//Run the query
	$result = $getusers->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No users found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($user_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			//Filter if there are search criteria.
			if($search != ''){
				//Search for the string
				$found = stristr($user_info['name'] . $user_info['email'] . $user_info['lname'] . $user_info['fname'] . $user_info['city'] . $user_info['state'] . $user_info['country'], $search);
				if($found !== false){
					$users[$iCount] = $user_info;
				} //end if
			} else {
				$users[$iCount] = $user_info;
				//$iCount++;
			} //end if
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getusers->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $users;
	} //end if


}


/***************************************************************************************/
/* Function Name: PEOPLE_SEARCH */
/* Description: Fetch and display a selection of people based on filters (i.e. name, state, etc.). */
/***************************************************************************************/

function PEOPLE_SEARCH ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $RECIPIENT_TABLE;
	global $people_info, $people;

	//Assign variables
	$sort	= $cc_vars['sort'];
	$ftype 	= $cc_vars['ftype']; //Filter type, options: 'lname', 'fname', 'country', 'state', 'zip'
	$filter	= $cc_vars['filter'];


	//Declare new database object with params
	$getpeople = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getpeople->open();

	//Build a query search for users and recipients, given filter criteria.
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE $ftype='" . $filter . "'";

	//Run the query
	$result = $getpeople->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No users found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($user_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$people[$iCount] = $people_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getpeople->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $people;
	} //end if


}


/***************************************************************************************/
/* Function Name: PEOPLE_INVITE */
/* Description: Allows a user to invite a person to a group. */
/***************************************************************************************/

function PEOPLE_INVITE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $INVITE_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$oid	= $cc_vars['owner_id'];

	$status		= 'active';
	$created	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$invite = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $invite->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$INVITE_TABLE` (`invitationid`, `userid`, `ownerid`, `groupid`, `created`, `email`, `response`) VALUES (NULL, '" . $uid . "', '" . $oid . "', '" . $gid . "', '" . $created . "', '" . $cc_vars['email'] . "', '" . $cc_vars['response'] . "');";

	//Run the query
	$result = $invite->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$invite->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to invite the user to the specified group.";

		//Return errors
		return $err_msg;
	} else {
		$err_msg =  "The user has been successfully issued a group invitation.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;

		//Redirect the user
		echo "<script type='text/javascript'>window.location.href='" . $_SESSION['home_url'] . "?tab=mygroup'; </script>";
	} //end if

}


/***************************************************************************************/
/* Function Name: GET_DETAIL */
/* Description: Displays details of a person, such as picture, location, voting history, */
/* current groups, threads, etc. */
/***************************************************************************************/

function GET_DETAIL ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $RECIPIENT_TABLE, $GROUP_TABLE, $VOTE_TABLE;
	global $user_info;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['recipient_id'];


	//Declare new database object with params
	$getdetail = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdetail->open();


	//Build a query to pull group information
	$sql = "";

	if (isset($uid)) {
		$sql = "SELECT * FROM `$USER_TABLE` WHERE userid='$uid'";
	} else if (isset($rid)) {
		$sql = "SELECT * FROM `$RECIPIENT_TABLE` WHERE recipientid='$rid'";
	} //end if

	//Run the query
	$result = $getdetail->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: User not found!";
	} else {
		$err_msg =  "success";

		//Store the results in an array
	  	$user_info = mysql_fetch_array($result, MYSQL_BOTH);

		//Free the result set
		mysql_free_result($result);

		//Build a new query to get voting history
		$sql = '';
		$sql = "SELECT * FROM `$VOTE_TABLE` WHERE userid='$uid'";

		//Run the query
		$result = $getdetail->query($sql);

			//Count the rows
			$num_votes = mysql_affected_rows();

			//Store the voting history in our array
			$user_info['votes'] = $num_votes;

		//Free the result set
		mysql_free_result($result);

		//Build a new query to get the group name
		$sql = '';
		$sql = "SELECT * FROM `$GROUP_TABLE` WHERE userid='$uid'";

		//Run the query
		$result = $getdetail->query($sql);

		//Count the rows
		$num_groups = mysql_affected_rows();

		//Store the group name in our array
		if ($num_groups < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "User not in a group!";
		} else {
			$group_info = mysql_fetch_array($result, MYSQL_BOTH);
			$user_info['group_name'] = $group_info['name'];
		} //end if
	} //end if

	//Close the DB connection
	$getdetail->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $user_info;
	} //end if


}


/***************************************************************************************/
/* Function Name: GET_RECIPIENTS */
/* Description: Display a list recipients in the system. */
/***************************************************************************************/

function GET_RECIPIENTS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $RECIPIENT_TABLE, $USER_TABLE;
	global $recipient_info, $recipients;

	//Assign variables
	$gid	= $cc_vars['group_id'];


	//Declare new database object with params
	$getrecipients = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrecipients->open();

	//Build a query to list recipients, with sort option.
	$sql = "";
	$sql = "SELECT * FROM `$RECIPIENT_TABLE`";
	$sql = $sql . " WHERE groupid='$gid'"; //Sort order

	//Run the query
	$result = $getrecipients->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No users found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($recipient_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$recipients[$iCount] = $recipient_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getrecipients->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $recipients;
	} //end if


}


/***************************************************************************************/
/* Function Name: SHOW_RECIPIENT */
/* Description: Display a list recipients in the system. */
/***************************************************************************************/

function SHOW_RECIPIENT ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $RECIPIENT_TABLE;
	global $recipient_info;

	//Assign variables
	$rid	= $cc_vars['recipient_id'];


	//Declare new database object with params
	$getrecipient = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrecipient->open();

	//Build a query to list recipients.
	$sql = "";
	$sql = "SELECT * FROM `$RECIPIENT_TABLE`";
	$sql = $sql . " WHERE recipientid='$rid'";

	//Run the query
	$result = $getrecipient->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No users found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$recipient_info = mysql_fetch_array($result, MYSQL_BOTH);
	} //end if

	//Close the DB connection
	$getrecipient->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $recipient_info;
	} //end if


}



/***************************************************************************************/
/* Function Name: RECIPIENT_ADD */
/* Description: Add a new recipient to the database. */
/***************************************************************************************/

function RECIPIENT_ADD ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $RECIPIENT_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];

	$status		= 'active';
	$created	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$recipient = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $recipient->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$RECIPIENT_TABLE` (`recipientid`, `ownerid`, `groupid`, `status`, `lname`, `fname`, `email`, `address1`, `address2`, `city`, `state`, `zip`, `created`, `comments`, `facebook`, `twitter`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '" . $cc_vars['status'] . "', '" . $cc_vars['lname'] . "', '" . $cc_vars['fname'] . "', '" . $cc_vars['email'] . "', '" . $cc_vars['address1'] . "', '" . $cc_vars['address2'] . "', '" . $cc_vars['city'] . "', '" . $cc_vars['state'] . "', '" . $cc_vars['zip'] . "', '" . $created . "', '" . $cc_vars['comments'] . "', '" . $cc_vars['facebook'] . "', '" . $cc_vars['twitter'] . "');";

	//Run the query
	$result = $recipient->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$recipient->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to add the recipient.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The recipient has been successfully added.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if

}


/***************************************************************************************/
/* Function Name: RECIPIENT_DEL */
/* Description: Delete a recipient from the database. */
/***************************************************************************************/

function RECIPIENT_DEL ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $RECIPIENT_TABLE, $PERMS_TABLE;

	//Assign variables
	$id	= $cc_vars['id'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "DELETE FROM `$RECIPIENT_TABLE` WHERE recipientid='$id'";

	//Run the query
	$result = $account->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Deletion of the recipient account failed!";
	} else {
		$err_msg =  "The recipient account was successfully deleted!";
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	return $err_msg;

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

}


/***************************************************************************************/
/* Function Name: RECIPIENT_UPDATE */
/* Description: Update a recipient's information in the database. */
/***************************************************************************************/

function RECIPIENT_UPDATE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $RECIPIENT_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];

	$status		= $cc_vars['status'];
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$recipient = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $recipient->open();

	//Build a query to update the recipient specified.
	$sql = "";
	$sql = "UPDATE `$RECIPIENT_TABLE` SET status=$status, lname='" . $cc_vars['lname'] . "', fname='" . $cc_vars['fname'] . "', email='" . $cc_vars['email'] . "', address1='" . $cc_vars['address1'] . "', address2='" . $cc_vars['address2'] . "', city='" . $cc_vars['city'] . "', state='" . $cc_vars['state'] . "', zip='" . $cc_vars['zip'] . "', comments='" . $cc_vars['comments'] . "', facebook='" . $cc_vars['facebook'] . "', twitter='" . $cc_vars['twitter'] . "'";
	//Run the query
	$result = $recipient->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$recipient->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to add the recipient.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The recipient has been successfully added.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;

		//Redirect the user
		echo "<script type='text/javascript'>window.location.href='" . $_SESSION['home_url'] . "?tab=mygroup'; </script>";
	} //end if

}


/***************************************************************************************/
/* Function Name: RECIPIENT_SUSPEND */
/* Description: Suspend a recipient account. */
/***************************************************************************************/

function RECIPIENT_SUSPEND ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $RECIPIENT_TABLE, $PERMS_TABLE;

	//Assign variables
	$id			= $cc_vars['id'];
	$comments	= $cc_vars['comments'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $account->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$RECIPIENT_TABLE` WHERE recipientid='$id'";

	//Run the query
	$result = $account->query($sql);

	//Get the results
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if ($row["status"] == "suspended") {
		//If the user is already suspended, throw an error.
		$err_msg =  "The recipient account you specified has already been suspended!";

		//Free the result set memory
		$account->free_result($result);
	} else {
		//Free the result set memory
		$account->free_result($result);

		//Build a queryto update the user table
		$sql = "";
		$sql = "UPDATE `$RECIPIENT_TABLE` SET status='suspended', comments='$comments' WHERE recipientid='$id'";
		
		//Run the query
		$result = $account->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: Suspension of the recipient account failed!";
		} else {
			$err_msg =  "The recipient account was successfully suspended!";
		} //end if
	} //end if

	//Close the DB connection
	$account->close($db);

	//Return any errors;
	return $err_msg;

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

}



?>