<?php

//Mark the current tab as active
echo '<script type="text/javascript">'
   , 'document.getElementById("account").className = "menu-tab-on";'
   , '</script>';


//Include modules
include_once 'modules/cc-profiles.php';
include_once 'modules/cc-accounts.php';
include_once 'modules/cc-financial.php';
include_once 'modules/cc-groups.php';

//Run the functions needed for this page.
$cc_vars['id']=$_SESSION['user_id'];
$cc_vars['user_id']=$_SESSION['user_id'];
$cc_vars['group_id']=$_SESSION['group_id'];
$cc_vars['email'] = $_SESSION['email'];
$cc_vars['join_id'] = $join_id;

//If the user has accepted a group invitation, do it.
if ($action == 'accept_invitation') {
	ACCEPT_INVITE($cc_vars);
	$message = $err_msg;
}

//If the user has rejected a group invitation, do it.
if ($action == 'reject_invitation') {
	REJECT_INVITE($cc_vars);
	$message = $err_msg;
}

//If the user has accepted a group request, do it.
if ($action == 'accept_joinrequest') {
	ACCEPT_JOIN($cc_vars);
	$message = $err_msg;
}

//If the user has rejected a group request, do it.
if ($action == 'reject_joinrequest') {
	REJECT_JOIN($cc_vars);
	$message = $err_msg;
}


GET_PROFILE($cc_vars);
GET_GROUP($cc_vars);
GET_REQUESTS($cc_vars);
//If the user is not already in a group, get any group invitations.
if ($_SESSION['group_id'] == 0) {
	GET_INVITATIONS($cc_vars);
}
GET_USER_DONATIONS($cc_vars);
GET_MEMBERS($cc_vars);
GET_MEMBER_REQUESTS($cc_vars);

/* Store some of the information for later. */
$cc_vars['user_id'] = $_SESSION['user_id'];
$_SESSION['group_id'] = $user_profile['groupid'];

//Get donation total.
$donation_total = GET_USER_DONATION_TOTAL($cc_vars);

//Is the user a group owner or admin
if ($user_profile['group_role'] == 'Owner' || $user_profile['group_role'] == 'Administrator') {
	$owneradmin = true;
}

?>
<script type="text/javascript">
$(document).ready(function() {
	$('#tbl-group-requests').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });
	$('#tbl-group-invites').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });
	$('#tbl-need-requests').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });
	$('#tbl-acct-fin').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });

	//Hide account page tab sections
  	setAcctTab('acct-det'); 

	//Warn the user before leaving the page
	$(window).bind('beforeunload', function(){
	  //return 'If you leave the page all unsaved data will be lost.';
	});

	//Generate the slider control
	$( "#slider" ).slider({
			value:20,
			min: 0,
			max: 100,
			step: 5,
			slide: function( event, ui ) {
				$( "#amount" ).val( "$" + ui.value );
				smile(ui.value);
			},
			change: function(event, ui) {
				//smile(ui.value);
            }
		});
		$( "#amount" ).val( "$" + $( "#slider" ).slider( "value" ) );

	//Tooltips
	$(".tip").tooltip();
	    
    // Validate account update form
    $("#frmAcctUpdate").validate({
  		rules: {
    		conf_pass: {
      		equalTo: "#new_pass"
    		}
		},
   		messages: {
     		"fname": "Please enter a group name.",
			"lname": "Please enter a group description.",
     		"email": {
       			required: "Required",
       			email: "Please use the format name@domain.com."
     		},
			"address1": "Please enter an address",
			"city": "Please enter a city.",
			"state": "Please select a state.",
			"country": "Please select a country."
		},
		errorElement: "div"
	});

	//Validate group update form
	$("#frmGroupUpdate").validate();


	//Modal Invitation Form
	$("a[rel]").overlay({
	
		// some mask tweaks suitable for modal dialogs
		mask: {
			color: '#ffffff',
			loadSpeed: 200,
			opacity: 0.9
		},

       onBeforeLoad: function() {
 
            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");
 
            // load the page specified in the trigger
            wrap.load(this.getTrigger().attr("href"));
        },
		
		closeOnClick: true,
		api:true
	});

});

