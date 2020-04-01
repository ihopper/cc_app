<?php
/*
Application: Common Change
Module: Get Requests
Filename: cc-getrequest.php
Version: 1.0
Description: This module collects & assigns variables submitted through POST & GET request actions.

Author: Isaac N. Hopper
Author URI: www.isaachopper.com

Version History:
- 07/13/12 - Version 1 created.


Class Dependencies:
-None

Function Definitions:
-None
*/


/***************************************************************************************/
/* Variable Declarations */
/***************************************************************************************/

global $cc_vars;


/***************************************************************************************/
/* Variables passed from GET/POST */
/***************************************************************************************/

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
		case 'GET': $the_request = &$_GET; break;
		case 'POST': $the_request = &$_POST; break;
		default: $the_request = &$_POST; break;
	}
	
	//Get the page parameters and strip tags where necessary, then reassign to the variable array.
	$action			 		= strip_tags($the_request['action']); //update_acct, del_acct, new_acct, sus_acct.

	//General variables
	$cc_vars['id']	 		= strip_tags($the_request['id']);
	$cc_vars['user_id']	 	= strip_tags($the_request['user_id']);
	$cc_vars['username']	= strip_tags($the_request['username']);
	$cc_vars['password']	= strip_tags($the_request['password']);

	//Profile variables
	$cc_vars['fname']	 	= strip_tags($the_request['fname']);
	$cc_vars['lname']	 	= strip_tags($the_request['lname']);
	$cc_vars['email']	 	= strip_tags($the_request['email']);
	$cc_vars['phone']	 	= strip_tags($the_request['phone']);
	$cc_vars['address1'] 	= addslashes(strip_tags($the_request['address1']));
	$cc_vars['address2'] 	= addslashes(strip_tags($the_request['address2']));
	$cc_vars['city']	 	= addslashes(strip_tags($the_request['city']));
	$cc_vars['state']	 	= addslashes(strip_tags($the_request['state']));
	$cc_vars['zip']	 		= strip_tags($the_request['zip']);
	$cc_vars['country'] 	= addslashes(strip_tags($the_request['country']));
	$cc_vars['new_pass'] 	= addslashes(strip_tags($the_request['new_pass']));
	$cc_vars['conf_pass'] 	= addslashes(strip_tags($the_request['conf_pass']));
	$cc_vars['tos']	 		= strip_tags($the_request['tos']);
	$cc_vars['thumbnail'] 	= addslashes($the_request['thumbnail']); //Don't strip tags, since the directory structure may have some.
	$cc_vars['facebook'] 	= addslashes($the_request['facebook']); //Don't strip tags. Content is URL.
	$cc_vars['share_fb'] 	= strip_tags($the_request['share-fb']);
	$cc_vars['twitter'] 	= $the_request['twitter']; //Don't strip tags. Content is URL.
	$cc_vars['share_twit'] 	= strip_tags($the_request['share-twit']);
	$cc_vars['filter']	 	= addslashes(strip_tags($the_request['filter']));
	$cc_vars['comments']	= addslashes(strip_tags($the_request['comments']));
	

	//Group variables
	$cc_vars['group_id']	 	= strip_tags($the_request['group_id']);
	$cc_vars['group_name']	 	= addslashes(strip_tags($the_request['group-name']));
	$cc_vars['group_desc']		= addslashes(strip_tags($the_request['group-desc']));
	$cc_vars['group_covenant']	= addslashes(strip_tags($the_request['group-covenant']));
	$cc_vars['group_city']	 	= addslashes(strip_tags($the_request['group-city']));
	$cc_vars['group_state']	 	= addslashes(strip_tags($the_request['group-state']));
	$cc_vars['group_zip']	 	= addslashes(strip_tags($the_request['group-zip']));
	$cc_vars['group_country'] 	= addslashes(strip_tags($the_request['group-country']));
	$cc_vars['group_tags']	 	= addslashes(strip_tags($the_request['group-tags']));
	$cc_vars['group_thumb'] 	= addslashes(strip_tags($the_request['group-thumbnail']));
	$cc_vars['status']	 		= addslashes(strip_tags($the_request['status']));


	//Comment Thread Variables
	$cc_vars['thread_id']		= strip_tags($the_request['thread_id']);
	$cc_vars['thread_content']	= addslashes(strip_tags($the_request['thread_content']));
	$cc_vars['thread_category']	= addslashes(strip_tags($the_request['thread_category']));
	$cc_vars['childof']	 		= addslashes(strip_tags($the_request['thread-childof']));

	
	//Financial variables
	$cc_vars['donation_id']			= strip_tags($the_request['donation_id']);
	$cc_vars['donation_amount']		= strip_tags($the_request['donation_amount']);
	$cc_vars['donation_date']		= strip_tags($the_request['donation_date']);
	$cc_vars['donation_type']		= strip_tags($the_request['donation_type']);
	$cc_vars['donation_ref']		= strip_tags($the_request['donation_ref']);
	$cc_vars['donation_notes']		= strip_tags($the_request['donation_notes']);
	$cc_vars['donation_fund']		= strip_tags($the_request['donation_fund']);
	$cc_vars['donation_status']		= strip_tags($the_request['donation_status']);

	//Need Request Variables
	$cc_vars['request_id']		= strip_tags($the_request['rid']);


	//Email Message Variables
	$cc_vars['email_sender']		= addslashes(strip_tags($the_request['email_sender']));
	$cc_vars['invitation_type']		= addslashes(strip_tags($the_request['invitation_type']));

	$invites[0]['email_lname']			= addslashes(strip_tags($the_request['lname1']));
	$invites[0]['email_fname']			= addslashes(strip_tags($the_request['fname1']));
	$invites[0]['email_recipient']		= addslashes(strip_tags($the_request['email1']));	
	$invites[1]['email_lname']			= addslashes(strip_tags($the_request['lname2']));
	$invites[1]['email_fname']			= addslashes(strip_tags($the_request['fname2']));
	$invites[1]['email_recipient']		= addslashes(strip_tags($the_request['email2']));
	$invites[2]['email_lname']			= addslashes(strip_tags($the_request['lname3']));
	$invites[2]['email_fname']			= addslashes(strip_tags($the_request['fname3']));
	$invites[2]['email_recipient']		= addslashes(strip_tags($the_request['email3']));
	$invites[3]['email_lname']			= addslashes(strip_tags($the_request['lname4']));
	$invites[3]['email_fname']			= addslashes(strip_tags($the_request['fname4']));
	$invites[3]['email_recipient']		= addslashes(strip_tags($the_request['email4']));
	$invites[4]['email_lname']			= addslashes(strip_tags($the_request['lname5']));
	$invites[4]['email_fname']			= addslashes(strip_tags($the_request['fname5']));
	$invites[4]['email_recipient']		= addslashes(strip_tags($the_request['email5']));

	
	//Thread Matrix Variables
	


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
		case 'create_group': GROUP_ADD($cc_vars); break;
		case 'update_group': GROUP_UPDATE($cc_vars); break;
		case 'invite': SEND_CCINVITE($cc_vars, $invites); break;
		case 'join_group': GROUP_JOIN($cc_vars); break;
		case 'suspend_group': GROUP_SUSPEND($cc_vars); break;
		case 'delete_group': GROUP_DELETE($cc_vars); break;
		case 'approve_donation': APPROVE_DONATION($cc_vars); break;
		case 'create_thread': CREATE_THREAD($cc_vars); break;
		case 'update_matrix': UPDATE_APPROVAL_MATRIX($cc_vars); break;
	} //end switch
} //end if-else



?>