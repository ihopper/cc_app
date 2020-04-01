<?php
	//Maintain session
	session_start();	

	//Includes
	include("functions.php");
	global $home_url;

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
	case 'GET': $the_request = &$_GET; break;
	case 'POST': $the_request = &$_POST; break;
	default: $the_request = &$_POST; break;
	}

	//Get the page parameters passed through GET
	$tab = strip_tags($the_request['tab']);
	$action = strip_tags($the_request['action']);
	$page = strip_tags($the_request['page']);
	$message = $_SESSION['msg'];
	
	//Sort & Search for Groups & People
	$cc_vars['search'] = strip_tags($the_request['search']);
	$cc_vars['sort'] = strip_tags($the_request['sort']);
	$sort_type = $cc_vars['sort'];
	$sort_page = strip_tags($the_request['page']);
	if($cc_vars['sort'] == 'default' && $sort_page == 'groups') {
		$cc_vars['sort'] = 'name';
	} else if($cc_vars['sort'] == '' && $tab == 'people'){
		$cc_vars['sort'] = 'group_members';
	} else if($cc_vars['sort'] == 'state' && $sort_page == 'groups'){
		$cc_vars['sort'] = '`CC_GROUPS`.' . $cc_vars['sort'];
		$sort_type = 'state';
	} //end if

	$search = strip_tags($the_request['search']);
	if($search != '' && $tab == 'people') {
		$cc_vars['sort'] = 'lname';
	} //end if


	//Registration Confirmation
	if($the_request['confirm']) {
		$_SESSION['confirm'] 	= strip_tags($the_request['confirm']);
		$_SESSION['uniqueid']	= strip_tags($the_request['r']);	
	} //end if

	//Miscellaneous
	$request_id 	= strip_tags($the_request['rid']);
	$join_id		= strip_tags($the_request['jid']);

	//Variables for group invitations on the people page
	$cc_vars['invitation_type']		= addslashes(strip_tags($the_request['invitation_type']));
	$invites[0]['email_lname']		= addslashes(strip_tags($the_request['lname']));
	$invites[0]['email_fname']		= addslashes(strip_tags($the_request['fname']));
	$invites[0]['email_recipient']	= addslashes(strip_tags($the_request['email']));

	//Check authentication & permissions
	$valid 		= 'true'; //$_SESSION['valid']; //is user validated? true or false
	$access_level	= $_SESSION['access_level']; //user access level
	
	//Initialize application settings, if needed
	//Do not run if the user is not yet authorized to view the page.
	if ($access_level > 0) {
		cc_init();
	} //end if


	//Import the header file
	include("header.php");



	//Configure the display
	$menuTabClass = 'menu-tab-off'; //Set the default style for tabs
	if ($valid == 'true' && $access_level > 0) {
		if ($tab == '') {
			$ui	=	'home'; //Set this as a parameter within the administrative settings db, i.e. $CC_PAGE_DEFAULT
		} elseif ($tab=='login') {
			$ui =	'login';
		} elseif ($tab=='logout') {
			$ui =	'logout';
		} elseif ($tab=='register') {
			$ui =	'register';
		} elseif ($tab=='groups') {
			$ui =	'groups';
		} elseif ($tab=='view_group') {
			$ui =	'view_group';
		} elseif ($tab=='people') {
			$ui =	'people';
		} elseif ($tab=='account') {
			$ui =	'account';
		} elseif ($tab=='mygroup') {
			$ui =	'mygroup';
		} elseif ($tab=='thread') {
			$ui =	'thread';
		} elseif ($tab=='create_group') {
			$ui =	'create_group';
		} elseif ($tab=='create_thread') {
			$ui =	'create_thread';
		} elseif ($tab=='create_request') {
			$ui =	'create_request';
		} elseif ($tab=='view_request') {
			$ui =	'view_request';
		} elseif ($tab=='edit_request') {
			$ui =	'edit_request';
		} elseif ($tab=='create_recipient') {
			$ui =	'create_recipient';
		} elseif ($tab=='invite') {
			$ui =	'invite';
		} elseif ($tab=='about') {
			$ui =	'about';
		} elseif ($tab=='gethelp') {
			$ui =	'contact';
		} elseif ($tab=='payment') {
			$ui =	'payment';
		} elseif ($tab=='donation') {
			$ui =	'donation';
		} elseif ($tab=='donation_confirm') {
			$ui =	'donation_confirm';
		} elseif ($tab=='support') {
			$ui =	'support';
		} elseif ($tab=='admin') {
			$ui =	'admin-dashboard';

			if ($page=='home') {
				$ui =	'admin-dashboard';
			} elseif ($page=='finance') {
				$ui =	'admin-finance';
			} elseif ($page=='groups') {
				$ui =	'admin-groups';
			} elseif ($page=='threads') {
				$ui =	'admin-threads';
			} elseif ($page=='email') {
				$ui =	'admin-email';
			} elseif ($page=='edit_email') {
				$ui =	'admin-edit-email';
			} elseif ($page=='gen') {
				$ui =	'admin-general';
			} elseif ($page=='prefs') {
				$ui =	'admin-prefs';
			}
		} else {
			$s_error = '';
		}
	} else {
		//If the user is not validated, take them to the login prompt
		echo "<script type='text/javascript'>window.location.href='" . $home_url . "login.php'; </script>";
	}
