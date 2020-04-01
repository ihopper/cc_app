<?php
/*
Application: Common Change
Module: User Account
Filename: cc-accounts.php
Version: 1.0
Description: This module allows the user to manage finances, view donations, and manage groups & users (if group owner).

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 06/27/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
*** To be moved to Groups module ***
- GET_GROUP – Fetches and displays group information.
- GET_MEMBERS - Fetches and displays group member information.
- MEMBER_KICK - Removes the specified member from the group. Member donated funds stay with the member, not the group.
- MEMBER_ROLE - Updates the member's role within the group.

*** To be moved to Requests module ***- GET_REQUESTS – Fetches and displays needs that have been put to a vote in the group.
- REQUEST_SHOW - Display request details.- REQUEST_ADD – Add a request to the queue and specify for which group this request is being made.- REQUEST_DELETE – Delete a request from the queue.- REQUEST_UPDATE – Edit a request’s parameters / information.- REQUEST_APPROVE – Approve a request based on group voting results.
- REQUEST_CALC – Use the request approval matrix to calculate approval.- REQUEST_DENY – Deny a request based on group voting results.
- REQUEST_PAY – Pay & close a request based on approval.
- REQUEST_EXPIRE – Mark all requests with a coundown of zero as expired.

*** To be moved to User Profile module ***- GET_VOTES – Display current vote tally- SET_VOTE – Vote yes or no on a request. Comments will be handled through the Threads Module.

**** Redundant ****
- CC_DONATE – Displays a Common Change donation request message and/or button and redirects to E-Commerce pages when clicked. Set these items in the Administration Settings panel. This has been replaced by the USER_REDIRECT function of the User Profile module. It will be used to redirect donors to the Vanco Services donation page.- GET_PERMS – Check user permissions- GET_SESSION – Check session variables for needed information.

*/

//Maintain session
session_start();


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
	$cc_vars['user_id']		= strip_tags($the_request['user_id']);
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
	$cc_vars['new_pass'] 	= addslashes(strip_tags($the_request['new_pass']));
	$cc_vars['conf_pass'] 	= addslashes(strip_tags($the_request['conf_pass']));
	$cc_vars['tos']	 		= strip_tags($the_request['tos']);
	$cc_vars['thumbnail'] 	= addslashes($the_request['thumbnail']); //Don't strip tags, since the directory structure may have some.
	$cc_vars['facebook'] 	= addslashes($the_request['facebook']); //Don't strip tags. Content is URL.
	$cc_vars['share_fb'] 	= strip_tags($the_request['share-fb']);
	$cc_vars['twitter'] 	= addslashes($the_request['twitter']); //Don't strip tags. Content is URL.
	$cc_vars['share_twit'] 	= strip_tags($the_request['share-twit']);
	$cc_vars['filter']	 	= addslashes(strip_tags($the_request['filter']));
	$cc_vars['comments']	= addslashes(strip_tags($the_request['comments']));

	//Variables for requests.
	$cc_vars['recipient_id']	= strip_tags($the_request['recipient_id']);
	$cc_vars['request_id']		= strip_tags($the_request['request_id']);
	$cc_vars['rprocess']		= addslashes(strip_tags($the_request['process']));
	$cc_vars['rtype']			= addslashes(strip_tags($the_request['type']));
	$cc_vars['rcat']			= addslashes(strip_tags($the_request['cat']));
	$cc_vars['ramount']			= strip_tags($the_request['amount']);
	$cc_vars['rtitle']			= addslashes(strip_tags($the_request['title']));
	$cc_vars['rcontent']		= addslashes(strip_tags($the_request['content']));
	$cc_vars['rexpir']			= $the_request['expir'];

	//Variables for votes.
	$cc_vars['vote']				= addslashes(strip_tags($the_request['vote']));
	$cc_vars['vcomment']			= addslashes(strip_tags($the_request['vcomment']));
	$cc_vars['vcat']				= addslashes(strip_tags($the_request['vcat']));
	$cc_vars['num_members']			= addslashes(strip_tags($the_request['num_members']));
	$cc_vars['request_amount']		= addslashes(strip_tags($the_request['request_amount']));
	

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
		case 'update_group': GROUP_UPDATE($cc_vars); break;
		case 'del_member': MEMBER_KICK($cc_vars); break;
		case 'update_role': MEMBER_ROLE($cc_vars); break;
		case 'add_request': REQUEST_ADD($cc_vars); break;
		case 'del_request': REQUEST_DELETE($cc_vars); break;
		case 'update_request': REQUEST_UPDATE($cc_vars); break;
		case 'approve_request': REQUEST_APROVE($cc_vars); break;
		case 'deny_request': REQUEST_DENY($cc_vars); break;
		case 'pay_request': REQUEST_PAY($cc_vars); break;
		case 'set_vote': SET_VOTE($cc_vars); break;
		case 'cc_donate': CC_DONATE($cc_vars); break;
	} //end switch
} //end if-else