//AJAX Form Submissions
function sendFormGrp() {
	$('#frmGroupUpdate').ajaxForm( {
		target: '#messages', 
		success: function() { 
			showNotification({
				message: "Group profile updated successfully.",
				type: "warning",
				autoClose: true,
				duration: 8
			});
		} 
	}); 

};

function sendFormAcct() {
	$('#frmAcctUpdate').ajaxForm( {
		target: '#messages', 
		success: function(data) { 
			showNotification({
				message: "User profile updated successfully.",
				type: "warning",
				autoClose: true,
				duration: 8
			});
			//Update the username in the header with a refresh
			//window.location.href='?tab=account';
		},
		error:function (xhr, ajaxOptions, thrownError){
			showNotification({
				message: thrownError,
				type: "error",
				autoClose: true,
				duration: 8
			});
        }    
	}); 

};

function sendFormUpload() {
	$('#user-thumb').attr("src", 'images/loading.gif');
	$('#frmUpload').ajaxForm( {
		target: '#messages', 
		success: function() { 
			var newfile = '<?php echo $thumb_dir ?>';
			//newfile = newfile+document.getElementById('user-file').value;
			newfile = newfile+document.getElementById('user-file').value.replace("C:\\fakepath\\", "");
			$('#user-thumb').attr("src", newfile);
			//getFilename();
			showNotification({
				message: "Profile image updated successfully.",
				type: "warning",
				autoClose: true,
				duration: 5
			});

		} 
	}); 

};

function sendFormUploadGrp() {
	$('#group-thumb').attr("src", 'images/loading.gif');
	$('#frmUploadGrp').ajaxForm( {
		target: '#messages', 
		success: function() { 
			var newfile = '<?php echo $thumb_dir ?>';
			newfile = newfile+document.getElementById('group-file').value;
			$('#group-thumb').attr("src", newfile);
			//getFilename();
			showNotification({
				message: "Group image updated successfully.",
				type: "warning",
				autoClose: true,
				duration: 5
			});

		} 
	}); 

};

function getFilename() {
	var fullPath = document.getElementById('#user-thumb').value;
	if (fullPath) {
	        var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
	        var filename = fullPath.substring(startIndex);
	        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
	                filename = filename.substring(1);
	        }
	        alert(filename);
	}
};


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
		echo "showMessage('" . $message . "')";
	} else if ($action == 'reject_invitation') {
		echo "showMessage('" . $message . "')";		
	} else if ($action == 'accept_joinrequest') {
		echo "showMessage('" . $message . "')";	
	} else if ($action == 'reject_joinrequest') {
		echo "showMessage('" . $message . "')";	
	} //end if
?>

function supportCC() {
	//Set the amount
	don_amt = $('#amount').val();

	//Strip the dollar sign
	don_amt = don_amt.substring(1);

	//Redirect
	window.location.href='?tab=payment&amount='+don_amt;
};

</script>


<div class="modalinvite" id="overlay">
  <!-- the external content is loaded inside this tag -->
  <div class="contentWrap"></div>
</div>

<h1>My Account</h1>

<a href="#" class="account-tab" onClick="setAcctTab('acct-det'); return false;">Account Details</a> 
| <a href="#" class="account-tab" onClick="setAcctTab('acct-fin'); return false;">My Finances</a> 
<?php if ($_SESSION['group_id'] > 0 && $owneradmin == true) { ?> | <a href="#" class="account-tab" onClick="setAcctTab('acct-admin'); return false;">Group Admin</a><?php } ?>
 | <a href="?tab=groups">Browse Groups</a>
 <?php if ($_SESSION['group_id'] == 0) { ?> | <a href="?tab=create_group">Create a Group</a> <?php } ?>
 | <a href="invite.php" rel="#overlay">Invite Friends</a>

<div id="messages"></div>