?>

<script type='text/javascript'>
function doSort(obj, page){
	$sort = obj;
	window.location.href='?tab=' + page + '&sort=' + $sort + '&page=' + page;
};

function doSearch(page){
	if(page == 'groups') {
		$terms = $('#browse-search1').val();
	} else if(page == 'people') {
		$terms = $('#browse-search2').val();
	};
	window.location.href='?tab=' + page + '&search=' + $terms;
};
</script>

<body>

<div id="bg">
	<div id="header">
		<div id="logo"></div><!-- .logo -->
		<div id="menu-welcome"><span class="text-small"><?php echo $_SESSION['user_fullname'] ?></span> | <a href="?tab=logout" class="link-account text-small">Sign Out</a>
		</div><!-- .menu-welcome -->

		<div class="clear"></div>
		<div id="menu-wrapper">
			<div id="menu">
				<li class="menu-tab-off" id="home" onClick="window.location.href='<? echo $_SESSION['home_url']; ?>'"><div style="margin-top: 10px;"><i class="icon-home"></i> Home</div></li>
				<?php if ($_SESSION['group_id'] > 0) { ?>
				<li class="menu-tab-off" id="mygroup" onClick="window.location.href='?tab=mygroup'"><div style="margin-top: 10px;"><i class="icon-group"></i> My Group</div></li>
				<?php } ?>
				<li class="menu-tab-off" id="groups" onClick="window.location.href='?tab=groups'"><div style="margin-top: 10px;"><i class="icon-globe"></i> Groups</div></li>
				<li class="menu-tab-off" id="people" onClick="window.location.href='?tab=people'"><div style="margin-top: 10px;"><i class="icon-user"></i> People</div></li>
				<li class="menu-tab-off" id="account" onClick="window.location.href='?tab=account'"><div style="margin-top: 10px;"><i class="icon-cog"></i> My Account</div></li>
				<li class="menu-tab-off" id="gethelp" onClick="window.location.href='?tab=gethelp'"><div style="margin-top: 10px;"><i class="icon-question-sign"></i> Get Help</div></li>
				<?php if ($access_level == 10) { ?>
					<li class="menu-tab-off	" id="admin" onClick="window.location.href='?tab=admin'"><div style="margin-top: 10px;"><i class="icon-wrench"></i> Admin</div></li>
				<?php } ?>
			</div><!-- .menu -->
		</div><!-- .menu-wrapper -->
		<div class="clear"></div>
	</div><!-- .header -->

