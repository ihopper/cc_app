<?php
	//Mark the current tab as active
	echo '<script type="text/javascript">'
	   , 'document.getElementById("people").className = "menu-tab-on";'
	   , '</script>';

//Include modules
include_once 'modules/cc-people.php';
include_once 'modules/cc-email.php';

//Run the functions needed for this page.
//$cc_vars['sort'] = "lname";

GET_USERS($cc_vars);

if ($cc_vars['invitation_type'] == 'group') {
	//$message = SEND_CCINVITE($cc_vars, $invites);
	//$message = $err_msg;
}



?>

<script type='text/javascript'>
$(document).ready(function() {
	//Modal Invitation Form
	$("a[rel]").colorbox({inline:true, href:"#invitebox", rel:"nofollow", width: "500px", height: "200px", close: "Close X"});

});

function setFormValues(fname, lname, email) {
	//Populate the form fields
	$('#fname1').val(fname);
	$('#lname1').val(lname);
	$('#email1').val(email);
	$('#invited').html(fname+" "+lname);
};

function sendFormInvite() {
	//Close the modal
	$.colorbox.close();

	//Submit the form
	$('#frmInvite').ajaxForm( {	
		target: '#messages', 
		success: function(e) { 
			showNotification({
				message: "Your group invitation has been sent.",
				type: "warning",
				autoClose: true,
				duration: 3
			});
			//Reload the page on success after pausing for the message
			setTimeout(function() {
  				location.reload();
			}, 3000);
		},
		error:function (xhr, ajaxOptions, thrownError){
			showNotification({
				message: thrownError,
				type: "error",
				autoClose: true,
				duration: 2
			});
        }    

	}); 

};

function showMessage($message) {
	showNotification({
		message: $message,
		type: "warning",
		autoClose: true,
		duration: 3
	});
};


</script>

<div id="messages"></div>
<!-- yes/no dialog -->
<div style="display:none;">
<div id="invitebox" style="padding:10px; background:#fff;">
<center>
	<h2>Invite <span id="invited">this user</span> to your group?</h2>

	<form method="post" action="modules/cc-email.php" id="frmInvite">
		<input type="hidden" name="action" value="invite">
		<input type="hidden" name="invitation_type" value="group">
		<input id="fname1" type="hidden" name="fname1" value="">
		<input id="lname1" type="hidden" name="lname1" value="">
		<input id="email1" type="hidden" name="email1" value="">

		<div class="clear" style="height: 15px;"></div>
		<button type="submit" class="btn-green" onClick="sendFormInvite();"> Yes </button>
		<button type="button" class="btn-green close" onClick="$.colorbox.close();"> No </button>
	</form>
</center>
</div>
</div>
<!-- end modal -->

<div id="browse-people" class="text-small">
	<?php 
		if (empty($users)) {
			//Display message
			echo 'No users were found who matched your search. Please try again.';
		} else {
			//Return the results
			foreach ($users as $user) {
	?>
	<div class="browse-people-box">
		<div class="browse-people-thumb">
			<img src="<?php echo $thumb_dir . $user['thumb']; ?>" class="thumb-100x100 float-lt">
		</div><!-- .browse-people-thumb -->

		<div class="browse-people-social">
			<?php if ($user['share_fb']==1){; ?>
			<a href="<?php echo $user['facebook']; ?>" target="_blank"><button class="icon-fb-32x32" title="<?php echo $user['facebook']; ?>"></button></a><br />
			<?php } ?>
			<?php if ($user['share_twit']==1){; ?>
			<a href="http://www.twitter.com/<?php echo $user['twitter']; ?>" target="_blank"><button class="icon-twit-32x32" title="<?php echo $user['twitter']; ?>"></button></a><br />
			<?php } ?>
			<button class="icon-email-32x32" onClick="window.location.href='mailto:<?php echo $user['email']; ?>';" title="<?php echo $user['email']; ?>"></button><br />
		</div><!-- .browse-people-social -->

		<div class="clear" style="height: 4px;"></div>
		<div class="browse-people-details">
			<strong class="text-med"><?php echo $user['fname'] . " " . $user['lname']; ?></strong><br />
			<?php echo $user['city']; ?>, <?php echo $user['state']; ?>
			<br /><?php echo $user['country']; ?><br />
			<p><strong>Group: </strong>
				<?php if ($user['name'] != '') { echo $user['name']; } else { ?>
						<a href="#invitebox" rel="#overlay" onClick="setFormValues('<?php echo $user['fname']; ?>', '<?php echo $user['lname']; ?>', '<?php echo $user['email']; ?>');" class="text-tiny text-lime colorbox">invite</a> 
				<?php } ?>
			</p>
		</div>

	</div><!-- .browse-people-box -->
		<?php } /*end foreach*/ ?>
	<?php } /*end if*/ ?>	


</div><!-- .browse-people -->

<div class="clear" style="height: 50px;"></div>