<div id="acct-det">
	<form method="post" action="modules/cc-profiles.php" id="frmAcctUpdate">
	<div id="acct-det-lt" class="float-lt">
		<input type="hidden" name="action" value="update_acct">
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
		<p><label for="fname" class="label">First Name:</label>
		<input id="fname" name="fname" class="input-dark input-250 required" minlength="2" maxlength="45" value="<?php echo $user_profile['fname']; ?>"></p>
		<p><label for="lname" class="label">Last Name:</label>
		<input id="lname" name="lname" class="input-dark input-250 required" minlength="2" maxlength="45" value="<?php echo $user_profile['lname']; ?>"></p>
		<p><label for="email" class="label">Email:</label>
		<input id="email" name="email" class="input-dark input-250" maxlength="45" value="<?php echo $user_profile['email']; ?>"><br /><br />
		<input type="checkbox" id="share-email" name="share-email" class="input-light" style="margin-left: 100px;" <?php if ($user_profile['share_email']==1){ echo "checked='yes'";} ?>><span class="text-small">Share email with Common Change users.</span></p>
		<p><label for="address1" class="label">Address:</label>
		<input id="address1" name="address1" class="input-dark input-250 required" maxlength="45" value="<?php echo $user_profile['address1']; ?>"></p>
		<p><label for="address2" class="label">&nbsp;</label>
		<input id="address2" name="address2" class="input-dark input-250" maxlength="45" value="<?php echo $user_profile['address2']; ?>"></p>
		<p><label for="city" class="label">City:</label>
		<input id="city" name="city" class="input-dark input-250 required" maxlength="45" value="<?php echo $user_profile['city']; ?>"></p>
		<p><label for="state" class="label">State:</label>
		<select name="state" class="input-dark required" value="<?php echo $user_profile['state']; ?>">
			<option id="NA" value="NA" Selected>Select State</opton>
			<option id="NA" value="NA">Non-US Resident</opton>
			<option id="AL" value="AL">Alabama</opton>
			<option id="AK" value="AK">Alaska</opton>
			<option id="AZ" value="AZ">Arizona</opton>
			<option id="AR" value="AR">Arkansas</opton>
			<option id="CA" value="CA">California</opton>
			<option id="CO" value="CO">Colorado</opton>
			<option id="CT" value="CT">Connecticut</opton>
			<option id="DE" value="DE">Delaware</opton>
			<option id="FL" value="FL">Florida</opton>
			<option id="GA" value="GA">Georgia</opton>
			<option id="HI" value="HI">Hawaii</opton>
			<option id="ID" value="ID">Idaho</opton>
			<option id="IL" value="IL">Illinois</opton>
			<option id="IN" value="IN">Indiana</opton>
			<option id="IA" value="IA">Iowa</opton>
			<option id="KS" value="KS">Kansas</opton>
			<option id="KY" value="KY">Kentucky</opton>
			<option id="LA" value="LA">Louisiana</opton>
			<option id="ME" value="ME">Maine</opton>
			<option id="MD" value="MD">Maryland</opton>
			<option id="MA" value="MA">Massachusetts</opton>
			<option id="MI" value="MI">Michigan</opton>
			<option id="MN" value="MN">Minnesota</opton>
			<option id="MS" value="MS">Mississippi</opton>
			<option id="MO" value="MO">Missouri</opton>
			<option id="MT" value="MT">Montana</opton>
			<option id="NE" value="NE">Nebraska</opton>
			<option id="NV" value="NV">Nevada</opton>
			<option id="NH" value="NH">New Hampshire</opton>
			<option id="NJ" value="NJ">New Jersey</opton>
			<option id="NM" value="NM">New Mexico</opton>
			<option id="NY" value="NY">New York</opton>
			<option id="NC" value="NC">North Carolina</opton>
			<option id="ND" value="ND">North Dakota</opton>
			<option id="OH" value="OH">Ohio</opton>
			<option id="OK" value="OK">Oklahoma</opton>
			<option id="OR" value="OR">Oregon</opton>
			<option id="PA" value="PA">Pennsylvania</opton>
			<option id="RI" value="RI">Rhode Island</opton>
			<option id="SC" value="SC">South Carolina</opton>
			<option id="SD" value="SD">South Dakota</opton>
			<option id="TN" value="TN">Tennessee</opton>
			<option id="TX" value="TX">Texas</opton>
			<option id="UT" value="UT">Utah</opton>
			<option id="VT" value="VT">Vermont</opton>
			<option id="VA" value="VA">Virginia</opton>
			<option id="WA" value="WA">Washington</opton>
			<option id="DC" value="DC">Washington D.C.</opton>
			<option id="WV" value="WV">West Virginia</opton>
			<option id="WI" value="WI">Wisconsin</opton>
			<option id="WY" value="WY">Wyoming</opton>
		</select></p>
		<?php echo "<script>document.getElementById('" . $user_profile['state'] . "').selected=true;</script>"; ?>

		<p><label for="zip" class="label">Zip:</label>
		<input id="zip" name="zip" class="input-dark input-125" minlength="5" maxlength="10" value="<?php echo $user_profile['zip']; ?>"></p>
		<p><label for="country" class="label">Country:</label>
		<select name="country" class="input-dark required" value="<?php echo $user_profile['country']; ?>">
			<option id="NA" value="NA" Selected>Select Country</opton>
			<option id="United States" value="United States">United States</opton>
		</select></p>
		<?php echo "<script>document.getElementById('" . $user_profile['country'] . "').selected=true;</script>"; ?>
		<br>

		<p class="text-small">To change your password, type a new one below. Otherwise, leave the password fields blank.</p>
		<p><label for="current_pass" class="label">Current Password:</label>
		<input type="password" id="current_pass" name="current_pass" class="input-dark input-250"></p>
		<p><label for="new_pass" class="label">New <br />Password:</label>
		<input id="new_pass" name="new_pass" class="input-dark input-250" minlength="6"></p>
		<p><label for="conf_pass" class="label">Confirm Password:</label>
		<input id="conf_pass" name="conf_pass" class="input-dark input-250"></p>
		<br /><br />
		
		<h3>Email Preferences</h3>
		<input type="checkbox" id="daily_email" name="daily_email" class="input-dark" <?php if ($user_profile['daily_email']==1){ echo "checked='yes'";} ?>><span class="text-small">Receive email updates once daily</span>
		<br /><br />

		<center><button type="submit" class="btn-green" onClick="sendFormAcct();" style="padding: 6px 15px;">Update Profile</button></center>
		<br />
	</div><!-- .acct-det-left -->

	<div id="acct-det-rt" class="float-lt" style="margin-left: 20px;">
		<div id="acct-social">
			<h3>Social Media:</h3>
			<img class="icon-fb-32x32 float-lt" style="margin-right: 6px; border: 0px;" />
			<input id="facebook" name="facebook" class="input-light input-200" value="<?php echo $user_profile['facebook']; ?>">
			<br />
			<span class="text-small float-lt" style="margin-top: 2px;">Facebook URL</span><br />
			<span class="text-tiny" style="margin-left: 40px;">Example: http://www.facebook.com/commonchange</span>
			<div class="clear" style="height: 8px;"></div>

			<img class="icon-twit-32x32 float-lt"  style="margin-right: 6px; border: 0px;" />
			<input id="twitter" name="twitter" class="input-light input-200" value="<?php echo $user_profile['twitter']; ?>">
			<br />
			<span class="text-small float-lt" style="margin-top: 2px;">Twitter ID</span><br />
			<span class="text-tiny" style="margin-left: 40px;">Example: CommonChange</span>
	</form><!-- .account-update -->

			<div class="clear" style="height: 15px;"></div>
			<h3>Profile Image:</h3>
			<div style="overflow: hidden;"><span class="text-small float-rt"><img id="user-thumb" src="<?php echo $thumb_dir . $user_profile['thumb']; ?>" class="thumb-100x100" align="left"></img><p>Images should be smaller than 2 mb in size, and should measure at least 250x250 pixels.</p></span></div>
			<br />

			<div class="clear" style="height: 10px;"></div>
			<div align="center">  	
				<form method="post" action="upload.php" id="frmUpload">
					<input type="hidden" name="type" value="user" />
					<input id="user-file" name="file" type="file" class="input-dark input-200" onChange="sendFormUpload(); $('#frmUpload').submit();"/>
				</form>
			</div>
			<div class="clear" style="height: 10px;"></div>

		</div><!-- .acct-social -->

			<div class="clear" style="height: 30px;"></div>

		<div id="acct-requests">
			<?php if ($_SESSION['group_id'] == 0) { ?>
			<h3>Group Invitations</h3>
			<div class="height100">
			<table id="tbl-group-invites">
				<thead>
					<tr>
						<th>Group</th>
						<th>Invited By</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php 
						//If the user has any group invitations, display them.
						if (empty($invites)) {
							//do nothing
							echo "<tr class='inactive'>";
							echo "<td>&nbsp;</td>";
							echo "<td>&nbsp;</td>";
							echo "<td>&nbsp;</td>";
							echo "</tr>";
						} else {
							//Output the results
							foreach($invites as $invite) {
					?>
					<tr class="active">
						<td><?php echo $invite['groupid']; ?></td>
						<td><?php echo $invite['fname'] . ' ' . $invite['lname']; ?></td>
						<td><button type="button" class="btn-green text-small" onClick="window.location.href='<?php echo $_SESSION['home_url']; ?>?tab=account&action=accept_invitation';">Join</button><button type="button" class="btn-green text-small" onClick="window.location.href='<?php echo $_SESSION['home_url']; ?>?tab=account&action=reject_invitation';">No Thanks</button></td>
					</tr>
						<?php } /*end foreach*/ ?>
					<?php } /*end if*/ ?>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table><!-- .tbl-group-invites -->
			</div><!-- .height 100 -->
			<?php } ?>

			<?php if ($_SESSION['group_id'] > 0) { ?>
			<h3>My Requests</h3>
			<div class="height100">
			<table id="tbl-need-requests">
				<thead>
					<tr>
						<th></th>
						<th></th>
						<th>Title</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if (empty($requests)) {
							//Display message
							echo '<tr class="inactive">';
							echo	'<td></td>';
							echo	'<td></td>';
							echo	'<td>No requests.</td>';
							echo	'<td></td>';
							echo '</tr>';
						} else {
							//Return the results
							foreach ($requests as $request) {
					?>
					<tr class="active" onclick="window.location.href = '?tab=view_request&rid=<?php echo $request['requestid']; ?>'">
						<td><img src="<?php echo $thumb_dir . $request['thumb']; ?>" class="thumb-16x16"></td>
						<td><div class="star-on" onclick="star(this, <?php echo $request['requestid']; ?>)"></div></td>
						<td><?php echo $request['title']; ?></td>
						<td><?php echo $request['status']; ?></td>
					</tr>
						<?php } /*end foreach*/ ?>
					<?php } /*end if*/ ?>	
					<tr class="active">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="inactive">
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table><!-- .tbl-need-requests -->
			</div><!-- .height 100 -->
			<?php } ?>

		</div><!-- .acct-requests -->
	</div><!-- .acct-det-right -->
	
