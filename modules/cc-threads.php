<?php
/*
Application: Common Change
Module: Threads
Filename: cc-threads.php
Version: 1.0
Description: This module manages comment threads for users and groups.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 08/06/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
- GET_THREADS – Fetches and displays threads associated with a particular group or user.- CREATE_THREAD – Creates a new thread. This may be a new topic or a response to an existing thread.- DELETE_THREAD – Deletes a thread and all associated thread responses. If the thread being deleted is itself a response, the parent thread will remain, and only the response thread will be deleted.
- UPDATE_THREAD - Update the content of a thread. This can only be done by a thread owner.- SEARCH_THREADS – This function is primarily aimed at the administrative panel to allow for quick searching of threads by user, group, or thread content (text). This could also be integrated into a separate threads page that displays “current discussions”.
- GET_PERMS – Check user permissions- GET_SESSION – Check session variables for needed information.

*/


/***************************************************************************************/
/* Includes */
/***************************************************************************************/
include_once 'cc-config.inc.php'; //Application Configuration File
include_once 'class.mysqldb.php';
include_once 'cc-getrequest.php';


/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/





/***************************************************************************************/
/* Function Name: GET_THREADS */
/* Description: */
/***************************************************************************************/

function GET_THREADS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $THREAD_TABLE, $USER_TABLE;
	global $thread_info, $threads;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$rid	= $cc_vars['request_id'];

	//Declare new database object with params
	$getthreads = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getthreads->open();

	//Build a query to list threads.
	$sql = "";
	//$sql = "SELECT * FROM `$THREAD_TABLE`";
	//$sql = $sql . " LEFT JOIN `$USER_TABLE`";
	//$sql = $sql . " ON $THREAD_TABLE.userid=$USER_TABLE.userid";
	//$sql = $sql . " WHERE `$THREAD_TABLE`.groupid=$gid";

	$sql = $sql . " SELECT t.*, u.fname, u.lname, u.thumb, u.userid";
	$sql = $sql . " FROM `$THREAD_TABLE` t, `$USER_TABLE` u";
	$sql = $sql . " WHERE t.requestid='$rid' AND u.userid = t.userid";
	
	//If the request id is provided, drill down to that level.
	if($rid != '') {
		//$sql = $sql . " AND `$THREAD_TABLE`.requestid=$rid";
	}

	//Order the results
	$sql = $sql . " ORDER BY threadid DESC";

	//Run the query
	$result = $getthreads->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No comments were found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($thread_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$threads[$iCount] = $thread_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getthreads->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $threads;
	} //end if


}

/***************************************************************************************/
/* Function Name: CREATE_THREAD */
/* Description: */
/***************************************************************************************/

function CREATE_THREAD($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $THREAD_TABLE;

	//Assign variables
	$gid	= $cc_vars['group_id'];
	$uid	= $cc_vars['user_id'];
	$rid	= $cc_vars['request_id'];

	$created = date("Y-m-d H:i:s"); //Date-Time stamp

	//Declare new database object with params
	$thread = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $thread->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$THREAD_TABLE` (`threadid`, `userid`, `groupid`, `requestid`, `content`, `category`, `childof`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '" . $rid . "', '" . $cc_vars['thread_content'] . "', '" . $cc_vars['thread_category'] . "', '" . $cc_vars['childof'] . "');";

	//Run the query
	$result = $thread->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$thread->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to add the comment thread.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The comment has been successfully added.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if


}


/***************************************************************************************/
/* Function Name: DELETE_THREAD */
/* Description: */
/***************************************************************************************/

function DELETE_THREAD($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $THREAD_TABLE, $PERMS_TABLE;

	//Assign variables
	$threadid	= $cc_vars['thread_id'];
	$uid		= $cc_vars['user_id'];
	$gid		= $cc_vars['group_id'];

	//Declare new database object with params
	$account = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $thread->open();


	//Build a query to make sure the user has rights to delete this comment thread
	$sql = "";
	$sql = "SELECT * FROM `$THREAD_TABLE` WHERE threadid='$threadid' AND userid='$uid'";

	//Run the query
	$result = $thread->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//if no rows were returned, this this user is not the owner of the comment thread. 
	
		//Throw a permissions error.
		$err_msg =  "Error: You do not have permission to delete this comment thread!";
	} else {
		
		//Free the resultset memory.
		$thread->free_result($result);

		//Build a query to delete the comment thread.
		$sql = "";
		$sql = "DELETE FROM `$THREAD_TABLE` WHERE threadid='$threadid'";
	
		//Run the query
		$result = $thread->query($sql);
	
		//Count the rows
		$num_rows = mysql_affected_rows();
	
		
		if ($num_rows < 1){
			//If no rows were affected, throw an error.
			$err_msg =  "Error: Deletion of the comment thread failed!";
		} else {
			$err_msg =  "The comment was successfully deleted!";
		} //end if	

	} //end if



	//Close the DB connection
	$thread->close($db);

	//Return any errors;
	return $err_msg;

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

}

/***************************************************************************************/
/* Function Name: SEARCH_THREADS */
/* Description: */
/***************************************************************************************/

function SEARCH_THREADS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $THREAD_TABLE, $USER_TABLE;
	global $thread_info, $threads;

	//Assign variables
	$uid		= $cc_vars['user_id'];
	$gid		= $cc_vars['group_id'];
	$filter		= $cc_vars['filter'];


	//Declare new database object with params
	$getpeople = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getthreads->open();

	//Build a query search for users and recipients, given filter criteria.
	$sql = "";
	$sql = "SELECT * FROM `$THREAD_TABLE` WHERE `title` LIKE '%$filter%' OR `content` LIKE '%$filter%'";

	//Run the query
	$result = $getpeople->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No comments matched your query.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($thread_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$threads[$iCount] = $thread_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getthreads->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $threads;
	} //end if

}


?>