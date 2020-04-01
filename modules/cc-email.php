<?php
/*
Application: Common Change
Module: User Profile
Filename: cc-email.php
Version: 1.0
Description: This module manages application email templates and settings.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 08/20/12 - Version 1 created.


Class Dependencies:
- MySqlDB Class – Database connectivity class.- SendMail Class – Provides email functionality.

Function Definitions:
•	GET_MESSAGE – Fetches a specified message template.•	CREATE_MESSAGE – Create a new message template. Messages must be saved as a template before scheduling an event.•	EDIT_MESSAGE – Edit /update a message template.•	DELETE_MESSAGE – Delete a message template. This will also delete any scheduled events associated with the message.•	CREATE_EVENT – Add a new scheduled event.•	EDIT_EVENT – Edit / update a scheduled event.•	DELETE_EVENT – Delete a scheduled event. This will not delete the associated message template.•	SEND_NOW – Send a message now, without scheduling, and save details to the database.•	LIST_MESSAGES – View a list of available message templates along with previously sent messages using the SEND_NOW function

*/

//Maintain session state
session_start();

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
/* Function Name: GET_MESSAGE */
/* Description: */
/***************************************************************************************/

function GET_MESSAGE () {
	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EMAIL_TABLE;
	global $message_info;

	//Assign variables
	$mid	= $cc_vars['message_id'];


	//Declare new database object with params
	$getmessage = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getmessage->open();


	//Build a query to pull group information
	$sql = "";
	$sql = "SELECT * FROM `$EMAIL_TABLE` WHERE groupid='$gid'";


	//Run the query
	$result = $getmessage->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: Group not found!";
	} else {
		$err_msg =  "success";

		//Store the results in an array
	  	$message_info = mysql_fetch_array($result, MYSQL_BOTH);

	} //end if

	//Close the DB connection
	$getmessage->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $message_info;
	} //end if




}



/***************************************************************************************/
/* Function Name: CREATE_MESSAGE */
/* Description: */
/***************************************************************************************/

