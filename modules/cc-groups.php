<?php
/*
Application: Common Change
Module: Groups
Filename: cc-groups.php
Version: 1.0
Description: This module manages user profiles and account activity, including account creation, suspension, and deletion.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 07/16/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
- GET_MEMBER_REQUESTS - Fetches and displays group membership requests.
- LIST_MEMBERS - Fetches and displays members of a given group.
- GET_INVITATIONS - Fetches and displays group invitations received by the user as long as the user is not yet grouped.
- ACCEPT_INVITE - Adds the user to the group from which an invitation was issued.
- REJECT_INVITE - Rejects an invitation to a group. Invitations can be reissued by the same group.

- GET_GROUPS – Display groups listed in the system with accompanying group / voting information.- GROUP_SEARCH – Fetch and display a selection of groups based on filters (i.e. name, state, etc.).- GROUP_DETAIL – Displays details of a group, such as picture, location, voting history, current groups, threads, etc.
- GROUP_ADD - Add a new group to the database.
- GROUP_DELETE - Delete a group from the database.
- GROUP_UPDATE - Update a group's information in the database.
- GROUP_SUSPEND - Suspend a group account.
- GROUP_JOIN - Request to join a specified group.
- ACCEPT_JOIN - Adds the requesting user to the group.
- REJECT_JOIN - Rejects group join request.

****** No longer needed *******
- GROUP_INVITE – Allows a user to invite a person to a group. Email invitations will be grouped with the email module.
  - This function has been moved to the People Module as as the PEOPLE_INVITE function.
*/

//Maintain session
session_start();

/***************************************************************************************/
/* Includes */
/***************************************************************************************/

include_once 'cc-config.inc.php'; //Application Configuration File
include_once 'class.mysqldb.php';
include_once 'cc-getrequest.php';



/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/

/* Variables now being collected and assigned in cc-getrequest.php */

/***************************************************************************************/
/* Function Name: GET_MEMBER_REQUESTS */
/* Description: Fetches and displays group membership requests. */
/***************************************************************************************/