/***************************************************************************************/
/* Function Name: GET_GROUP */
/* Description: Fetches and displays group information. */
/***************************************************************************************/

function GET_GROUP ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE;
	global $group_info;

	//Assign variables
	$gid	= $cc_vars['group_id'];


	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();


	//Build a query to pull group information
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";


	//Run the query
	$result = $group->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Group not found!";
	} else {
		$err_msg =  "success";

		//Store the results in an array
	  	$group_info = mysql_fetch_array($result, MYSQL_BOTH);

		//Make sure the user is the group owner
		if ($group_info['userid'] == $_SESSION['user_id']) {
			$isowner = True;
		} else {
			$isowner = False;
		} //end if

	} //end if

	//Close the DB connection
	$group->close($db);

	//Return data
	if ($isowner) {
		//Return the array
		return $group_info;
	} else {
		//Resturn errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if


}


/***************************************************************************************/
/* Function Name: GET_MEMBERS */
/* Description: Fetches and displays group member information. */
/***************************************************************************************/

function GET_MEMBERS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;
	global $member_info, $members;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];


	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to check if the user is the group owner.
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE` WHERE groupid='$gid' AND userid='$uid'";

	//Run the query
	$result = $group->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	//Make sure the user is the group owner
	if ($row['group_role'] == 'Owner' || $row['group_role'] == 'Administrator') {
		$isowner = True;
	} else {
		$isowner = False;
	} //end if

	//Free the result set.
	mysql_free_result($result);

	//If the user is the group owner, carry on.
	if ($isowner) {
		//Build a query to pull group member information
		$sql = "";
		$sql = "SELECT * FROM `$USER_TABLE` WHERE groupid='$gid'";
	
		//Run the query
		$result = $group->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "No Members Found.";
		} else {
			$err_msg =  "success";
	
			//Store the results in an array
			$iCount = 0;
		  	while ($member_info = mysql_fetch_array($result, MYSQL_BOTH)) {
				$members[$iCount] = $member_info;
				$iCount++;
			} //end while
		} //end if
	} //end if

	//Close the DB connection
	$group->close($db);

	//Return data
	if ($isowner) {
		//Return the array
		return $members;
	} else {
		//Return errors
		return $err_msg;
	} //end if


}


/***************************************************************************************/
/* Function Name: MEMBER_KICK */
/* Description: */
/***************************************************************************************/

function MEMBER_KICK ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$mid	= $cc_vars['member_id']; //The ID of the user being kicked.


	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to check if the user is the group owner.
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $group->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	//Make sure the user is the group owner
	if ($row['userid'] == $_SESSION['user_id']) {
		$isowner = True;
	} else {
		$isowner = False;
	} //end if

	//Free the result set.
	mysql_free_result($result);

	//If the user is the group owner, carry on.
	if ($isowner) {
		//Build a query to pull group member information
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET groupid='0'";
		$sql = $sql . " WHERE userid='" . $mid . "'";
	
		//Run the query
		$result = $group->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to remove member from the group.";
		} else {
			$err_msg =  "success";
			//Email the user?
		} //end if
	} else {
		$err_msg = "Error: permission denied! This user does not have sufficient access.";
	} //end if

	//Close the DB connection
	$group->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: MEMBER_ROLE */
/* Description: */
/***************************************************************************************/

function MEMBER_ROLE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$mid	= $cc_vars['member_id']; //The ID of the user being updated.
	$role	= $cc_vars['role']; //The new role for the member.

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to check if the user is the group owner.
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $group->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	//Make sure the user is the group owner
	if ($row['userid'] == $_SESSION['user_id']) {
		$isowner = True;
	} else {
		$isowner = False;
	} //end if

	//Free the result set.
	mysql_free_result($result);

	//If the user is the group owner, carry on.
	if ($isowner) {
		//Build a query to update the member role.
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET group_role='" . $role . "'";
		$sql = $sql . " WHERE userid='" . $mid . "'";
	
		//Run the query
		$result = $group->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to update the member role.";
		} else {
			$err_msg =  "success";
			//Email the user?
		} //end if
	} else {
		$err_msg = "Error: permission denied! This user does not have sufficient access.";
	} //end if

	//Close the DB connection
	$group->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: GET_REQUESTS */
/* Description: */
/***************************************************************************************/

function GET_REQUESTS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;
	global $request_info, $requests;

	//Assign variables
	$requests = array();
	$gid	= $cc_vars['group_id'];


	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to pull all requests in the queue.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE` ON `$REQUEST_TABLE`.ownerid=`$USER_TABLE`.userid";
	$sql = $sql . " WHERE `$REQUEST_TABLE`.groupid='$gid'";

	//Order the results
	$sql = $sql . " ORDER BY requestid DESC";

	//Use UNION to also pull information on the user who submitted the request.

	//Run the query
	$result = $group->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No Requests Found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($request_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$requests[$iCount] = $request_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$group->close($db);

	if ($err_msg == 'success') {
		//Return data
		return $requests;
	} else {
		//Return errors
		return $err_msg;
	}

}


