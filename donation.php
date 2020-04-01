<?php
	include_once "vanco_encryption.php.inc";
	include_once "vanco_web_services.php";
	include_once "modules/cc-profiles.php";

	//Set variables
	$cc_vars['id']=$_SESSION['user_id'];
	$cc_vars['user_id']=$_SESSION['user_id'];
	$cc_vars['group_id']=$_SESSION['group_id'];
	$cc_vars['email'] = $_SESSION['email'];

	//Get the user's address information
	GET_PROFILE($cc_vars);

	//Get the address information
	$donor_name = $user_profile['fname'] . " " . $user_profile['lname'];
	$donor_address = $user_profile['address1'];
	$donor_city = $user_profile['city'];
	$donor_state = $user_profile['state'];
	$donor_zip = $user_profile['zip'];

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
	case 'GET': $the_request = &$_GET; break;
	case 'POST': $the_request = &$_POST; break;
	default: $the_request = &$_POST; break;
	}

	//Get the page parameters passed through GET
	// 'amp;' has to be added to returned variables, due to Vanco string creation bug.
	$vanco_vars['ClientID'] = strip_tags($the_request['amp;ClientID']);
	$vanco_vars['CustomerID'] = strip_tags($the_request['amp;CustomerID']);
	$vanco_vars['CustomerRef'] = strip_tags($the_request['amp;CustomerRef']);
?>

<script type='text/javascript'>
//break out of the iFrame, if necessary.
if(this != top){
	//top.location.href = this.location.href;
	top.location.href = 'http://www.isaachopper.com/dev/cc/?tab=donation&ClientID=<?php echo $vanco_vars["ClientID"]; ?>&CustomerID=<?php echo $vanco_vars["CustomerID"]; ?>&CustomerRef=<?php echo $vanco_vars["CustomerRef"]; ?>';
}


$(document).ready(function() {
	//Show the date field popup
	$(":date").dateinput({ format: "mm/dd/yyyy" });

});

</script>

<?php

	//Second Pass Through
	$vanco_vars['ClientID'] = strip_tags($the_request['ClientID']);
	$vanco_vars['CustomerID'] = strip_tags($the_request['CustomerID']);
	$vanco_vars['CustomerRef'] = strip_tags($the_request['CustomerRef']);

	$vanco_vars['RequestID'] = '12345';//rand(1000, 10000);

	//Run the functions for this page
	VANCO_LOGIN();
	VANCO_GET_PAYMENT_METHOD($vanco_vars)

?>

<div class="clear" style="height: 15px;"></div>
<div style="width: 700px; min-height: 150px; margin: 0 auto; padding: 10px 6px;">
	<fieldset>
	<legend><h3>Payment Details</h3></legend>
	
	<span class="text-med">Account Type: 
		<?php 
			if($vanco_vars['AccountType']=='C'){ 
				echo 'Checking Account'; 
			} else if($vanco_vars['AccountType']=='S'){  
				echo 'Savings Account';
			} else if($vanco_vars['AccountType']=='CC'){  
				echo $vanco_vars['CardType'];
			} //end if
		?>
	</span><br />
	<span class="text-med">Account Number: <?php echo $vanco_vars['AccountNumber']; ?></span><br />
	<span class="text-med">Routing Number: <?php echo $vanco_vars['RoutingNumber']; ?></span><br />
	</fieldset>

	<div class="clear" style="height: 15px;"></div> 
	<fieldset>
	<legend><h3>Billing Address</h3></legend>
	<?php
		//Make sure the address information is intact
		if($donor_name == '' || $donor_address == '' || $donor_city == '' || $donor_state == '' || $donor_zip == ''){
			echo "<h3 style='color: red;'>Your full address is required for making a donation</h3>";
		} else {
	?>
		<span class="text-med"><?php echo $donor_name; ?></span><br />
		<span class="text-med"><?php echo $donor_address; ?></span><br />
		<span class="text-med"><?php echo $donor_city . ', ' . $donor_state . ' ' . $donor_zip ?></span><br />
	<?php } ?>
	</fieldset>
</div>

<div class="clear" style="height: 15px;"></div>
<div style="width: 700px; min-height: 150px; margin: 0 auto; padding: 10px 6px;">
	<fieldset>
		<legend><h3>Fund Designations and Amounts</h3></legend>
	<p class="text-med">Please fill out the following information and click "continue" to confirm your donation details.</p>

	<form method="get" action="#" id="frmDonation">
		<input type="hidden" name="tab" value="donation_confirm">
		<input type="hidden" name="SessionID" value="<?php echo $vanco_vars['SessionID']; ?>">
		<input type="hidden" name="ClientID" value="<?php echo $vanco_vars['ClientID']; ?>">
		<input type="hidden" name="CustomerID" value="<?php echo $vanco_vars['CustomerID']; ?>">
		<input type="hidden" name="CustomerRef" value="<?php echo $vanco_vars['CustomerRef']; ?>">
		<input type="hidden" name="RequestID" value="<?php echo $vanco_vars['RequestID']; ?>">
		<input type="hidden" name="PaymentMethodRef" value="<?php echo $vanco_vars['PaymentMethodRef']; ?>">
		<input type="hidden" name="AccountType" value="<?php echo $vanco_vars['AccountType']; ?>">
		<input type="hidden" name="CardType" value="<?php echo $vanco_vars['CardType']; ?>">
		<input type="hidden" name="AccountNumber" value="<?php echo $vanco_vars['AccountNumber']; ?>">
		<input type="hidden" name="RoutingNumber" value="<?php echo $vanco_vars['RoutingNumber']; ?>">

		<div class="clear" style="height: 10px;"></div>
		<label class="text-med label" style="width: 125px;">Donation Amount:</label>
		<input type="text" name="FundAmount" class="input-100" style="text-align: right;" value="<?php echo $_SESSION['donation_amount']; ?>">		

		<div class="clear" style="height: 10px;"></div>
		<label class="text-med label" style="width: 125px;">Select a Fund:</label>
		<select name="FundID" class="input-100">
			<option value="0001">Your Group Fund</option>
			<option value="0002">Common Change Fund</option>
		</select>
		
		<div class="clear" style="height: 10px;"></div>
		<label class="text-med label" style="width: 125px;">Donation Frequency:</label>
		<select name="FrequencyCode" class="input-100">
			<option>Choose One</option>
			<option value="O">One Time</option>
			<option value="W">Weekly</option>
			<option value="M">Monthly</option>
			<option value="Q">Quarterly</option>
			<option value="A">Annually</option>
		</select>
		
		<div class="clear" style="height: 10px;"></div>
		<label class="text-med label" style="width: 125px;">Donation Start Date:</label>
		<input type="date" name="StartDate" class="input-100">
		
		<div class="clear" style="height: 10px;"></div>
		<button type="submit" class="btn-green" style="margin-left: 150px;">Continue</button>
	</form><!--.frmDonation-->
	</fieldset>
</div>

<div class="clear" style="height: 50px;"></div>