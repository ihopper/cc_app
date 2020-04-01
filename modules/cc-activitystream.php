<?php
/*
Application: Common Change
Module: Actiity Stream
Filename: cc-activitystream.php
Version: 1.0
Description: This module generates a stream of activity from the database for the specified user.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 12/04/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.
- SendMail Class – Provides email functionality.

Function Definitions:
- GET_ACTIVITY - Fetch and display recent activity from the Common Change application

*/

//Maintain session
session_start();

/***************************************************************************************/
/* Includes */
/***************************************************************************************/
include_once "cc-config.inc.php";
include_once "class.mysqldb.php";



/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/
$DISPLAY_LIMIT = 20; //The number of recent items to display.

$FETCH_NEED_AMOUNT = 1;
$FETCH_PAID_AMOUNT = 1;




/***************************************************************************************/
/* Function Name: [Name] */
/* Description: */
/***************************************************************************************/

function GET_ACTIVITY($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $USER_TABLE, $GROUP_TABLE, $REQUEST_TABLE, $FINANCE_TABLE, $JOIN_TABLE, $THREAD_TABLE, $VOTE_TABLE;
	global $cc_activity, $tmp_array;

	//Assign variables
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$getactivity = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getactivity->open();


	/****** Get recent group requests submitted ******/
	$sql = "";
	$sql = "SELECT j.*, u.fname, u.lname, u.thumb";
	$sql = $sql . " FROM `$JOIN_TABLE` j, `$USER_TABLE` u";
	$sql = $sql . " WHERE u.userid = j.userid";
	$sql = $sql . " AND j.groupid = '$gid'";

	//Run the query
	$result = $getactivity->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	//Store the results in an array.
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No Records Found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($tmp_array = mysql_fetch_array($result, MYSQL_BOTH)) {
	  		$cc_activity[$iCount]['category'] 	= 'Join Request';
			$cc_activity[$iCount]['user']		= $tmp_array['fname'] . ' ' . $tmp_array['lname'];
			$cc_activity[$iCount]['thumb'] 		= $tmp_array['thumb'];
			$cc_activity[$iCount]['title'] 		= $tmp_array['fname'] . ' ' . $tmp_array['lname'] . ' has requested to join your group.';
			$cc_activity[$iCount]['link'] 		= 'http://www.isaachopper.com/dev/cc/?tab=account';
			$cc_activity[$iCount]['content'] 	= '';
			$cc_activity[$iCount]['modified'] 	= $tmp_array['created'];
			$iCount++;
		} //end while
	} //end if

	//Free the result set.
	mysql_free_result($result);

	/****** Get new group members ******/


	/****** Get recent group comments ******/
	$sql = "";
	$sql = "SELECT t.*, u.fname, u.lname, u.thumb, r.title ";
	$sql = $sql . " FROM `$THREAD_TABLE` t, `$USER_TABLE` u, `$REQUEST_TABLE` r";
	$sql = $sql . " WHERE u.userid = t.userid AND r.requestid = t.requestid";
	$sql = $sql . " AND t.groupid = '$gid'";

	//Run the query
	$result = $getactivity->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	$num_groups = $num_rows;
	
	//Store the results in an array.
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No Records Found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		//$iCount = 0;
	  	while ($tmp_array = mysql_fetch_array($result, MYSQL_BOTH)) {
	  		$cc_activity[$iCount]['category'] 	= 'Comment';
			$cc_activity[$iCount]['user']		= $tmp_array['fname'] . ' ' . $tmp_array['lname'];
			$cc_activity[$iCount]['thumb'] 		= $tmp_array['thumb'];
			$cc_activity[$iCount]['title'] 		= $tmp_array['title'];
			$cc_activity[$iCount]['link'] 		= 'http://www.isaachopper.com/dev/cc/?tab=view_request&rid=' . $tmp_array['requestid'];
			$cc_activity[$iCount]['content'] 	= $tmp_array['content'];
			$cc_activity[$iCount]['modified'] 	= $tmp_array['created'];
			$iCount++;
		} //end while
	} //end if

	//Free the result set.
	mysql_free_result($result);


	/****** Get need requests ******/
	//Build a query to pull all requests in the queue.
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE` ON `$REQUEST_TABLE`.ownerid=`$USER_TABLE`.userid";
	$sql = $sql . " WHERE `$REQUEST_TABLE`.groupid='$gid'";

	//Run the query
	$result = $getactivity->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	//Store the results in an array.
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No Records Found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		//$iCount = 0;

	  	while ($tmp_array = mysql_fetch_array($result, MYSQL_BOTH)) {
	  		$cc_activity[$iCount]['category'] 	= 'Request';
			$cc_activity[$iCount]['user']		= $tmp_array['fname'] . ' ' . $tmp_array['lname'];
			$cc_activity[$iCount]['thumb'] 		= $tmp_array['thumb'];
			$cc_activity[$iCount]['title'] 		= $tmp_array['title'];
			$cc_activity[$iCount]['link'] 		= 'http://www.isaachopper.com/dev/cc/?tab=view_request&rid=' . $tmp_array['requestid'];
			$cc_activity[$iCount]['content'] 	= 'Amount: $' . $tmp_array['amount'] . '<br />' . $tmp_array['content'];
			$cc_activity[$iCount]['modified'] 	= $tmp_array['modified'];

			$iCount++;
		} //end while
	} //end if

	//Free the result set.
	mysql_free_result($result);


	/****** Get votes ******/
	$sql = "";
	$sql = $sql . "SELECT v.*, u.fname, u.lname, u.thumb, r.title ";
	$sql = $sql . " FROM `$VOTE_TABLE` v, `$USER_TABLE` u, `$REQUEST_TABLE` r";
	$sql = $sql . " WHERE u.userid = v.userid AND r.requestid = v.requestid";
	$sql = $sql . " AND v.groupid = '$gid'";

	//Run the query
	$result = $getactivity->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	//Store the results in an array.
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No Records Found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		//$iCount = 0;
	  	while ($tmp_array = mysql_fetch_array($result, MYSQL_BOTH)) {
	  		$cc_activity[$iCount]['category'] 	= 'Vote';
	  		$cc_activity[$iCount]['user']		= $tmp_array['fname'] . ' ' . $tmp_array['lname'];
	  		$cc_activity[$iCount]['thumb'] 		= $tmp_array['thumb'];
			$cc_activity[$iCount]['title'] 		= $tmp_array['title'];
			$cc_activity[$iCount]['link'] 		= 'http://www.isaachopper.com/dev/cc/?tab=view_request&rid=' . $tmp_array['requestid'];
			$cc_activity[$iCount]['content'] 	= 'Vote: ' . $tmp_array['vote'];
			$cc_activity[$iCount]['modified'] 	= $tmp_array['datestamp'];
			$iCount++;
		} //end while
	} //end if

	//Free the result set.
	mysql_free_result($result);



	//Close the DB connection
	$getactivity->close($db);

}


?>