/***************************************************************************************/
/* Function Name: REQUEST_SHOW */
/* Description: */
/***************************************************************************************/

function REQUEST_SHOW ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;
	global $request_info, $requests;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];
	$rid	= $cc_vars['request_id'];

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to pull all requests in the queue.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE` ON `$REQUEST_TABLE`.ownerid=`$USER_TABLE`.userid";
	$sql = $sql . " WHERE `$REQUEST_TABLE`.requestid='$rid'";

	//Use UNION to also pull information on the user who submitted the request.

	//Run the query
	$result = $group->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No Requests Found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$request_info = mysql_fetch_array($result, MYSQL_BOTH);

	} //end if

	//Close the DB connection
	$group->close($db);

	if ($err_msg == 'success') {
		//Return data
		return $request_info;
	} else {
		//Return errors
		return $err_msg;
	}

}


/***************************************************************************************/
/* Function Name: [REQUEST_ADD] */
/* Description: */
/***************************************************************************************/

function REQUEST_ADD ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['recipient_id'];

	$process	= $cc_vars['rprocess'];
	$type		= $cc_vars['rtype'];
	$category	= $cc_vars['rcat'];
	$amount		= $cc_vars['ramount'];
	$title		= $cc_vars['rtitle'];
	$content	= $cc_vars['rcontent'];
	//$timestamp 	= strtotime($cc_vars['rexpir']);

	//Set the current date
	$date = date("Y-m-d H:i:s");
	if ($cc_vars['rexpir'] == '48hours'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +2 days');
	} else if ($cc_vars['rexpir'] == '3days'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +3 days');
	} else if ($cc_vars['rexpir'] == '1week'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +1 week');
	} else if ($cc_vars['rexpir'] == '10days'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +10 days');
	} else if ($cc_vars['rexpir'] == '1month'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +1 month');
	} //end if

	$expir		= date("Y-m-d H:i:s", $timestamp);
	$status		= 'open';
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$REQUEST_TABLE` (`requestid`, `ownerid`, `groupid`, `recipientid`, `process`, `type`, `category`, `amount`, `title`, `content`, `expiration`, `votes`, `status`, `modified`, `num_members`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '" . $rid . "', '" . $process . "', '" . $type . "', '" . $category . "', '" . $amount . "', '" . $title . "', '" . $content . "', '" . $expir . "', NULL, '" . $status . "', '" . $modified . "', '" . $cc_vars['num_members'] . "');";

	//Run the query
	$result = $request->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$request->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to create the request.";

		//Return errors
		return $err_msg;
	} else {
		$err_msg =  "Your request has been submitted for group review.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;

		//Redirect the user
		//echo "<script type='text/javascript'>window.location.href='" . $_SESSION['home_url'] . "?tab=mygroup'; </script>";
	} //end if

}


