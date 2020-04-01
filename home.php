<?php
//Mark the current tab as active
echo '<script type="text/javascript">'
   , 'document.getElementById("home").className = "menu-tab-on";'
   , '</script>';

//Include modules
include_once 'modules/cc-groups.php';
include_once 'modules/cc-activitystream.php';


//Set variables
$cc_vars['unique_id'] = $_SESSION['unique_id'];
$cc_vars['email'] = $_SESSION['email'];
$cc_vars['user_id'] = $_SESSION['user_id'];
$cc_vars['group_id'] = $_SESSION['group_id'];

//Call functions needed for this page
//If the user has accepted a group invitation, do it.
if ($action == 'accept_invitation') {
	$err_msg = ACCEPT_INVITE($cc_vars);
}

//If the user is not already in a group, get any group invitations.
if ($_SESSION['group_id'] == 0) {
	GET_INVITATIONS($cc_vars);
}

//If the user has clicked the registration confirmation link
if ($_SESSION['confirm']) {
	$cc_vars['confirm'] = $_SESSION['confirm'];
	$cc_vars['uniqueid'] = $_SESSION['uniqueid'];

	$user_confirmed = USER_CONFIRM ($cc_vars);
}

//Get the user profile
GET_PROFILE($cc_vars);
$_SESSION['group_id'] = $user_profile['groupid'];

?>

<script type="text/javascript">
$(document).ready(function() {
	//Modal Invitation Form
	$("a[rel]").colorbox({href:"invite.php", width: "900px", height: "500px", close: "Close X"});

});

function showMessage($message) {
	showNotification({
		message: $message,
		type: "warning",
		autoClose: true,
		duration: 3
	});
};

<?php 
	if ($action == 'accept_invitation') {
		echo "showMessage('" . $err_msg . "')";
	}
?>
</script>


<div class="clear" style="height: 30px;"></div>

<div id="messages"></div>

<div style="width: 350px; float: right; border: 1px solid #ccc; background-color: #EDF4F8; margin-left: 10px; padding: 10px 6px;">

	<center><h2>Welcome <?php echo $_SESSION['user_fullname']; ?></h2></center>
	<h3 class="text-normal">Here are a few simple steps to get you started with Common Change</h3>

	<?php if($user_confirmed) { echo "<script type='text/javascript'>showMessage('" . $user_confirmed . "');</script>"; } ?>
	<?php if($user_profile['email_confirmed'] != 'yes') { ?>
		<h3 style="color: red;">Please check your email and follow the instructions to confirm your account.</h>
	<?php } /*end if*/ ?>

	<?php if ($_SESSION['group_id'] == 0) { ?>
	<?php 
		//If the user has any group invitations, display them.
		if (empty($invites)) {
			//do nothing
		} else {
			//Output the results
			foreach($invites as $invite) {
	?>
	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="<? echo $_SESSION['home_url']; ?>?action=accept_invitation" class="text-med text-lime">Join <?php echo $invite['fname'] . ' ' . $invite['lname']; ?>'s Group</a><br />
		<span class="text-med">You can take a look at the group <a href="?tab=view_group&gid=<?php echo $invite['groupid']; ?>" class="text-lime">here</a> before you make your decision. <br />Users can only belong to one group.</span>
	</div>
			<?php } /*end foreach*/ ?>
		<?php } /*end if*/ ?>
	<?php } /*end if*/ ?>

	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="?tab=support" class="text-med text-lime">Support Common Change</a><br />
		<span class="text-med">How much is Common Change worth to you?</span>
	</div>

	<?php if ($_SESSION['group_id'] > 0) { ?>
	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="?tab=payment" class="text-med text-lime">Donate to Your Group Fund</a><br />
		<span class="text-med">Make funds avaulable to meet needs presented by your group.</span>
	</div>
	<?php } /*end if*/ ?>


	<?php if ($_SESSION['group_id'] > 0) { ?>
	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="?tab=mygroup" class="text-med text-lime">Visit Your Group Page</a><br />
		<span class="text-med">Weigh in on current needs or create your own.</span>
	</div>
	<?php } /*end if*/ ?>


	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="?tab=account" class="text-med text-lime">Update Your Profile</a><br />
		<span class="text-med">Setting your current information is neccesary in order to use many <br />Common Change features.</span>
	</div>

	<?php if ($_SESSION['group_id'] == 0) { ?>
	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="?tab=create_group" class="text-med text-lime">Create a Group</a><br />
		<span class="text-med">Create a group of your own and start inviting your friends.</span>
	</div>
	<?php } /*end if*/ ?>

	<div class="clear" style="height: 15px;"></div>
	<div class="float-lt" style="margin-left: 20px;">
		<a href="invite.php" class="text-med text-lime" rel="#overlay">Invite Friends</a><br />
		<span class="text-med">Invite your friends to experience Common Change.</span>
	</div>

<div class="clear" style="height: 20px;"></div>

</div>
<h2>Activity Stream</h2>
<?php GET_ACTIVITY($cc_vars); ?>

		<?php 
		//print_r($cc_activity);
			if (empty($cc_activity)) {
				//Display message
				echo 'You have no recent activity.';
			} else {
				//Sort the array
				function date_compare($a, $b)
				{
				    $t1 = strtotime($a['modified']);
				    $t2 = strtotime($b['modified']);
				    return $t1 - $t2;
				}    
				usort($cc_activity, 'date_compare');

				//Return the results using array_reverse to print the most recent items first
				$iRow = 1;
				foreach (array_reverse($cc_activity) as $activity) {
				?>
					<div class="activity">
					<a href="#" class="img">
						<img src="<?php echo $thumb_dir . $activity['thumb']; ?>" alt="" class="thumb-48x48" />
					</a>
					  <div class="bd text-med">
						<p class="text-lime"><a href="<?php echo $activity['link']; ?>"><?php echo $activity['title']; ?></a></p>
						<p><?php if($activity['category'] == 'Comment') { 
									echo '<i class="icon-comment-alt"></i>'; 
								} elseif ($activity['category'] == 'Request') {
									echo '<i class="icon-bullhorn"></i>';
								} elseif ($activity['category'] == 'Vote') {
									echo '<i class="icon-check"></i>';
								} elseif ($activity['category'] == 'Join Request') {
									echo '<i class="icon-group"></i>';
							} ?>
							<strong><?php echo $activity['category']; ?></strong> by <i class="icon-user"></i> <?php echo $activity['user']; ?> on <i class="icon-calendar"></i> <?php echo strftime('%b %d, %Y', strtotime($activity['modified'])); ?> at <?php echo strftime('%I:%M %p', strtotime($activity['modified'])); ?></p>
						<p><?php echo $activity['content']; ?></p>
					  </div>
					</div><!-- .activity -->
				<?php
					//echo $activity['title'] . "<br />" . $activity['link'] . "<br />" . $activity['content'] . "<br />" . $activity['modified'];
				}
			}
		?>		

</div>


</div>


<div class="clear" style="height: 50px;"></div>