<?php
	include_once "vanco_encryption.php.inc";
	include_once "vanco_web_services.php";
	include_once "modules/cc-profiles.php";

	//Check for the request method
	switch($_SERVER['REQUEST_METHOD'])
	{
	case 'GET': $the_request = &$_GET; break;
	case 'POST': $the_request = &$_POST; break;
	default: $the_request = &$_POST; break;
	}


	//Set variables
	$_SESSION['donation_amount'] = strip_tags($the_request['amount']);
	$cc_vars['id']=$_SESSION['user_id'];
	$cc_vars['user_id']=$_SESSION['user_id'];
	$cc_vars['group_id']=$_SESSION['group_id'];
	$cc_vars['email'] = $_SESSION['email'];

	//Get the user's address information
	GET_PROFILE($cc_vars);

	//Setup the URL
	const VANCO_ADDRESS = 'https://www.vancodev.com/cgi-bin/vancotest_ver3.vps?appver3=owQYbecXmO4hpXGzupWAdUh1eSX88q1BHSCftZwn1zOojaIITxf2k_u6ktwGL51sHmkP09_f-qHggHpYhdue-eHYwZh71bQ3xoCyFvDXsEmqwKqjhyRe8XhUeYHCPQew?';
	$vanco_credentials = $url;
	$donor_name = $user_profile['fname'] . " " . $user_profile['lname'];
	$donor_address = $user_profile['address1'];
	$donor_city = $user_profile['city'];
	$donor_state = $user_profile['state'];
	$donor_zip = $user_profile['zip'];
	$return_url = 'http://www.isaachopper.com/dev/cc/donation.php';
	
	$url_string  = VANCO_ADDRESS;
	$url_string .= '&credentials=' . $vanco_credentials;
	$url_string .= '&CustomerName=' . $donor_name;
	$url_string .= '&CustomerAddress=' . $donor_address;
	$url_string .= '&CustomerCity=' . $donor_city;
	$url_string .= '&CustomerState=' . $donor_state;
	$url_string .= '&CustomerZip=' . $donor_zip;
	$url_string .= '&ReturnURL=' . $return_url;

?>

<style type="text/css">
#vanco-frame {
	border: none;	
}
</style>

<script type="text/javascript">
vanco-frame.$('centerContent').style.border='1px solid #000000';
</script>

<h2>Electronic Fund Transfer / Online Giving</h2>
<p class="text-med">Electronic Donations are an easier, more secure way to make your regular donations to Common Change and your group through automatic withdrawal from your bank account or automatic charges to your credit card.</p>
 
<h3>Reasons to donate electronically</h3>
	<li class="text-med" style="margin-left: 20px;">You help reduce overhead expenses associated with processing tax</li>
	<li class="text-med" style="margin-left: 20px;">You can avoid the hassle of writing and mailing checks</li>
	<li class="text-med" style="margin-left: 20px;">You don't have to worry about your check being lost or stolen</li>
	<li class="text-med" style="margin-left: 20px;">Your donations will be recorded on your bank statement</li>

<div class="clear" style="height: 15px;"></div>
<fieldset>
<legend><h3>Your Information</h3></legend>

<?php
//Make sure the address information is intact
if($donor_name == '' || $donor_address == '' || $donor_city == '' || $donor_state == '' || $donor_zip == ''){
	echo "<h2>Your full address is required for making a donation. Please setup your <a href='?tab=account' class='text-lime text-big'>profile</a> and then try again.</h2>";
} else {
?>

	<p class="text-med">We have used the name and address information we have for you. If this is not correct, please update your <a href="?tab=account" class="text-lime">profile</a> before you continue.</p>

<iframe id="vanco-frame" src='<?php echo $url_string; ?>' style="width: 100%; min-height: 400px;"></iframe> 

<?php } ?>
</frameset>

<div class="clear" style="height: 50px;"></div>