/***************************************************************************************/
/* Function Name: REQUEST_DELETE */
/* Description: */
/***************************************************************************************/

function REQUEST_DELETE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;

	//Assign variables
	$rid	= $cc_vars['request_id'];

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to delete the specified request.
	$sql = "";
	$sql = "DELETE FROM `$REQUEST_TABLE`";
	$sql = $sql . " WHERE requestid='$rid'";

	//Run the query
	$result = $request->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to delete the request from the group queue.";
	} else {
		$err_msg =  "success";
		//Email the user?
	} //end if

	//Close the DB connection
	$request->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: [REQUEST_UPDATE] */
/* Description: */
/***************************************************************************************/

function REQUEST_UPDATE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['request_id'];

	$process	= $cc_vars['rprocess'];
	$type		= $cc_vars['rtype'];
	$category	= $cc_vars['rcat'];
	$amount		= $cc_vars['ramount'];
	$title		= $cc_vars['rtitle'];
	$content	= $cc_vars['rcontent'];
	//$timestamp 	= strtotime($cc_vars['rexpir']);

	//Set the current date
	$date = date("Y-m-d H:i:s");
	if ($cc_vars['rexpir'] == '24hours'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +1 day');
	} else if ($cc_vars['rexpir'] == '3days'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +3 days');
	} else if ($cc_vars['rexpir'] == '1week'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +1 week');
	} else if ($cc_vars['rexpir'] == '10days'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +10 days');
	} else if ($cc_vars['rexpir'] == '1month'){
		$timestamp	= strtotime(date('Y-m-d H:i:s', strtotime($date)) . ' +1 month');
	} //end if

	$expir		= date("Y-m-d H:i:s", $timestamp);
	$status		= 'open';
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to check if the user is the request owner.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE` WHERE requestid='$rid'";

	//Run the query
	$result = $request->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	//Make sure the user is the request owner
	if ($row['ownerid'] == $uid) {
		$isowner = True;
	} else {
		$isowner = False;
	} //end if

	//Free the result set.
	mysql_free_result($result);

	//If the user is the group owner, carry on.
	if ($isowner) {
		//Build a query to delete the specified request.
		$sql = "";
		$sql = "UPDATE `$REQUEST_TABLE` SET process='" . $process . "', type='" . $type . "', category='" . $category . "', amount='" . $amount . "', title='" . $title . "', content='" . $content . "', expiration='" . $expir . "', status='" . $status . "', modified='" . $modified . "'";
		$sql = $sql . " WHERE requestid='" . $rid . "'";
	
		//Run the query
		$result = $request->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to update the current request.";
		} else {
			$err_msg =  "success";
			//Email the user?
		} //end if
	} else {
		$err_msg = "Error: permission denied! This user does not have sufficient access.";
	} //end if

	//Close the DB connection
	$request->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: REQUEST_APPROVE */
/* Description: */
/***************************************************************************************/

function REQUEST_APPROVE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['request_id'];

	$status		= 'approved';
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to check if the user is the request owner.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE` WHERE requestid='$rid'";

	//Run the query
	$result = $request->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	//Make sure the user is the group owner
	//if ($row['ownerid'] == $_SESSION['user_id']) {
		$isowner = True;
	//} else {
		//$isowner = False;
	//} //end if

	//Free the result set.
	mysql_free_result($result);

	//If the user is the group owner, carry on.
	if ($isowner) {
		//Build a query to delete the specified request.
		$sql = "";
		$sql = "UPDATE `$REQUEST_TABLE` SET status='" . $status . "', modified='" . $modified . "'";
		$sql = $sql . " WHERE requestid='" . $rid . "'";
	
		//Run the query
		$result = $request->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to approve the request.";
		} else {
			$err_msg =  "success";
			//Email the user?
		} //end if
	} else {
		$err_msg = "Error: permission denied! This user does not have sufficient access.";
	} //end if

	//Close the DB connection
	$request->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: REQUEST_CALC */
