<?php
/*
* Description: This is a standard config file with defaults for
* database connectivity.
* Author: Isaac N. Hopper
* Created: 05-30-12
*/

//Standard Global Database Variables
$db_user	=	''; //The database user
$db_pass	=	''; //The database password
$db_host	=	''; //The path to the database
$database	=	''; //The database

//Environment
$home_url = '/dev/cc/'; //The base URL of the application
$thumb_dir = '/dev/cc/thumbs/'; //Thumbnail directory


//Constant declarations

/* Table Definitions */
$USER_TABLE			= 'CC_USERS';
$RECIPIENT_TABLE	= 'CC_RECIPIENTS';
$PERMS_TABLE		= 'CC_ROLES';
$FINANCE_TABLE		= 'CC_FINANCE';
$GROUP_TABLE		= 'CC_GROUPS';
$REQUEST_TABLE		= 'CC_REQUESTS';
$THREAD_TABLE		= 'CC_THREADS';
$VOTE_TABLE			= 'CC_VOTES';
$SEED_TABLE			= 'CC_SEED';
$INVITE_TABLE		= 'CC_INVITATIONS';
$JOIN_TABLE			= 'CC_JOIN';
$EMAIL_TABLE		= 'CC_MESSAGES';
$EVENTS_TABLE		= 'CC_EVENTS';
$SETTINGS_TABLE		= 'CC_SETTINGS';
$REQUEST_MATRIX		= 'CC_APPROVAL_MATRIX';

?>