function GET_MEMBER_REQUESTS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $JOIN_TABLE, $USER_TABLE;
	global $join_info, $join_requests;

	//Assign variables
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$getrequests = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequests->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$JOIN_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE` ON `$JOIN_TABLE`.userid=`$USER_TABLE`.userid";
	$sql = $sql . " WHERE `$JOIN_TABLE`.groupid='$gid' AND `$JOIN_TABLE`.response=''";

	//Run the query
	$result = $getrequests->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "There are no group membership requests at this time.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($join_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$join_requests[$iCount] = $join_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getrequests->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $join_requests;
	} //end if

}



/***************************************************************************************/
/* Function Name: LIST_MEMBERS */
/* Description: Display users listed in the system with accompanying group / voting information. */
/***************************************************************************************/

function LIST_MEMBERS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;
	global $member_info, $members;

	//Assign variables
	$members = array(); //Initialize/empty the array
	$gid	= $cc_vars['group_id'];
	$sort	= $cc_vars['sort'];

	//Declare new database object with params
	$getmembers = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getmembers->open();

	//Build a query to list users, with sort option. The left join looks for matching user data in the groups table.
	//If the user does not belong to a group the user information is still produced, minus group info.
	$sql = "";
	$sql = "SELECT * FROM `$USER_TABLE`";
	$sql = $sql . " WHERE groupid='$gid'";

	if($sort !='') {
		$sql = $sql . " ORDER BY $sort"; //Sort order
	}

	//Run the query
	$result = $getmembers->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No users found for that group.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($member_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$members[$iCount] = $member_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getmembers->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $members;
	} //end if


}



/***************************************************************************************/
/* Function Name: GET_INVITATIONS */
/* Description: Fetches and displays group invitations received by the user as long as */
/* 				the user is not yet grouped. */
/***************************************************************************************/

function GET_INVITATIONS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $INVITE_TABLE, $USER_TABLE;
	global $invite_info, $invites;
	$invites = array(); //Initialize the array

	//Assign variables
	$unique_id	= $cc_vars['unique_id'];
	$email		= $cc_vars['email'];

	//Declare new database object with params
	$getrequests = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequests->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT `$INVITE_TABLE`.*, `$USER_TABLE`.fname, `$USER_TABLE`.lname, `$USER_TABLE`.groupid FROM `$INVITE_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE` ON `$INVITE_TABLE`.ownerid=`$USER_TABLE`.userid";
	$sql = $sql . "  WHERE `$INVITE_TABLE`.email='$email'";
	if ($unique_id != '') {
		$sql = $sql . " AND `$INVITE_TABLE`.unique_id='$unique_id'";
	} //end if

	//Run the query
	$result = $getrequests->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "You have no group membership requests at this time.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($invite_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$invites[$iCount] = $invite_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getrequests->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $invites;
	} //end if

}



/***************************************************************************************/
/* Function Name: ACCEPT_INVITE */
/* Description: Adds the user to the group from which an invitation was issued. */
/***************************************************************************************/

function ACCEPT_INVITE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $INVITE_TABLE, $USER_TABLE;
	global $request_info, $requests;

	//Assign variables
	$uid 		= $cc_vars['user_id'];
	$unique_id	= $cc_vars['unique_id'];
	$email		= $cc_vars['email'];

	//Declare new database object with params
	$getrequests = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequests->open();

	//Build a query to make sure the user received an invitation to the specified group.
	$sql = "";
	$sql = "SELECT * FROM `$INVITE_TABLE` WHERE email='$email'";

	//Run the query
	$result = $getrequests->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "You have not been invited to join this group";
	} else {

		//Get the results
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Assign variables
		$gid = $row['groupid'];

		//Free the result set
		mysql_free_result($result);
		
		//Update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET groupid='" . $gid . "'";
		$sql = $sql . " WHERE userid='" . $uid . "'";

		//Run the query
		$result = $getrequests->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();

		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "We were unable to add you to the group, please contact a site administrator for assistance.";
		} else {
			$err_msg =  "You have successfully joined the group.";			

			//Update session variables
			$_SESSION['group_id'] = $gid;
		} //end if

	} //end if

	//Close the DB connection
	$getrequests->close($db);


	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

	//Return errors
	return $err_msg;


}



/***************************************************************************************/
/* Function Name: REJECT_INVITE */
/* Description: Adds the user to the group from which an invitation was issued. */
/***************************************************************************************/

function REJECT_INVITE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $INVITE_TABLE;
	global $request_info, $requests;

	//Assign variables
	$uid 		= $cc_vars['user_id'];
	$email		= $cc_vars['email'];

	//Declare new database object with params
	$getrequests = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequests->open();

	//Build a query to make sure the user received an invitation to the specified group.
	$sql = "";
	$sql = "SELECT * FROM `$INVITE_TABLE` WHERE email='$email'";

	//Run the query
	$result = $getrequests->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "You have not been invited to join this group";
	} else {

		//Get the results
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Assign variables
		$gid = $row['groupid'];

		//Free the result set
		mysql_free_result($result);
		
		//Update the user table
		$sql = "";
		$sql = "DELETE FROM `$INVITE_TABLE`";
		$sql = $sql . " WHERE email='" . $email . "' AND groupid='" . $gid . "'";

		//Run the query
		$result = $getrequests->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();

		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "We were unable to update the invitation, please contact a site administrator for assistance.";
		} else {
			$err_msg =  "You have rejected the group invitation.";			

			//Update session variables
			$_SESSION['group_id'] = $gid;
		} //end if

	} //end if

	//Close the DB connection
	$getrequests->close($db);


	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

	//Return errors
	return $err_msg;


}



/***************************************************************************************/
/* Function Name: GET_GROUPS */
/* Description: Display groups listed in the system with accompanying member information. */
/***************************************************************************************/

function GET_GROUPS ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;
	global $group_info, $groups;

	//Assign variables
	$groups = array();
	$sort	= $cc_vars['sort'];
	$search	= $cc_vars['search'];


	//Declare new database object with params
	$getgroups = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getgroups->open();

	//Build a query to list groups, with sort option. The left join looks for matching group data in the user table to get the group owner.
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE`";
	$sql = $sql . " ON $GROUP_TABLE.userid=$USER_TABLE.userid";

	if($sort !='') {
		$sql = $sql . " ORDER BY $sort"; //Sort order
	}

	//Run the query
	$result = $getgroups->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No groups found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($group_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			//Filter if there are search criteria.
			if($search != ''){
				//Search for the string
				$found = stristr($group_info['name'] . $group_info['description'] . $group_info['tags'], $search);
				if($found !== false){
					$groups[$iCount] = $group_info;
				} //end if
			} else {
				$groups[$iCount] = $group_info;
				//$iCount++;
			} //end if
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getgroups->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $groups;
	} //end if


}



