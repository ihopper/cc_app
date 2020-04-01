<?php
echo '<script type="text/javascript">';
	//Mark the current tab as active
	echo 'document.getElementById("admin-finance").className = "admin-menu-on";';
	echo 'document.getElementById("admin").className = "menu-tab-on";';
echo '</script>';

//Include modules
include_once 'modules/cc-profiles.php';
include_once 'modules/cc-accounts.php';
include_once 'modules/cc-financial.php';
include_once 'modules/cc-groups.php';

//Run the functions needed for this page.

GET_DONATIONS($cc_vars);
GET_GROUPS($cc_vars);

//Initialize variables
$add_donation = '';

//Check for the request method
switch($_SERVER['REQUEST_METHOD'])
{
case 'GET': $the_request = &$_GET; break;
case 'POST': $the_request = &$_POST; break;
default: $the_request = &$_POST; break;
}

//Get the page parameters passed through GET
$add_donation 		= strip_tags($the_request['add_donation']);
$method				= strip_tags($the_request['m']);
$created			= date("Y-m-d");

$cc_vars['user_id']				= strip_tags($the_request['userid']);
$cc_vars['group_id']			= strip_tags($the_request['groupid']);

if ($add_donation == 'yes') {
	$cc_vars['donation_amount']		= strip_tags($the_request['donation_amount']);
	$cc_vars['donation_date']		= strip_tags($the_request['donation_date']);
	$cc_vars['donation_type']		= strip_tags($the_request['donation_type']);
	$cc_vars['donation_ref']		= strip_tags($the_request['donation_ref']);
	$cc_vars['donation_notes']		= '';
	$cc_vars['donation_fund']		= strip_tags($the_request['donation_fund']);
} //end if


if ($method == 'list_members') {
	LIST_MEMBERS($cc_vars);
} else if ($method == 'show_member') {
	//Get the user's address information
	$cc_vars['user_id']	= strip_tags($the_request['userid']);
	LIST_MEMBERS($cc_vars);
	GET_PROFILE($cc_vars);
} //end if


if ($add_donation == 'yes') {
	//Update the database with the donation information
	$message = ADD_DONATION($cc_vars);	
} //end if

?>

<script type="text/javascript">
window.onload = init;

$(document).ready(function() {
	//$('#tbl-group-donations').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });

});

function init() {
	//Hide account page tab sections
  	setFinTab('fin-group'); 
};

function showVanco() {
	window.open("https://www.eservicepayments.com/cgi-bin/Vanco_ver3.vps?appver3=Fi1giPL8kwX_Oe1AO50jRlCEqn_RAMYUQs0hvpWR-8bHO3iVYxvvxhHjRfLOeq662EvVVAEjqawDomKT1pboufHoi_s9Dpue25eeTzHxee8=", "Vanco Services Process Transaction", "width=1024,height=800");
};

function showMessage($message) {
	showNotification({
		message: $message,
		type: "warning",
		autoClose: true,
		duration: 3
	});

	//Reload the page
	setTimeout(function() {
		window.location.href='?tab=admin&page=finance';},
		3000
	);
};

function getMembers(){
	group_id = $('#groupid').val();
	window.location.href='?tab=admin&page=finance&groupid='+group_id+'&m=list_members#step2';
};

function showMember(){
	group_id = $('#groupid').val();
	user_id = $('#userid').val();
	window.location.href='?tab=admin&page=finance&groupid='+group_id+'&userid='+user_id+'&m=show_member#step2';
};

<?php 
	if ($add_donation == 'yes') {
		echo "showMessage('" . $message . "')";
	} //end if
?>

</script>

<h1>Financials</h1>
<hr>
<a href="#" class="account-tab" onClick="setFinTab('fin-group'); return false;">Manage Donations</a> 
| <a href="#" class="account-tab" onClick="setFinTab('fin-reports'); return false;">Reports</a> 

<div class="clear" style="height: 15px;"></div>

