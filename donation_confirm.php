<?php
	include_once "vanco_encryption.php.inc";
	include_once "vanco_web_services.php";
	include_once "modules/cc-profiles.php";
	include_once "modules/cc-financial.php";

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


	//Initialize variables
	$confirm_payment = '';

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
	case 'GET': $the_request = &$_GET; break;
	case 'POST': $the_request = &$_POST; break;
	default: $the_request = &$_POST; break;
	}

	//Get the page parameters passed through POST
	$confirm_payment			 	= strip_tags($the_request['confirm']);
	$vanco_vars['SessionID'] 		= strip_tags($the_request['SessionID']);

	$vanco_vars['ClientID'] 		= strip_tags($the_request['ClientID']);
	$vanco_vars['CustomerID'] 		= strip_tags($the_request['CustomerID']);
	$vanco_vars['CustomerRef'] 		= strip_tags($the_request['CustomerRef']);
	$vanco_vars['RequestID'] 		= strip_tags($the_request['RequestID']);
	$vanco_vars['PaymentMethodRef'] = strip_tags($the_request['PaymentMethodRef']);

	$vanco_vars['AccountType'] 		= strip_tags($the_request['AccountType']);
	$vanco_vars['CardType'] 		= strip_tags($the_request['CardType']);
	$vanco_vars['AccountNumber'] 	= strip_tags($the_request['AccountNumber']);
	$vanco_vars['RoutingNumber'] 	= strip_tags($the_request['RoutingNumber']);

	$vanco_vars['FundID'] 			= strip_tags($the_request['FundID']);
	$vanco_vars['FundAmount'] 		= strip_tags($the_request['FundAmount']);
	$vanco_vars['FrequencyCode'] 	= strip_tags($the_request['FrequencyCode']);
	$vanco_vars['StartDate'] 		= strip_tags($the_request['StartDate']);

	if ($confirm_payment == 'yes') {
		//Process the secure transaction
		VANCO_ADD_TRANSACTION($vanco_vars);	

		//Logout of Vanco
		VANCO_LOGOUT($vanco_vars);

		//Update the database with the donation information
		$cc_vars['user_id']				= $_SESSION['user_id'];
		$cc_vars['group_id']			= $_SESSION['group_id'];
		$cc_vars['donation_amount']		= $vanco_vars['FundAmount'];
		$cc_vars['donation_date']		= date("Y-m-d H:i:s");
		$cc_vars['donation_type']		= $vanco_vars['AccountType'];
		$cc_vars['donation_ref']		= '';
		$cc_vars['donation_notes']		= '';
		$cc_vars['donation_fund']		= $vanco_vars['FundID'];

		ADD_DONATION($cc_vars);	
	} //end if

	//Set human-readable donation frequency
	switch($vanco_vars['FrequencyCode']) {
		case 'O': $donation_frequency = 'One-Time'; break;
		case 'M': $donation_frequency = 'Monthly'; break;
		case 'W': $donation_frequency = 'Weekly'; break;
		case 'BW': $donation_frequency = 'Biweekly'; break;
		case 'Q': $donation_frequency = 'Quarterly'; break;
		case 'A': $donation_frequency = 'Annual'; break;
	} //end switch

?>

<script type="text/javascript">

$(document).ready(function() {

	//Setup page printing
	//$('#print').prepend('<a class="print-preview">Print this page</a>');
    $('a.print-preview').printPreview();

});

function printPage(){
	$("#header").css('display', 'none');
};

</script>
<div id="printarea">
<h1>Confirm Donation Information</h1>

<div style="min-height: 150px; padding: 10px 6px;">


<fieldset>
<legend><h3>Payment Details</h3></legend>

<label class="text-med label" style="width: 150px;">Account Type:</label><strong class="text-med"> 
	<?php 
		if($vanco_vars['AccountType']=='C'){ 
			echo 'Checking Account'; 
		} else if($vanco_vars['AccountType']=='S'){  
			echo 'Savings Account';
		} else if($vanco_vars['AccountType']=='CC'){  
			echo $vanco_vars['CardType'];
		} //end if
	?>
</strong><br />
<label class="text-med label" style="width: 150px;">Account Number:</label><strong class="text-med"><?php echo $vanco_vars['AccountNumber']; ?></strong><br />
<label class="text-med label" style="width: 150px;">Routing Number:</label><strong class="text-med"><?php echo $vanco_vars['RoutingNumber']; ?></strong><br />
</fieldset>

<div class="clear" style="height: 15px;"></div>
<fieldset>
<legend><h3>Billing Address</h3></legend>
<span class="text-med"><strong><?php echo $donor_name; ?></strong></span><br />
<span class="text-med"><strong><?php echo $donor_address; ?></strong></span><br />
<span class="text-med"><strong><?php echo $donor_city . ', ' . $donor_state . ' ' . $donor_zip ?></strong></span><br />
</fieldset>

<div class="clear" style="height: 15px;"></div>
<fieldset>
<legend><h3>Donation Information</h3></legend>

<label class="text-med label" style="width: 150px;">Donation Amount:</label><strong class="text-med"><?php echo $vanco_vars['FundAmount']; ?> USD</strong><br />
<label class="text-med label" style="width: 150px;">Donation Fund:</label><strong class="text-med"><?php echo $vanco_vars['FundID']; ?></strong><br />
<label class="text-med label" style="width: 150px;">Donation Frequency:</label><strong class="text-med"><?php echo $vanco_vars['FrequencyCode']; ?></strong><br />
<label class="text-med label" style="width: 150px;">Donation Start Date:</label><strong class="text-med"><?php echo $vanco_vars['StartDate']; ?></strong><br />
</fieldset>

<form method="get" action="#" id="frmDonation">
	<input type="hidden" name="tab" value="donation_confirm">
	<input type="hidden" name="confirm" value="yes">
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
	<input type="hidden" name="FundID" value="<?php echo $vanco_vars['FundID']; ?>">
	<input type="hidden" name="FundAmount" value="<?php echo $vanco_vars['FundAmount']; ?>">
	<input type="hidden" name="FrequencyCode" value="<?php $donation_frequency ?>">
	<input type="hidden" name="StartDate" value="<?php echo $vanco_vars['StartDate']; ?>">

	<div class="clear" style="height: 15px;"></div>
	<?php if ($confirm_payment == 'yes') { ?>
		<center>
			<span class="text-big">Thank you for your donation!</span>
			<span class="text-med">Funds will not appear in your group account until your donation has been processed successfully. Typical time is 3-5 business days.</span>
		</center>
	<?php } else { ?>
		<center><button type="submit" class="btn-green">Confirm Donation</button></center>
	<?php } /*end if*/ ?>
	<div id="print" style="text-align: center;"><center><a class="print-preview">Print this page</a></center></div>
</form>


</div>
</div><!-- .printarea -->