/* Description: */
/***************************************************************************************/

function REQUEST_CALC ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $matrix_row, $matrix;
	global $REQUEST_MATRIX;

	//Assign variables
	$num_members		= $cc_vars['num_members'];
	$amount				= $cc_vars['request_amount'];
	$funds_available 	= $cc_vars['funds_available'] + $amount; //available funds fix
	$percent_of_funds	= $amount / $funds_available;
	$percent_of_funds	= $percent_of_funds * 100;
	$percent_required	= '';

	$status		= 'denied';

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to get the request matrix
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_MATRIX`";

	//Run the query
	$result = $request->query($sql);

	//Calculate
	$iCount = 0;
  	while ($matrix_row = mysql_fetch_array($result, MYSQL_BOTH)) {
		//Calculate whether or not the request should be approved
		if ($percent_of_funds >= $matrix_row['funds_percentage_lower'] && $percent_of_funds <= $matrix_row['funds_percentage_upper']) {
			if ($num_members < 5) {
				//Set percent required
				$percent_required = $matrix_row['num_members_1'];

			} else if ($num_members >= 6 && $num_members <= 13) {
				//Set percent required
				$percent_required = $matrix_row['num_members_2'];

			} else if ($num_members >= 14 && $num_members <= 20) {
				//Set percent required
				$percent_required = $matrix_row['num_members_3'];

			} else if ($num_members >= 21 && $num_members <= 99) {
				//Set percent required
				$percent_required = $matrix_row['num_members_4'];

			} else if ($num_members >= 100) {
				//Set percent required
				$percent_required = $matrix_row['num_members_5'];

			} else {
				//Do nothing
			} //end if
		} //end if
	} //end while

	//Check to see if the necessary percentage of approved votes has been reached.
	$votes = GET_VOTES ($cc_vars);
	$percent_yes = $votes['yes'] / $num_members;
	$percent_yes = $percent_yes * 100;

	if ($percent_yes >= $percent_required) {
		$status = "approved";
	} else {
		$status = "denied";
	} //end if


	//Free the result set.
	mysql_free_result($result);


	//Close the DB connection
	$request->close($db);

	//Return results
	return $status;

}



/***************************************************************************************/
/* Function Name: [REQUEST_DENY] */
/* Description: */
/***************************************************************************************/

function REQUEST_DENY ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['recipient_id'];

	$status		= 'denied';
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to check if the user is the request owner.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE` WHERE requestid='$rid'";

	//Run the query
	$result = $request->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	//Make sure the user is the group owner
	//if ($row['ownerid'] == $_SESSION['user_id']) {
		$isowner = True;
	//} else {
		//$isowner = False;
	//} //end if

	//Free the result set.
	mysql_free_result($result);

	//If the user is the group owner, carry on.
	if ($isowner) {
		//Build a query to delete the specified request.
		$sql = "";
		$sql = "UPDATE `$REQUEST_TABLE` SET status='" . $status . "', modified='" . $modified . "'";
		$sql = $sql . " WHERE requestid='" . $rid . "'";
	
		//Run the query
		$result = $request->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to deny the request.";
		} else {
			$err_msg =  "success";
			//Email the user?
		} //end if
	} else {
		$err_msg = "Error: permission denied! This user does not have sufficient access.";
	} //end if

	//Close the DB connection
	$request->close($db);

	//Return errors
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: REQUEST_PAY */
/* Description: */
/***************************************************************************************/

function REQUEST_PAY ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $REQUEST_TABLE;

	//Assign variables
	$rid	= $cc_vars['request_id'];

	$status		= 'paid';
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$request = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $request->open();

	//Build a query to update the specified request.
	$sql = "";
	$sql = "UPDATE `$REQUEST_TABLE` SET status='" . $status . "', modified='" . $modified . "'";
	$sql = $sql . " WHERE requestid='" . $rid . "'";

	//Run the query
	$result = $request->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to close the request.";
	} else {
		$err_msg =  "success";
		//Email the user?

		//Update the group's available funds.

	} //end if

	//Close the DB connection
	$request->close($db);

	//Return errors
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: REQUEST_EXPIRE */
/* Description: */
/***************************************************************************************/