</div><!-- .acct-det -->

<div class="clear"></div>
<div id="acct-fin">
<div style="text-align: center;"><h2>You have donated a total of <span class="text-lime">$<?php echo $donation_total; ?></span> to your group.</h2></div>
	<div id="acct-fin-lt" class="float-lt">
		<div class="height150">
		<table id="tbl-acct-fin">
			<thead>
				<tr>
					<th>Amount</th>
					<th>Type</th>
					<th>Date</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if (empty($donations)) {
						//Display message
						echo '<tr class="inactive">';
						echo	'<td>No donations.</td>';
						echo	'<td>&nbsp;</td>';
						echo	'<td>&nbsp;</td>';
						echo '</tr>';
					} else {
						//Return the results
						foreach ($donations as $donation) {
				?>
				<tr class="active">
					<td><?php echo $donation['amount']; ?></td>
					<td><?php echo $donation['type']; ?></td>
					<td><?php echo $donation['date']; ?></td>
					<td><?php echo $donation['status']; ?></td>
				</tr>
					<?php } /*end foreach*/ ?>
				<?php } /*end if*/ ?>	
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="inactive">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table><!-- .tbl-acct-fin -->
		</div><!-- .height 150 -->
		
		<fieldset style="margin-top: 5px;">
			<legend class="text-small">Filter Rows</legend>
			<div class="float-lt">
				<label for="filter-date1" class="label text-small" style="width: 30px;">Date:</label>
				<input id="filter-date1" name="filter-date1" class="input-dark input-100"><br />
					<div class="clear" style="height: 4px;"></div>
				<label for="filter-date2" class="label text-small" style="width: 30px;">&nbsp;</label>
				<input id="filter-date2" name="filter-date2" class="input-dark input-100">
			</div>
			<div class="float-lt" style="margin-left: 20px;">
				<label for="filter-type" class="label text-small">Type:</label><br />
					<div class="clear" style="height: 10px;"></div>
				<select id="filter-type" name="filter-type" class="input-dark input-100">
						<OPTION value="default">Please Select</OPTION>
						<OPTION value=""></OPTION>
				</select>
			</div>
			<div class="float-rt" style="margin-left: 4px;">
				<button class="btn-green text-small" style="width: 75px;">Filter</button><br />
					<div class="clear" style="height: 4px;"></div>
				<button class="btn-green text-small" style="width: 75px;">Export</button>
			</div>
		</fieldset>
	</div><!-- .acct-fin-lt -->

	<div id="acct-fin-rt" class="float-lt">
		<div id="acct-fin-ccdonate" class="float-rt">
			<center><h3>What is Common Change worth to you?</h3></center>
			
			<div style="float: left; width: 300px; margin-top: 10px;">
				<div id="slider"></div>
			</div>
			<div style="float: left; margin-left: 4px;">
				<div id="smiley" class="smiley-neutral"></div>
			</div>
				<div class="clear" style="height: 15px;"></div>

			<label for="amount" class="label text-lime" style="width: 125px; line-height: 32px;">Monthly Contribution:<a href="#" class="text-green tip" style="border: 1px solid #ccc; padding: 2px; background-color: #f0f0f0;" title="Use the sliding scale or enter any amount into the amount field.">?</a></label>
			<input type="text" id="amount" name="amount" style="1px solid #ccc; color: #333; height: 22px; width: 50px;">
			<button type="button" class="btn-green text-small" onClick="supportCC();">Support Common Change</button>

		</div><!-- .acct-fin-donate -->
			<div class="clear" style="height: 20px;"></div>
		<div id="acct-fin-groupdonate">
			<fieldset style="width: 355px;">
				<legend><?php echo $group_info['name']; ?></legend>
				<button class="btn-grey float-lt text-med">$<?php echo $group_info['funds_available'];?><br /><span class="text-small">Group Account Balance</span></button>
				<button type="button" class="btn-green float-rt text-med" style="padding: 10px 4px;" onClick="window.location.href='?tab=payment';">Donate to Your Group</button>
			</fieldset>
		</div><!-- .acct-fin-groupdonate -->

	</div><!-- .acct-fin-rt -->