/***************************************************************************************/
/* Function Name: GROUP_SEARCH */
/* Description: Fetch and display a selection of groups based on filters (i.e. name, state, etc.). */
/***************************************************************************************/

function GROUP_SEARCH ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;
	global $group_info, $groups;

	//Assign variables
	$sort	= $cc_vars['sort'];
	$ftype 	= $cc_vars['ftype']; //Filter type, ptions: 'lname', 'fname', 'country', 'state', 'zip'
	$filter	= $cc_vars['filter'];


	//Declare new database object with params
	$getgroups = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getgroups->open();

	//Build a query search for users and recipients, given filter criteria.
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE $ftype='" . $filter . "'";

	//Run the query
	$result = $getgroups->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No users found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($group_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$groups[$iCount] = $group_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getgroups->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $groups;
	} //end if


}


/***************************************************************************************/
/* Function Name: GET_DETAIL */
/* Description: Displays details of a person, such as picture, location, voting history, */
/* current groups, threads, etc. */
/***************************************************************************************/

function GROUP_DETAIL ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $GROUP_TABLE;
	global $group_info;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];


	//Declare new database object with params
	$groupdetail = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $groupdetail->open();


	//Build a query to pull group information
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $groupdetail->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Group not found!";
	} else {
		$err_msg =  "success";

		//Store the results in an array
	  	$group_info = mysql_fetch_array($result, MYSQL_BOTH);

		//Free the result set
		mysql_free_result($result);

	} //end if

	//Close the DB connection
	$groupdetail->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $group_info;
	} //end if


}



/***************************************************************************************/
/* Function Name: GROUP_ADD */
/* Description: Add a new recipient to the database. */
/***************************************************************************************/