<?php if ($tab=='groups') { ?>
<div id="browse-nav">
	<div class="clear" style="height: 10px;"></div>
	<div style="width: 800px; margin: 0px auto;">
		<div class="float-lt">
			<label for "browse-sortby1" class="text-med" style="width: 30px; line-height: 20px;">Sort:</label>
			<select id="browse-sortby1" name="browse-sortby1" class="input-125 input-dark float-lt" onChange="doSort(this.value, 'groups');">
				<option value="name">Default</option>
				<option value="name">Alphabetic (a-z)</option>
				<option value="name DESC">Alphabetic (z-a)</option>
				<option value="state">By State</option>
			</select>
		</div><!-- .float-lt -->

		<div class="float-lt" style="width: 200px; margin-left: 130px;">
			<?php if ($_SESSION['group_id'] == 0) { ?>
				<center><button type="button" class="btn-grey2" onClick="window.location.href='?tab=create_group';">Create a Group</buttton></center>
			<?php } ?>
		</div><!-- .float-lt -->

		<div class="float-rt" style="margin-right: 30px;">
			<label for "browse-search1" class="text-med" style="width: 50px; line-height: 22px;"">Search:</label>
			<input type="text" id="browse-search1" name="browse-search1" class="input-125 input-dark browse-search"></input>
			<button type="button" class="btn-grey2" onClick="doSearch('groups');">go</button>
		</div><!-- .float-rt -->
	</div>
</div><!-- .browse-nav -->
<?php } ?>

<?php if ($tab=='people') { ?>
<div id="browse-nav">
	<div class="clear" style="height: 10px;"></div>
	<div style="width: 800px; margin: 0px auto;">
		<div class="float-lt">
			<label for "browse-sortby2" class="text-med label" style="width: 30px; line-height: 20px;">Sort:</label>
			<select id="browse-sortby2" name="browse-sortby2" class="input-125 input-dark float-lt" onChange="doSort(this.value, 'people');">
				<option value="">Default</option>
				<option value="group_members">Group Members</option>
				<option value="lname">By Last Name (A-Z)</option>
				<option value="lname DESC">By Last Name (Z-A)</option>
				<option value="state">By State</option>
			</select>
		</div><!-- .float-lt -->

		<div class="float-lt" style="width: 200px; margin-left: 130px;">
			<?php if ($_SESSION['group_id'] == 0) { ?>
				<center><button type="button" class="btn-grey2" onClick="window.location.href='?tab=create_group';">Create a Group</buttton></center>
			<?php } ?>
		</div><!-- .float-lt -->

		<div class="float-rt" style="margin-right: 30px;">
			<label for "browse-search2" class="text-med" style="width: 50px; line-height: 22px;">Search:</label>
			<input type="text" id="browse-search2" name="browse-search2" class="input-125 input-dark browse-search"></input>
			<button type="button" class="btn-grey2" onClick="doSearch('people');">go</button>
		</div><!-- .float-rt -->
	</div>
</div><!-- .browse-nav -->
<?php } ?>

<?php /* Generate the admin panel if the tab is clicked */ ?>
<?php if ($tab=='admin') { ?>
<script type="text/javascript">
	$("body").addClass("adminbg");
</script>
<div id="admin-wrapper">
	<div id="admin-nav">
		<php? //Import the administrative menu ?>
		<?php include("admin-menu.php"); ?>
	</div><!-- .admin-nav -->

	<div id="admin-panel">
		<php? //Import the selected user interface ?>
		<?php include("$ui.php"); ?>
	</div><!-- .admin-panel -->	
	<div class="clear"></div>
</div><!-- .admin-wrapper --> 
<div class="clear"></div>

<?php } else { ?>
	<div id="container">
		<php? /* Import the selected user interface */ ?>
		<?php include("$ui.php"); ?>
	</div><!-- .container --> 
<?php } /* end if */ ?>

</div><!-- .bg -->

<?php //Import the footer file ?>
<?php include("footer.php"); ?>

<script type='text/javascript'>
	<?php echo "$('#browse-sortby1').val('" . $sort_type . "');"; ?>
	<?php echo "$('#browse-sortby2').val('" . $sort_type . "');"; ?>
</script>

</body>
</html>