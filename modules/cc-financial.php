<?php
/*
Application: Common Change
Module: Finincial
Filename: cc-financial.php
Version: 1.0
Description: This module allows CC administrators to display and manage financial information for groups and individual users.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 08/08/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
- GET_DONATIONS – Display all donations that have not ben approved.
- GET_GROUP_DONATIONS – Display donation information by group.
- GET_GROUP_FUNDS - Display available and shared funds by group.- GET_USER_DONATIONS – Display donation information for a specified user.
- GET_USER_DONATION_TOTAL - Fetches the user's group donations and totals them.
- GET_SEED_DONATIONS – Display donation information for the Common Change seed fund.- ADD_DONATION – Add a new donation for a specified user.
- VIEW_DONATION - View details of a specific donation.- DELETE_DONATION – Delete a donation from a specified user account.- UPDATE_DONATION – Update the information associated with a specific donation (i.e. fix check number, adjust amount, etc.).
- APPROVE_DONATION – Mark a donation as approved after confirmation of payment has been received.- REPORT_DONATIONS – Generate a series of printable donation reports based on administrator input.
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
/* Function Name: GET_DONATIONS */
/* Description: */
/***************************************************************************************/

function GET_DONATIONS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE, $USER_TABLE;
	global $donation_info, $donations, $funds_available, $funds_shared;

	//Assign variables
	$sort	= $cc_vars['sort'];
	$donations = array();

	//Declare new database object with params
	$getdonations = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdonations->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$FINANCE_TABLE`";
	$sql = $sql . " LEFT JOIN `$USER_TABLE` ON `$FINANCE_TABLE`.userid=`$USER_TABLE`.userid";
	$sql = $sql . "  WHERE `$FINANCE_TABLE`.status='pending'";
	if($sort != '') {
		$sql = $sql . " ORDER BY '$sort'";
	} //end if

	//Run the query
	$result = $getdonations->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "There are no donations for this group.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($donation_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$donations[$iCount] = $donation_info;
			$iCount++;
		} //end while

	} //end if

	//Close the DB connection
	$getdonations->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $donations;
	} //end if

}



/***************************************************************************************/
/* Function Name: GET_GROUP_DONATIONS */
/* Description: */
/***************************************************************************************/

function GET_GROUP_DONATIONS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE, $SEED_TABLE, $GROUP_TABLE;
	global $donation_info, $donations, $funds_available, $funds_shared;

	//Assign variables
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$getdonations = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdonations->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$FINANCE_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $getdonations->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "There are no donations for this group.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($donation_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$donations[$iCount] = $donation_info;
			$iCount++;
		} //end while

	} //end if

	//Close the DB connection
	$getdonations->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $donations;
	} //end if

}



/***************************************************************************************/
/* Function Name: GET_GROUP_FUNDS */
/* Description: */
/***************************************************************************************/

function GET_GROUP_FUNDS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $GROUP_TABLE;
	global $group_funds;

	//Assign variables
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$getdonations = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdonations->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";

	//Run the query
	$result = $getdonations->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "There are no funds listed for this group.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$group_funds['funds_available']	= $row['funds_available'];
		$group_funds['funds_shared']	= $row['funds_shared'];

	} //end if

	//Close the DB connection
	$getdonations->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $group_funds;
	} //end if

}


/***************************************************************************************/
/* Function Name: GET_USER_DONATIONS */
/* Description: */
/***************************************************************************************/

function GET_USER_DONATIONS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE;
	global $donation_info, $donations;

	//Assign variables
	$donations = array();
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$getdonations = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdonations->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$FINANCE_TABLE` WHERE userid='$uid'";

	//Run the query
	$result = $getdonations->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "There are no donations for this user.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($donation_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$donations[$iCount] = $donation_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getdonations->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $donations;
	} //end if

}


/***************************************************************************************/
/* Function Name: GET_USER_DONATION_TOTAL */
/* Description: Get the total donations the user has given to the group. */
/***************************************************************************************/

function GET_USER_DONATION_TOTAL ($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE;
	global $donation_total;

	//Assign variables
	$uid	= $cc_vars['user_id'];


	//Declare new database object with params
	$funds = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $funds->open();

	//Build a query to get the users' group donations.
	$sql = "";
	$sql = "SELECT amount FROM `$FINANCE_TABLE` WHERE userid='$uid' and fund='0001' and status='approved'";

	//Run the query
	$result = $funds->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No donations found.";
		$donation_total = '0';
	} else {
		$err_msg =  "success";

		//Total the donations
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			$donation_total = $donation_total + $row['amount'];
		} //end while
	} //end if


	//Close the DB connection
	$funds->close($db);

	//Return data
	return $donation_total;

}



/***************************************************************************************/
/* Function Name: GET_SEED_DONATIONS */
/* Description: */
/***************************************************************************************/