function GROUP_ADD ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE;

	//Assign variables
	$uid	= $cc_vars['user_id'];

	$status		= 'active';
	$created	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$GROUP_TABLE` (`groupid`, `userid`, `name`, `city`, `state`, `zip`, `country`, `status`, `description`, `covenant`, `thumb`, `tags`, `created`) VALUES (NULL, '" . $uid . "', '" . $cc_vars['group_name'] . "', '" . $cc_vars['group_city'] . "', '" . $cc_vars['group_state'] . "', '" . $cc_vars['group_zip'] . "', '" . $cc_vars['group_country'] . "', '" . $status . "', '" . $cc_vars['group_desc'] . "', '" . $cc_vars['group_covenant'] . "', '" . $cc_vars['group_thumb'] . "', '" . $cc_vars['group_tags'] . "', '" . $created . "')";

	//Run the query
	$result = $group->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to add the group.";

	} else {
		$err_msg =  "The group has been successfully added.";

		/***Update the user's groupid ***/
		//Build a query to get the new groupid
		$sql = "";
		$sql = "SELECT * FROM `$GROUP_TABLE` WHERE userid='" . $uid . "'";

		//Run the query
		$result = $group->query($sql);

		//Store the results
		$row = mysql_fetch_array($result, MYSQL_BOTH);
		$gid = $row['groupid'];

		//Free the result set memory
		$group->free_result($result);

		//Build a query to update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET groupid='" . $gid . "', group_role='Owner'";
		$sql = $sql . " WHERE userid='" . $uid . "'";

		//Run the query
		$result = $group->query($sql);

		//Set the session variable
		$_SESSION['group_id'] = $gid;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;

	} //end if

	//Close the DB connection
	$group->close($db);

	//Return errors
	return $err_msg;
}



/***************************************************************************************/
/* Function Name: GROUP_DELETE */
/* Description: */
/***************************************************************************************/

function GROUP_DELETE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $USER_TABLE, $PERMS_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();


	/******* Remove the users from the group *******/
	//Build a query to update the groupid of each user in the group
	$sql = "";
	$sql = "UPDATE `$USER_TABLE` SET groupid='0'";
	$sql = $sql . " WHERE groupid='" . $gid . "'";

	//Run the query
	$result = $group->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: No users removed from group!";
	} else {
		$err_msg =  $num_rows . " users successfully removed from the group!";
		
		//Update the session variables
		$_SESSION['group_id'] = '0';

		/******* Delete the group *******/
		//Build a query to delete the group
		$sql = "";
		$sql = "DELETE FROM `$GROUP_TABLE` WHERE groupid='$gid'";
	
		//Run the query
		$result = $group->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: Deletion of the group failed!";
		} else {
			$err_msg =  "The group was successfully deleted!";
		} //end if
	} //end if

	//Close the DB connection
	$group->close($db);

	//Return any errors;
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: GROUP_UPDATE */
/* Description: */
/***************************************************************************************/

function GROUP_UPDATE ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $RECIPIENT_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];

	if ($cc_vars['status'] != '') {
		$status		= $cc_vars['status'];
	} else {
		$status = 'active';
	}
	$modified	= date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();

	//Build a query to update the recipient specified.
	$sql = "";
	$sql = "UPDATE `$GROUP_TABLE` SET status='$status', name='" . $cc_vars['group_name'] . "', description='" . $cc_vars['group_desc'] . "', covenant='" . $cc_vars['group_covenant'] . "', thumb='" . $cc_vars['group_thumb'] . "', tags='" . $cc_vars['group_tags'] . "'";
	$sql = $sql . " WHERE groupid='" . $gid . "'";

	//Run the query
	$result = $group->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$group->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to update the group.";

		//Return errors
		return $err_msg;
	} else {
		$err_msg =  "The group has been successfully updated.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if

}


/***************************************************************************************/
/* Function Name: GROUP_UPDATE_THUMB */
/* Description: Update the user profile image.
/***************************************************************************************/

function GROUP_UPDATE_THUMB ($cc_vars) {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE;

	$gid = $cc_vars['group_id'];

	$thumb = $thumb_dir . $cc_vars['thumb'];

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $group->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		$err_msg = "Error: The group does not exist!";

		//Free the result set memory
		$group->free_result($result);
	} else {
		//Free the result set memory
		$group->free_result($result);

		//Build a query to update the user table
		$sql = "";
		$sql = "UPDATE `$GROUP_TABLE` SET thumb='" . $thumb . "'";	
		$sql = $sql . " WHERE groupid='$gid'";


		//Run the query
		$result = $group->query($sql);
		

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
	$group->close($db);

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

	//Return any errors;
	return $err_msg;
}



/***************************************************************************************/
/* Function Name: GROUP_SUSPEND */
/* Description: Toggle group suspension*/
/***************************************************************************************/

function GROUP_SUSPEND ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE, $PERMS_TABLE;

	//Assign variables
	$gid		= $cc_vars['group_id'];
	$status		= $cc_vars['status'];
	$comments	= $cc_vars['comments'];

	//Declare new database object with params
	$group = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $group->open();


	//Build a query to check whether the user is already suspended
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$id'";

	//Run the query
	$result = $group->query($sql);

	//Get the results
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	if ($row["status"] == $status) {
		//If the user is already suspended, throw an error.
		$err_msg =  "The group status did not change.";

		//Free the result set memory
		$group->free_result($result);
	} else {
		//Free the result set memory
		$group->free_result($result);

		//Build a queryto update the user table
		$sql = "";
		$sql = "UPDATE `$GROUP_TABLE` SET status='$status' WHERE groupid='$gid'";
		
		//Run the query
		$result = $group->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: unable to update group status!";
		} else {
			$err_msg =  "The group status was successfully updated!";
		} //end if
	} //end if

	//Close the DB connection
	$group->close($db);

	//Return any errors;
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: GROUP_JOIN */
/* Description: Add a new recipient to the database. */
/***************************************************************************************/

function GROUP_JOIN ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $JOIN_TABLE;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$joingroup = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $joingroup->open();

	//Build a query to make sure this request has not already been made.
	$sql = "";
	$sql = "SELECT * FROM `$JOIN_TABLE` WHERE userid='$uid' AND groupid='$gid'";

	//Run the query
	$result = $joingroup->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, this request is unique.

		//Free the result set memory
		$joingroup->free_result($result);

		//Build a query to insert the request.
		$sql = "";
		$sql = "INSERT into `$JOIN_TABLE` (`joinid`, `userid`, `groupid`, `response`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '')";

		//Run the query
		$result = $joingroup->query($sql);

		//Count the rows
		$num_rows = mysql_affected_rows();
		
		if ($num_rows < 1){
			//if no rows were affected, throw an error.
			$err_msg = "Error: unable to add the group request.";
		} //end if
		
	} else {
		//If a record was found, this request has already been submitted.
		$err_msg = "You have already requested to join this group.";
	} //end if

	//Close the DB connection
	$joingroup->close($db);

	//Return errors
	return $err_msg;
}



/***************************************************************************************/
/* Function Name: ACCEPT_JOIN */
/* Description: Adds the user to the group from which an invitation was issued. */
/***************************************************************************************/

function ACCEPT_JOIN ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $JOIN_TABLE, $USER_TABLE;
	global $request_info, $requests;

	//Assign variables
	$uid 		= $cc_vars['join_id'];
	$gid		= $cc_vars['group_id'];
	$email		= $cc_vars['email'];

	//Declare new database object with params
	$getrequests = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequests->open();

	//Build a query to make sure the user requested to join the specified group.
	$sql = "";
	$sql = "SELECT * FROM `$JOIN_TABLE` WHERE userid='$uid' AND groupid='$gid'";

	//Run the query
	$result = $getrequests->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "The user has not requested to join this group.";
	} else {

		//Get the results
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Free the result set
		mysql_free_result($result);
		
		//Update the user table
		$sql = "";
		$sql = "UPDATE `$USER_TABLE` SET groupid='" . $gid . "'";
		$sql = $sql . " WHERE userid='" . $uid . "'";

		//Run the query
		$result = $getrequests->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();

		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "We were unable to add this user to the group, please contact a site administrator for assistance.";
		} else {
			$err_msg =  "You have successfully added the user to the group.";			
			
			//Update the join table
			$sql = "";
			$sql = "UPDATE `$JOIN_TABLE` SET response='accepted'";
			$sql = $sql . " WHERE userid='$uid' AND groupid='$gid'";

			//Run the query
			$result = $getrequests->query($sql);
		
			//Count the rows
			$num_rows = mysql_affected_rows();

			if ($num_rows < 1){
				$err_msg = "Error: Could not update the join table.";
			} //end if

		} //end if

	} //end if

	//Close the DB connection
	$getrequests->close($db);


	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

	//Return errors
	return $err_msg;


}



/***************************************************************************************/
/* Function Name: REJECT_JOIN */
/* Description: Adds the user to the group from which an invitation was issued. */
/***************************************************************************************/

function REJECT_JOIN ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $JOIN_TABLE;
	global $request_info, $requests;

	//Assign variables
	$uid 		= $cc_vars['join_id'];
	$gid		= $cc_vars['group_id'];

	//Declare new database object with params
	$getrequests = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getrequests->open();

	//Build a query to make sure the user received an invitation to the specified group.
	$sql = "";
	$sql = "SELECT * FROM `$JOIN_TABLE` WHERE WHERE userid='$uid' AND groupid='$gid'";

	//Run the query
	$result = $getrequests->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "The user has not requested to join this group";
	} else {

		//Get the results
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Free the result set
		mysql_free_result($result);
		
		//Update the join table
		$sql = "";
		$sql = "DELETE FROM `$JOIN_TABLE`";
		$sql = $sql . " WHERE userid='$uid' AND groupid='$gid'";

		//Run the query
		$result = $getrequests->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();

		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "We were unable to update the request, please contact a site administrator for assistance.";
		} else {
			$err_msg =  "You have rejected the group join request.";			

		} //end if

	} //end if

	//Close the DB connection
	$getrequests->close($db);


	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

	//Return errors
	return $err_msg;


}




?>