function REQUEST_EXPIRE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $REQUEST_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];

	$status		= 'closed';
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp


	//Declare new database object with params
	$getrequest = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequest->open();


	//Build a query to get currently open requests and their expiration datetimes.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE` WHERE groupid='$gid' AND status='open'";

	//Run the query
	$result = $getrequest->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No requests found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($request_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$requests[$iCount] = $request_info;
			$iCount++;
		} //end while



		//Loop through the requests and check the expiration datetimes.
		foreach ($requests as $request) {
			//Check the time remaining on this request
			$datetime1 = strtotime($request['expiration']);
			$datetime2 = strtotime("now");
			$time_left = $datetime1 - $datetime2;
	
			//If the request has expired, carry on.
			if ($time_left <= 0) {
				//Update variables
				$rid = $request['requestid'];
	
				//Build a query to update the request status.
				$sql = "";
				$sql = "UPDATE `$REQUEST_TABLE` SET status='$status', modified='$modified' WHERE requestid='$rid'";
			
				//Run the query
				$result = $getrequest->query($sql);
			
				//Count the rows
				$num_rows = mysql_affected_rows();
			
				if ($num_rows < 1){
					//If no rows were affected, throw an error.
					$err_msg =  "Error: unable to close the request.";
				} else {
					$err_msg =  "success";
					//Email the user?
				} //end if
	
			} //end if
	
		} //end foreach

	} //end if

	//Close the DB connection
	$getrequest->close($db);

	//Return errors
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: GET_VOTES */
/* Description: */
/***************************************************************************************/