function CREATE_MESSAGE () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EMAIL_TABLE;

	//Assign variables
	$created	= ''; //Date-Time stamp

	//Declare new database object with params
	$message = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $message->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$EMAIL_TABLE` (`messageid`, `subject`, `sender`, `replyto`, `headers`, `content`) VALUES (NULL, '" . $cc_vars['subject'] . "', '" . $cc_vars['sender'] . "', '" . $cc_vars['replyto'] . "', '" . $cc_vars['headers'] . "', '" . $cc_vars['content'] . "');";

	//Run the query
	$result = $message->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$message->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to save the message template.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The message template has been saved successfully.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if



}




/***************************************************************************************/
/* Function Name: EDIT_MESSAGE */
/* Description: */
/***************************************************************************************/

function EDIT_MESSAGE () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EMAIL_TABLE;

	//Assign variables
	$mid	= $cc_vars['message_id'];
	$modified	= ''; //Date-Time stamp

	//Declare new database object with params
	$message = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $message->open();

	//Build a query to update the recipient specified.
	$sql = "";
	$sql = "UPDATE `$EMAIL_TABLE` SET subject='" . $cc_vars['subject'] . "', sender='" . $cc_vars['sender'] . "', replyto='" . $cc_vars['replyto'] . "', headers='" . $cc_vars['headers'] . "', content='" . $cc_vars['content'] . "'";
	//Run the query
	$result = $message->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$message->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to update the message template.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The message template has been successfully updated.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if


}



/***************************************************************************************/
/* Function Name: DELETE_MESSAGE */
/* Description: */
/***************************************************************************************/

function DELETE_MESSAGE () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EMAIL_TABLE;

	//Assign variables
	$mid		= $cc_vars['message_id'];

	//Declare new database object with params
	$message = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $message->open();


	//Build a query to delete the comment thread.
	$sql = "";
	$sql = "DELETE FROM `$EMAIL_TABLE` WHERE messageid='$mid'";

	//Run the query
	$result = $thread->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: could not delete the message template!";
	} else {
		$err_msg =  "The message template was successfully deleted!";
	} //end if	


	//Close the DB connection
	$message->close($db);

	//Return any errors;
	return $err_msg;

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

}



/***************************************************************************************/
/* Function Name: CREATE_EVENT */
/* Description: */
/***************************************************************************************/

function CREATE_EVENT () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EVENTS_TABLE;

	//Assign variables
	$mid		= $cc_vars['message_id'];
	$created	= ''; //Date-Time stamp

	//Declare new database object with params
	$event = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $event->open();

	//Build a query to insert a new request into the group queu.
	$sql = "";
	$sql = "INSERT into `$EVENTS_TABLE` (`eventid`, `messageid`, `scheduled`) VALUES (NULL, '" . $mid . "', '" . $cc_vars['scheduled'] . "');";

	//Run the query
	$result = $event->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$event->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to save the event.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The event has been saved successfully.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if


}



/***************************************************************************************/
/* Function Name: EDIT_EVENT */
/* Description: */
/***************************************************************************************/

function EDIT_EVENT () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EVENTS_TABLE;

	//Assign variables
	$eid	= $cc_vars['event_id'];

	//Declare new database object with params
	$event = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $event->open();

	//Build a query to update the recipient specified.
	$sql = "";
	$sql = "UPDATE `$EMAIL_TABLE` SET schedule='" . $cc_vars['schedule'] . "'";

	//Run the query
	$result = $event->query($sql);

	//Store the results in an array
  	$row = mysql_fetch_array($result, MYSQL_BOTH);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	//Close the DB connection
	$event->close($db);

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: unable to update the scheduled event.";

		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		$err_msg =  "The scheduled event has been successfully updated.";

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} //end if


}



/***************************************************************************************/
/* Function Name: DELETE_EVENT */
/* Description: */
/***************************************************************************************/

function DELETE_EVENT () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EVENTS_TABLE;

	//Assign variables
	$eid		= $cc_vars['event_id'];

	//Declare new database object with params
	$event = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $event->open();


	//Build a query to delete the comment thread.
	$sql = "";
	$sql = "DELETE FROM `$EVENTS_TABLE` WHERE eventid='$eid'";

	//Run the query
	$result = $event->query($sql);

	//Count the rows
	$num_rows = mysql_affected_rows();

	
	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "Error: could not delete the message template!";
	} else {
		$err_msg =  "The message template was successfully deleted!";
	} //end if	


	//Close the DB connection
	$event->close($db);

	//Return any errors;
	return $err_msg;

	//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
	$_SESSION['msg'] = $err_msg;

}



/***************************************************************************************/
/* Function Name: SEND_NOW */
/* Description: */
/***************************************************************************************/

function SEND_NOW () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EMAIL_TABLE;

	//Assign variables.
	$mid = $cc_vars['message_id'];

	//Declare new database object with params
	$message = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $message->open();

	//Check to see if there is a user with the username provided.
	//Build the query.
	$sql = "";
	$sql = "SELECT * from `EMAILS_TABLE` WHERE messageid='$mid'";

	//Run the query
	$result = $message->query($sql);
	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1) { 
		//If the user doesn't exist send an error message.
		$err_msg = "Error: the username you entered was not found.";
	} else {
		//Get the user data.
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		$mSubject = $row['subject'];
		$mSender = $row['sender'];
		$mReplyTo = $row['replyto'];
		$mRecipient = $cc_vars['recipient']; //passed from query.
		$mCC = '';
		$mBCC = '';
		$mBody = $row['content'];
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
	$message->close($db);

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;


}



/***************************************************************************************/
/* Function Name: LIST_MESSAGES */
/* Description: */
/***************************************************************************************/

function LIST_MESSAGES () {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $EMAIL_TABLE, $EVENTS_TABLE;
	global $message_info, $messages;

	//Assign variables
	$sort	= $cc_vars['sort'];


	//Declare new database object with params
	$getmessages = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $getmessages->open();

	//Build a query to list users, with sort option. The left join looks for matching user data in the groups table.
	//If the user does not belong to a group the user information is still produced, minus group info.
	$sql = "";
	$sql = "SELECT `$EMAIL_TABLE`.*, `$EVENTS_TABLE`.scheduled";
	$sql = $sql . " LEFT JOIN `$EVENTS_TABLE`";
	$sql = $sql . " ON $EMAIL_TABLE.messageid=$EVENTS_TABLE.messageid";
	$sql = $sql . " ORDER BY `$EMAIL_TABLE`.$sort"; //Sort order

	//Run the query
	$result = $getmessages->query($sql);

	
	//Count the rows
	$num_rows = mysql_affected_rows();

	if ($num_rows < 1){
		//If no rows were affected, throw an error.
		$err_msg =  "No message templates found.";
	} else {
		$err_msg =  "success";

		//Store the results in an array
		$iCount = 0;
	  	while ($message_info = mysql_fetch_array($result, MYSQL_BOTH)) {
			$messages[$iCount] = $message_info;
			$iCount++;
		} //end while
	} //end if

	//Close the DB connection
	$getmessages->close($db);

	//Return data
	if ($num_rows < 1) {
		//Return errors
		return $err_msg;

		//Set an error message as a session variable, so that we can maintain it accross pages without POST or GET actions.
		$_SESSION['msg'] = $err_msg;
	} else {
		//Return the array
		return $messages;
	} //end if


}


/***************************************************************************************/
/* Function Name: SEND_CCINVITE */
/* Description: */
/***************************************************************************************/

function SEND_CCINVITE ($cc_vars, $invites) {

	//Provide access to our global variables in the config file.
	global $db_user, $db_pass, $db_host, $database, $sql, $err_msg, $num_rows, $result;
	global $SETTINGS_TABLE, $INVITE_TABLE;

	//Declare new database object with params
	$message = new MySqlDB($db_user, $db_pass, $db_host, $database);

	//Open db connection and capture the resource link as $db
	$db = $message->open();


	//Assign variables
	$invitation_type = $cc_vars['invitation_type']; //Options are cc and group
	
	//Check to see if there is a user with the username provided.
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
		//Get the message data.
		$row = mysql_fetch_array($result, MYSQL_BOTH);

		//Loop through the invitees	
		foreach ($invites as $invited) {
			if($invited['email_recipient'] != '') {
				//Assign Variables
				$recipient_name 	= $invited['email_fname'] . " " . $invited['email_lname'];
				$recipient_email 	= $invited['email_recipient'];
				$sender_name 		= $_SESSION['user_fullname'];
				$sender_email 		= $cc_vars['email_sender'];
				$unique_id			= mt_rand();
		
		
				$mSubject = "Invitation to Join Common Change";
				$mSender = "CommonChange@commonchange.com";
				$mReplyTo = "donotreply@commonchange.com";
				$mRecipient = $recipient_email;
				$mCC = '';
				$mBCC = '';
				if ($invitation_type == 'cc') {
					$mBody = $output = sprintf($row['email_invite'], $recipient_name, $sender_name, $recipient_email, $unique_id,  $invited['email_fname'],  $invited['email_lname']);
				} else if($invitation_type == 'group') {
					$mBody = $output = sprintf($row['email_groupinvite'], $recipient_name, $sender_name, $recipient_email, $unique_id);
				} //end if
				$mRedirect = '';
		
				//If everything looks OK, then send the email.
				$mail_sent = new sendmail($mSubject, $mSender, $mReplyTo, $mRecipient, $mCC, $mBCC, $mBody, $mRedirect);
					
				//Test for success.
				if ($mail_sent) {
					$err_msg = "The email message was sent successfully.";

					/***** Check to see if the invitation is to Common Change or to a group *****/
					if ($invitation_type == 'group') {
						//Add the invitation to the invites table.
						$sql = "INSERT INTO `$INVITE_TABLE` (`invitationid`, `email`, `unique_id`, `groupid`, `ownerid`, `response`) VALUES (NULL, '" . $recipient_email . "', '" . $unique_id . "', '" . $_SESSION['group_id'] . "', '" . $_SESSION['user_id'] . "', NULL);";
					
						//Run the query
						$result = $message->query($sql);
						
						//Count the rows
						$num_rows = mysql_affected_rows();
					
						if ($num_rows < 1) { 
							//If the user doesn't exist send an error message.
							$err_msg = "Error: unable to add the invitation.";
						} else {
							$err_msg = "The invitations was successfully added.";
						} // end if
					} //end if
				} else {
					//If the sendmail function failed.
					$err_msg = "Error: We were unable to send your email message. Please contact the system administrator for further assistance.";
	
					//Run the query
					$result = $message->query($sql);
					
					//Count the rows
					$num_rows = mysql_affected_rows();
				
					if ($num_rows < 1) { 
						//If the user doesn't exist send an error message.
						$err_msg = "Error: unable to add the group invitation.";
					} //end if
				} //end if
			} //end if
		} //end foreach
	} //end if
	
	//Close the DB connection
	$message->close($db);

	//Return any errors;
	$_SESSION['err_msg'] = $err_msg;
	return $err_msg;

}


?>