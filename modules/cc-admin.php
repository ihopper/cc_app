<?php
/*
Application: Common Change
Module: Administrative Core
Filename: cc-admin.php
Version: 1.0
Description: This module manages user profiles and account activity, including account creation, suspension, and deletion.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 08/15/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
- GET_SETTINGS – Display the current application settings.- SET_SETTINGS – Update the application settings.- RESTORE_SETTINGS – Restore the system to the default settings.
- UPDATE_APPROVAL_MATRIX - Change the thread approval matrix settings.
- SET_USER_PERMISSIONS - Change permissions for individual users.

*/


/***************************************************************************************/
/* Includes */
/***************************************************************************************/
include_once 'cc-config.inc.php'; //Application Configuration File
include_once 'class.mysqldb.php';
include_once 'class.sendmail.php';
include_once 'cc-getrequest.php';


/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/





/***************************************************************************************/
/* Function Name: GET_SETTINGS */
/* Description: */
/***************************************************************************************/

function GET_SETTINGS ($cc_vars) {


}



/***************************************************************************************/
/* Function Name: SET_SETTINGS */
/* Description: */
/***************************************************************************************/

function SET_SETTINGS ($cc_vars) {


}



/***************************************************************************************/
/* Function Name: RESTORE_SETTINGS */
/* Description: */
/***************************************************************************************/

function RESTORE_SETTINGS ($cc_vars) {


}



/***************************************************************************************/
/* Function Name: SHOW_APPROVAL_MATRIX */
/* Description: */
/***************************************************************************************/

function SHOW_APPROVAL_MATRIX ($cc_vars) {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $REQUEST_MATRIX;
	global $matrix_row, $matrix;

	//Assign variables
	$gid	= $cc_vars['group_id'];


	//Declare new database object with params
	$getmatrix = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getmatrix->open();


	//Build a query to get the matrix data
	$sql = "";
	$sql = "SELECT * FROM `$REQUEST_MATRIX`";


	//Run the query
	$result = $getmatrix->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Group not found!";
	} else {

		//Store the results in an array
		$iCount = 0;
	  	while ($matrix_row = mysql_fetch_array($result, MYSQL_BOTH)) {
			$matrix[$iCount] = $matrix_row;
			$iCount++;
		} //end while


	} //end if

	//Close the DB connection
	$getmatrix->close($db);

	//Return the results
	return $matrix;


}



/***************************************************************************************/
/* Function Name: UPDATE_APPROVAL_MATRIX */
/* Description: */
/***************************************************************************************/

function UPDATE_APPROVAL_MATRIX ($cc_vars) {


}



/***************************************************************************************/
/* Function Name: ADD_MATRIX_ROW */
/* Description: */
/***************************************************************************************/

function ADD_MATRIX_ROW ($cc_vars) {


}



?>