function GET_VOTES ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $VOTE_TABLE;
	global $votes_no, $votes_yes, $vote_totals;

	//Assign variables
	$rid	= $cc_vars['request_id'];
	$votes_yes = 0;
	$votes_no = 0;

	//Declare new database object with params
	$votes = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $votes->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$VOTE_TABLE` WHERE requestid='" . $rid . "'";

	//Run the query
	$result = $votes->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No votes found for this request.";
	} else {
		$err_msg =  "success!";

		//Total the votes
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			if ($row['vote'] == 'approve') {
				$votes_yes++;
			} elseif ($row['vote'] == 'disapprove') {
				$votes_no++;
			} //end if
		} //end while
		

		//Store the results in an array
		$vote_totals['yes'] = $votes_yes;
		$vote_totals['no'] = $votes_no;
		$vote_totals['total'] = $votes_yes + $votes_no;
	} //end if

	//Close the DB connection
	$votes->close($db);
	
	if ($num_rows < 1){
		//Return errors
		return $err_msg;
	} else {
		//Return the array.
		return $vote_totals;
	} //end if

}



/***************************************************************************************/
/* Function Name: SET_VOTE */
/* Description: */
/***************************************************************************************/

function SET_VOTE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $VOTE_TABLE, $THREAD_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['request_id'];

	$vote		= $cc_vars['vote'];
	$datestamp	= date("Y-m-d H:i:s"); //Date-Time stamp

	$has_voted	= "false"; //Flag for vote update.

	//Declare new database object with params
	$votes = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $votes->open();

	//Build a query to check if the user has already voted on this request.
	$sql = "";
	$sql = "SELECT * FROM `$VOTE_TABLE` WHERE userid='$uid' AND requestid='$rid'";

	//Run the query
	$result = $votes->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		$has_voted = "false";
	} else {
		$has_voted = "true";
		//Store the results in an array
	  	$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Get the voteid
		$vid = $row['voteid'];
	} //End if

	//Free the result set.
	mysql_free_result($result);	

	//If the user has not previously voted
	if ($has_voted == "false"){
		//Build a query to record a new vote for the need request.
		$sql = "";
		$sql = "INSERT into `$VOTE_TABLE` (`voteid`, `userid`, `groupid`, `requestid`, `vote`, `datestamp`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '" . $rid . "', '" . $vote . "', '" . $datestamp . "');";
	} elseif ($has_voted == "true"){
		//Build a query to update the user's vote.
		$sql = "";
		$sql = "UPDATE `$VOTE_TABLE` SET vote='" . $vote . "', datestamp='" . $datestamp . "'";
		$sql = $sql . " WHERE voteid='" . $vid . "'";	
	} //End if


	if ($has_voted == "false"){
		//Run the query
		$result = $votes->query($sql);
		
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to submit vote.";
		} else {
			$err_msg =  "Vote submitted!";
	
			//If everything worked OK, add the comment thread.
			//Build a query to insert a new request into the group queu.
			$sql = "";
			$sql = "INSERT into `$THREAD_TABLE` (`threadid`, `userid`, `groupid`, `requestid`, `content`, `category`, `childof`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '" . $rid . "', '" . $cc_vars['vcomment'] . "', '" . $vote . "', '" . $cc_vars['childof'] . "');";
			
			//Run the query
			$result = $votes->query($sql);
			
			//Count the rows
			$num_rows = mysql_affected_rows();
	
			if ($num_rows < 1){
				//If no rows were affected, throw an error.
				$err_msg =  "Error: unable to submit comment.";
			} else {
				$err_msg =  "Comment submitted!";
			} //end if

			//Once the vote has been submitted and the new comment has been added, update existing comments to reflect the vote.
			$sql = "";
			$sql = "UPDATE `$THREAD_TABLE` SET `category`='$vote'";
			$sql = $sql . " WHERE `userid`='$uid' AND `requestid`='$rid'";
			
			//Run the query
			$result = $votes->query($sql);
			
			//Count the rows
			$num_rows = mysql_affected_rows();
	
			if ($num_rows < 1){
				//If no rows were affected, throw an error.
				$err_msg =  "Error: unable to update comments.";
			} else {
				$err_msg =  "Comments updated!";
			} //end if
			
		} //end if

		/************* APPROVAL CALCULATIONS *******************/

		//Check available group funds
		$cc_vars['funds_available'] = CHECK_FUNDS($cc_vars);

		//Calculate whether the request is approved
		$status = REQUEST_CALC ($cc_vars);

		//If the status is approved, update the request.
		if ($status == "approved") {
			REQUEST_APPROVE ($cc_vars);
		} //end if

	} //end if

	//Close the DB connection
	//$votes->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: CHECK_VOTE */
/* Description: Check to see if the user has voted on the specified thread.*/
/***************************************************************************************/

function CHECK_VOTE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $VOTE_TABLE;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['request_id'];

	$has_voted	= "false"; //Flag for vote update.

	//Declare new database object with params
	$votes = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $votes->open();

	//Build a query to check if the user has already voted on this request.
	$sql = "";
	$sql = "SELECT * FROM `$VOTE_TABLE` WHERE userid='$uid' AND requestid='$rid'";

	//Run the query
	$result = $votes->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		$has_voted = "false";
	} else {
		$has_voted = "true";
	} //End if

	//Close the DB connection
	$votes->close($db);

	//Return errors
	return $has_voted;

}



/***************************************************************************************/
/* Function Name: CHECK_FUNDS */
/* Description: Returns the total available funds for a group */
/***************************************************************************************/

function CHECK_FUNDS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $REQUEST_TABLE, $GROUP_TABLE;
	global $funds_available;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$checkfunds = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $checkfunds->open();


	//Build a query to pull group information
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $checkfunds->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Group not found!";
	} else {
		$err_msg =  "success";

		//Store the results in an array
	  	$row = mysql_fetch_array($result, MYSQL_BOTH);
		$funds_available = $row['funds_available'];

		//Free the result set
		mysql_free_result($result);

		//Build a query to get the amount of open requests
		$sql = "";
		$sql = "SELECT * FROM `$REQUEST_TABLE` WHERE groupid='$gid' AND status='open'";

		//Run the query
		$result = $checkfunds->query($sql);

		//Store the results in an array
		$iCount = 0;
		$committed_funds = 0;
	  	while ($request = mysql_fetch_array($result, MYSQL_BOTH)) {
			$committed_funds = $committed_funds + $request['amount'];
			$iCount++;
		} //end while

		//Calculate true available funds
		$funds_available = $funds_available - $committed_funds;

	} //end if

	//Close the DB connection
	//$checkfunds->close($db);

	//Return the available funds
	return $funds_available;

}


?>