</div><!-- .acct-fin -->


<div id="acct-admin">
	<form method="post" action="modules/cc-groups.php" id="frmGroupUpdate">
		<div id="acct-admin-lt" style="margin: 0px auto;">
			<input type="hidden" name="action" value="update_group">
			<input type="hidden" name="group_id" value="<?php echo $group_info['groupid']; ?>">
			<p><label for="group-name" class="label">Group Name:</label>
			<input id="group-name" name="group-name" class="input-dark input-250 required" value="<?php echo $group_info['name']; ?>" minlength="2" maxlength="45"></p>
			<p><label for="group-desc" class="label">Group Description:</label>
			<textarea rows=6 id="group-desc" name="group-desc" class="input-dark input-600 required"><?php echo $group_info['description']; ?></textarea></p>
			<!--<p><label for="group-covenant" class="label">Group Covenant:</label>
			<textarea rows=6 id="group-covenant" name="group-covenant" class="input-dark input-600 required"><?php echo $group_info['covenant']; ?></textarea></p>-->
			<p><label for="group-covenant" class="label">Group Tags:</label>
			<input id="group-tags" name="group-tags" class="input-dark input-250" value="<?php echo $group_info['tags']; ?>"><br />
			<span class="text-small" style="margin-left: 100px;">Please enter tags separated by commas (e.g. tag1, tag2).</span></p>
			
			<center><button type="submit" name="grp-update-submit" class="btn-green" style="padding: 6px 15px;" onClick="sendFormGrp();">Update Group</button></center>
		</div><!-- .acct-admin-left -->
	</form><!-- .group-update -->