<div id="fin-group">
	<h2>Approve / Decline Donations</h2>
	<div class="height150">
	<table id="tbl-group-donations" class="table table-bordered table-striped table-hover sortable">
		<thead>
			<tr>
				<th>Name</th>
				<th>Address</th>
				<th>Amount</th>
				<th>Fund</th>
				<th>Type</th>
				<th>Date</th>
				<th>Status</th>
				<th>Approve / Decline</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				if (empty($donations)) {
					//Display message
					echo '<tr class="inactive">';
					echo	'<td>&nbsp;</td>';
					echo	'<td>&nbsp;</td>';
					echo	'<td>No donations pending.</td>';
					echo	'<td>&nbsp;</td>';
					echo	'<td>&nbsp;</td>';
					echo	'<td>&nbsp;</td>';
					echo	'<td>&nbsp;</td>';
					echo '</tr>';
				} else {
					//Return the results
					foreach ($donations as $donation) {
			?>
			<tr id="grpDonation<?php echo $donation['donationid']; ?>" class="active">
				<td><?php echo $donation['fname'] . ' ' . $donation['lname']; ?></td>
				<td><?php echo $donation['address1']; ?><br /><?php echo $donation['city'] . ' ' . $donation['state'] . ' ' . $donation['zip']; ?></td>
				<td><?php echo $donation['amount']; ?></td>
				<td><?php echo $donation['fund']; ?></td>
				<td><?php echo $donation['type']; ?></td>
				<td><?php echo $donation['date']; ?></td>
				<td><?php echo $donation['status']; ?></td>
				<td>
					<button type="button" class="btn-green text-tiny" onClick="donationStatus('<?php echo $donation['donationid']; ?>', 'approved', '<?php echo $donation['amount']; ?>');">Approve</button>
					<button type="button" class="btn-green text-tiny" onClick="donationStatus('<?php echo $donation['donationid']; ?>', 'declined', '<?php echo $donation['amount']; ?>');">Decline</button>
				</td>
			</tr>
				<?php } /*end foreach*/ ?>
			<?php } /*end if*/ ?>	
		</tbody>
	</table><!-- .tbl-acct-fin -->
	</div><!-- .height 150 -->


	<div class="clear" style="height: 25px;"></div>
	<a name="manual"></a>
	<h2> Add a Manual Group Donation</h2>

	<div class="clear" style="height: 15px;"></div>
	<h3>Step 1: Process Donation</h3>
	<button type="button" class="btn-green" onClick="showVanco();">Open Vanco</button>

	<div class="clear" style="height: 25px;"></div>
	<h3 id="step2">Step 2: Add Donation to Common Change</h3>
	<form method="get" action="#" id="frmManualDonation">
		<input type="hidden" name="add_donation" value="yes">
		<input type="hidden" name="tab" value="admin">
		<input type="hidden" name="page" value="finance">
		<div class="clear" style="height: 15px"></div>
		<fieldset>
			<legend class="text-med">Donor Details</legend>
			<div style="margin-left: 6px;">
				<div class="float-lt" style="margin-right: 10px;">
					<label for="groupid" class="label">Group:</label><br />
					<select id="groupid" name="groupid" class="input-dark input-125 required" style="height: 24px;" onChange="getMembers();">
						<option selected>Please Select</option>
						<?php

							foreach ($groups as $group) {
						?>
							<option id="grp<?php echo $group['groupid']; ?>" value="<?php echo $group['groupid']; ?>" onClick="window.location.href='?tab=admin&page=finance&groupid=<?php echo $group['groupid']; ?>&m=list_members#manual';">
								<?php echo $group['name']; ?>
							</option>
						<?php } /*end foreach*/ ?>
					</select>
					<?php echo "<script>document.getElementById('grp" . $cc_vars["group_id"] . "').selected=true;</script>"; ?>
					<button type="button" class="img-search"></button>
				</div>
				<div class="float-lt">
					<label for="userid" class="label">Member:</label><br />
					<select id="userid" name="userid" class="input-dark input-125 required" style="height: 24px;" onChange="showMember();">
						<option selected>Please Select</option>
						<?php
							
							foreach ($members as $member) {
						?>
							<option id="member<?php echo $member['userid']; ?>" value="<?php echo $member['userid']; ?>" onClick="window.location.href='?tab=admin&page=finance&groupid=<?php echo $cc_vars['group_id']; ?>&userid=<?php echo $member['userid']; ?>&m=show_member#manual';">
								<?php echo $member['lname'] . ', ' . $member['fname']; ?>
							</option>
						<?php } /*end foreach*/ ?>
					</select>
					<?php echo "<script>document.getElementById('member" . $cc_vars["user_id"] . "').selected=true;</script>"; ?>
					<button type="button" class="img-search"></button>
				</div>
				<div class="float-lt">
					<label for="donation_fund" class="label">Fund:</label><br />
					<select id="donation_fund" name="donation_fund" class="input-dark input-125 required" style="height: 24px;">
						<option value="0001" selected>Group Fund</option>
						<option value="0002">Dust Fund</option>
					</select>
					<button type="button" class="img-search"></button>
				</div>
			</fieldset>
		</div>

		<div class="clear" style="height: 15px"></div>
		<fieldset>
			<legend class="text-med">Donation Details</legend>
			<div class="float-lt" style="margin-right: 10px;">
				<label for="donation_amount" class="label">Amount:</label><br />
				<input id="donation_amount" name="donation_amount" class="input-dark input-125 required">
			</div>
			<div class="float-lt" style="margin-right: 10px;">
				<label for="donation_ref" class="label">Reference:</label><br />
				<input id="donation_ref" name="donation_ref" class="input-dark input-125 required">
			</div>
			<div class="float-lt">
				<label for="donation_date" class="label">Date:</label><br />
				<input id="donation_date" name="donation_date" value="<?php echo $created; ?>" class="input-dark input-125 required">
			</div>
		</fieldset>

		<div class="clear" style="height: 15px"></div>
		<fieldset>
			<legend class="text-med">Payment Method</legend>
			<div>
				<input type="radio" name="donation_type" value="CC" checked> <span class="text-med">Credit Card</span>
				<input type="radio" name="donation_type" value="C"> <span class="text-med">ACH</span>
				<input type="radio" name="donation_type" value="EFT"> <span class="text-med">EFT</span>
			</div>		
		</fieldset>

		<div class="clear" style="height: 15px"></div>
		<fieldset>
			<legend class="text-med">Billing Information</legend>
			<p><label for="fname" class="label">Name:</label>
			<span class="text-med"><?php echo $user_profile['fname'] . ' ' . $user_profile['lname']; ?>&nbsp;</span><br /></p>
			<p><label for="address1" class="label">Address:</label> 
			<span class="text-med"><?php echo $user_profile['address1']; ?>&nbsp;</span></p>
			<p><label for="address2" class="label"></label> 
			<span class="text-med"><?php echo $user_profile['address2']; ?>&nbsp;</span></p>
			<p><label for="city" class="label">City:</label> 
			<span class="text-med"><?php echo $user_profile['city']; ?>&nbsp;</span></p>
			<p><label for="state" class="label">State:</label> 
			<span class="text-med"><?php echo $user_profile['state']; ?>&nbsp;</span></p>
			<p><label for="zip" class="label">Zip:</label> 
			<span class="text-med"><?php echo $user_profile['zip']; ?>&nbsp;</span></p>
			<p><label for="country" class="label">Country:</label> 
			<span class="text-med"><?php echo $user_profile['country']; ?>&nbsp;</span></p>
			<p><label for="phone" class="label">Phone:</label> 
			<span class="text-med"><?php echo $user_profile['phone']; ?>&nbsp;</span></p>
			<p><label for="email" class="label">Email:</label> 
			<span class="text-med"><?php echo $user_profile['email']; ?>&nbsp;</span></p>
			<br>
		</fieldset>

		<div class="clear" style="height: 20px;"></div>
		<center>
			<button type="submit" class="btn-green">Save</button>
			<button class="btn-green">Cancel</button>
		<center>
	</form><!-- .frmManualGroupDonation -->
</div><!-- .fin-group -->


<div id="fin-seed">
</div><!-- .fin-seed -->

<div id="fin-reports">
	<h2>Financial Reports</h2>
	<p>Reports will be added once data exists in the database.</p>
</div><!-- .fin-reports -->

<script type="text/javascript">
	$("#groupid").val(<?php echo $cc_vars['group_id']; ?>);
	$("#userid").val(<?php echo $cc_vars['user_id']; ?>);
</script>

<div class="clear" style="height: 50px;"></div>