function GET_SEED_DONATIONS($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE;
	global $seed_info, $seed_donations;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];

	//Declare new database object with params
	$getdonations = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdonations->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$FINANCE_TABLE` WHERE fund='seed'";

	//Run the query
	$result = $getdonations->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "There are no seed donations in the database.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($seed_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$seed_donations[$iCount] = $seed_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getdonations->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $seed_donations;
	} //end if

}



/***************************************************************************************/
/* Function Name: ADD_DONATION */
/* Description: */
/***************************************************************************************/

function ADD_DONATION($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE;

	//Assign variables
	$uid	= $cc_vars['user_id'];
	$gid	= $cc_vars['group_id'];
	$status = 'pending';


	//Declare new database object with params
	$donation = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $donation->open();

	//Build a query to insert a new donation.
	$sql = "";
	$sql = "INSERT into `$FINANCE_TABLE` (`donationid`, `userid`, `groupid`, `fund`, `amount`, `date`, `type`, `reference`, `notes`, `status`) VALUES (NULL, '" . $uid . "', '" . $gid . "', '" . $cc_vars['donation_fund'] . "', '" . $cc_vars['donation_amount'] . "', '" . $cc_vars['donation_date'] . "', '" . $cc_vars['donation_type'] . "', '" . $cc_vars['donation_ref'] . "', '" . $cc_vars['donation_notes'] . "', '$status')";

	//Run the query
	$result = $donation->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to add the donation.";

	} else {
		$err_msg =  "The donation has been successfully added.";
	} //end if

	//Close the DB connection
	$donation->close($db);

	//Return errors
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: VIEW_DONATION */
/* Description: */
/***************************************************************************************/

function VIEW_DONATION($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE, $SEED_TABLE, $GROUP_TABLE;
	global $donation_info, $donations, $funds_available, $funds_shared;

	//Assign variables
	$donationid	= $cc_vars['donation_id'];

	//Declare new database object with params
	$getdonations = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getdonations->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "SELECT * FROM `$FINANCE_TABLE` WHERE donationid='$donationid'";

	//Run the query
	$result = $getdonations->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: could not find the specified donation";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$donation_info = mysql_fetch_array($result, MYSQL_BOTH); 

	} //end if

	//Close the DB connection
	$getdonations->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $donation_info;
	} //end if

}


/***************************************************************************************/
/* Function Name: DELETE_DONATION */
/* Description: */
/***************************************************************************************/

function DELETE_DONATION($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE;

	//Assign variables
	$donationid	= $cc_vars['donation_id'];

	//Declare new database object with params
	$donation = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $donation->open();

	//Build a query to delete the donation
	$sql = "";
	$sql = "DELETE FROM `$FINANCE_TABLE` WHERE donationid='$donationid'";

	//Run the query
	$result = $donation->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Unable to remove the specified donation!";
	} else {
		$err_msg =  "The donation was successfully deleted!";
	} //end if

	//Close the DB connection
	$donation->close($db);

	//Return any errors;
	return $err_msg;

}


/***************************************************************************************/
/* Function Name: UPDATE_DONATION */
/* Description: */
/***************************************************************************************/

function UPDATE_DONATION($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE;

	//Assign variables
	$donationid	= $cc_vars['donation_id'];

	//Declare new database object with params
	$donation = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $donation->open();

	//Build a query to update the donation information
	$sql = "";
	$sql = "UPDATE `$FINANCE_TABLE` SET userid='" . $cc_vars['user_id'] . "', groupid='" . $cc_vars['group_id'] . "', fund='" . $cc_vars['donation_fund'] . "', amount='" . $cc_vars['donation_amount'] . "', date='" . $cc_vars['donation_date'] . "', type='" . $cc_vars['donation_type'] . "', reference='" . $cc_vars['donation_ref'] . "', notes='" . $cc_vars['donation_notes'] . "'";
	$sql = $sql . " WHERE donationid='" . $donationid . "'";

	//Run the query
	$result = $donation->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Unable to update the specified donation!";
	} else {
		$err_msg =  "The donation was successfully updated!";
	} //end if

	//Close the DB connection
	$donation->close($db);

	//Return any errors;
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: APPROVE_DONATION */
/* Description: */
/***************************************************************************************/

function APPROVE_DONATION($cc_vars) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $FINANCE_TABLE, $GROUP_TABLE;

	//Assign variables
	$donationid	= $cc_vars['donation_id'];
	$status		= $cc_vars['donation_status'];
	$gid 		= '';

	//Declare new database object with params
	$donation = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $donation->open();

	//Build a query to update the donation information
	$sql = "";
	$sql = "UPDATE `$FINANCE_TABLE` SET status='$status'";
	$sql = $sql . " WHERE donationid='" . $donationid . "'";

	//Run the query
	$result = $donation->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	//Build a query to get the donation information
	$sql = "";
	$sql = "SELECT * FROM `$FINANCE_TABLE`";
	$sql = $sql . " WHERE donationid='" . $donationid . "'";

	//Run the query
	$result = $donation->query($sql);

	//Store the results in an array
	$row = mysql_fetch_array($result, MYSQL_BOTH);
	
	//Get the groupid
	$gid = $row['groupid'];

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Unable to update the specified donation!";
	} else {
		$err_msg =  "The donation was successfully updated!";

		if($status == 'approved') {
			//Build a query to get the curent funds available
			$sql = "";
			$sql = "SELECT * FROM `$GROUP_TABLE` WHERE groupid='$gid'";
	
			//Run the query
			$result = $donation->query($sql);
			
			//Store the results in an array
	  		$row = mysql_fetch_array($result, MYSQL_BOTH);
	
	
			//Update variables
			$funds = $row['funds_available'];
			$funds = $funds + $cc_vars['donation_amount'];

			//Build a query to update the group table
			$sql = "";
			$sql = "UPDATE `$GROUP_TABLE` SET funds_available='$funds'";
			$sql = $sql . " WHERE groupid='" . $gid . "'";

			//Run the query
			$result = $donation->query($sql);
		
			//Count the rows
			$num_rows = mysql_affected_rows();
		
			
			if ($num_rows < 1){
				//If no rows were affected, throw an error.
				$err_msg =  "Error: Unable to update the specified donation!";
			} else {
				$err_msg =  "The donation was successfully updated!";
			} //end if
		} //end if
	} //end if

	//Close the DB connection
	$donation->close($db);

	//Return any errors;
	return $err_msg;

}



/***************************************************************************************/
/* Function Name: REPORT_DONATIONS */
/* Description: */
/***************************************************************************************/

function REPORT_DONATIONS($cc_vars) {

}



?>