<!--		<div id="acct-admin-rt" class="float-rt">
			<div id="acct-group-social">
				<div><span class="text-small float-rt"><img id="group-thumb" src="<?php echo $thumb_dir . $group_info['thumb']; ?>" class="thumb-100x100" align="left"></img><p>Images should be smaller than 2 mb in size, and should measure at least 250x250 pixels.</p></span></div>
				<br /><br /><br />

				<h3>Upload a New Photo:</h3>
				<div align="center">  	
					<form method="post" action="upload.php" id="frmUploadGrp">
						<input type="hidden" name="type" value="group" />
						<input id="group-file" name="file" type="file" class="input-dark input-200" />
						<br /><br />
						<button type="submit" class="btn-green" onClick="sendFormUploadGrp();">Upload Image</button>
					</form>
				</div>
				<div class="clear"></div>

			<h3>Group Tags:</h3>
			<input id="group-tags" name="group-tags" class="input-light input-150" value="<?php echo $group_info['tags']; ?>">
				<div class="clear"></div>

			</div><!-- .acct-group-social -->
<!--		</div><!-- .acct-admin-rt -->
	
	<div class="clear"></div>
	<div id="acct-admin-bottom">
		<fieldset>
			<legend>Group Members</legend>
			<div id="acct-group-members" class="float-lt">
				<h3>Current Members:</h3>
				<table id="tbl-group-members">
					<thead>
						<tr>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							if (empty($members)) {
								//Do nothing
							} else {
								//Return the results
								foreach ($members as $member) {
						?>
						<tr id="<?php echo 'row' . $member['userid'] ?>">
							<td><img src="<?php echo $thumb_dir . $member['thumb']; ?>" class="thumb-25x25"></img></td>
							<td class="text-small"><?php echo $member['fname'] . " " . $member['lname']; ?></td>
							<td><select id="group-role" name="group-role" class="input-dark input-100 text-small" onChange="updateRole('<?php echo $member['userid']; ?>','<?php echo $member['groupid']; ?>',this);">
									<OPTION selected><?php echo $member['group_role']; ?></OPTION>
									<?php if($member['group_role'] == 'Member') {
										echo "<OPTION>Owner</OPTION>";
										echo "<OPTION>Administrator</OPTION>";
									} else if($member['group_role'] == 'Owner') {
										echo "<OPTION>Member</OPTION>";
										echo "<OPTION>Administrator</OPTION>";
									} else if($member['group_role'] == 'Administrator') {
										echo "<OPTION>Member</OPTION>";
										echo "<OPTION>Owner</OPTION>";
									} //end if
									?>
								</select>
							</td>
							<td><button class="btn-green text-small" onClick="delMember('<?php echo $member['userid']; ?>','<?php echo $member['groupid']; ?>');">Remove</button></td>
						</tr>
							<?php } /*end foreach*/ ?>
						<?php } /*end if*/ ?>
					</tbody>
				</table><!-- .acct-group-members -->
			</div><!-- .acct-group-members -->

			<div id="acct-group-requests" class="float-rt">
				<h3>Requests to Join Group:</h3>
				<div class="height100">
				<table id="tbl-group-requests">
					<thead>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php 
							//If the user has any group invitations, display them.
							if (empty($join_requests)) {
								//do nothing
								echo "<tr class='inactive'>";
								echo "<td>&nbsp;</td>";
								echo "<td>&nbsp;</td>";
								echo "<td>&nbsp;</td>";
								echo "</tr>";
							} else {
								//Output the results
								foreach($join_requests as $join_request) {
						?>
						<tr class="active">
							<td><?php echo $join_request['fname'] . " " . $join_request['lname']; ?></td>
							<td><?php echo $join_request['email']; ?></td>
							<td><button class="btn-green text-small" onClick="window.location.href='<?php echo $_SESSION['home_url']; ?>?tab=account&action=accept_joinrequest&jid=<?php echo $join_request['userid']; ?>';">Accept</button><button class="btn-green text-small" onClick="window.location.href='<?php echo $_SESSION['home_url']; ?>?tab=account&action=reject_joinrequest&jid=<?php echo $join_request['userid']; ?>';">Deny</button></td>
						</tr>
						<?php } /*end foreach*/ ?>
					<?php } /*end if*/ ?>

						<tr class="inactive">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="inactive">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="inactive">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr class="inactive">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table><!-- .tbl-group-requests -->
				</div><!-- .height 100 -->
			</div><!-- .acct-group-requests -->
		</fieldset>
	</div><!-- .acct-admin-bottom -->

</div><!-- .acct-admin -->

<div class="clear" style="height: 50px;"></div>