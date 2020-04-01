<?php

include_once 'modules/cc-people.php';
include_once 'modules/cc-groups.php';
include_once 'modules/cc-accounts.php';

//Assign variables
$cc_vars['group_id'] 	= $_SESSION['group_id'];
$cc_vars['user_id'] 	= $_SESSION['user_id'];
$cc_vars['request_id'] 	= $request_id;

//Run the functions needed for this page.
LIST_MEMBERS ($cc_vars);
	$cc_vars['num_members'] = count($members);
$recipients = GET_RECIPIENTS($cc_vars);
$funds_available = CHECK_FUNDS ($cc_vars);

?>

<script type="text/javascript">
$(document).ready(function() {
	//Create an array of recipients
	var recipient_list=new Array;
	<?php
		$iCount = 0;
		foreach ($recipients as $recipient) {
			echo "recipient_list[$iCount] = { label: '". $recipient['fname'] . " " . $recipient['lname'] ."',";
			echo "address1: '". $recipient['address1'] ."',";
			echo "address2: '". $recipient['address2'] ."',";
			echo "city: '". $recipient['city'] ."',";
			echo "state: '". $recipient['state'] ."',";
			echo "zip: '". $recipient['zip'] ."',";
			echo "country: '". $recipient['country'] ."',";
			echo "recipientid: '". $recipient['recipientid'] ."'};\n";
			$iCount++;
		}
	?>

	//Show the date field popup
	$(":date").dateinput({ format: "mm/dd/yyyy" });

	//Enable autocomplete for recipients
	$( "#lookup" ).autocomplete({
		source: recipient_list,
		focus: function( event, ui ) {
			$( "#lookup" ).val( ui.item.label );
				return false;
		},	
		select: function(event, ui) {
			//Once an item is selected, output the recipient's information
			var raddress;

			raddress = "<p style='margin-left: 100px;' class='text-med'>" + ui.item.address1 + "<br />" + ui.item.address2 + "<br />" + ui.item.city + ", " + ui.item.state + " " + ui.item.zip + "<br />" + ui.item.country + "</p>";
			$( "#recipient_id" ).attr('value', ui.item.recipientid );
			$( "#recipient_address" ).html(raddress);
		}
	});

    // Validate forms
    $("#frmPostNeed").validate({
   		messages: {
     		"title": "Please enter a request title",
			"process": "Please select a process.",
			"date": "You must enter a valid date.",
			"recipient_id": "You must choose a valid recipient.",
			"content": "Please enter a description.",	
			"tos": "You must agree to the Terms of Service in order to proceed.",
			"amount": "Insufficient funds. Your group has $<?php echo $funds_available ?> available for new requests."
   		},
		rules: {
			"amount": {max: <?php echo $funds_available ?>}
		},
		errorElement: "div"
	});


	$("a[rel]").colorbox({href:"tos.html", width: "900px", height: "500px", close: "Close X"});
});

function sendFormNeed() {

	//Disable the submit button
	//obj.disabled = true;
	//obj.value = "Please wait...";

	//Verify that sufficient funds are available for the request.
//	if($('#amount').val() > <?php echo $funds_available ?>){
//		alert('There are not enough group funds available for your request.');
//		$("form[name='frmPostNeed']").submit(function(){
//        	return false;
//    	});
//	} else {
		//Submit the form in the background
		$('#frmPostNeed').ajaxForm( {
			target: '#messages', 
			success: function() { 
				showNotification({
					message: "The need was successfully added to your group queue.",
					type: "warning",
					autoClose: true,
					duration: 5
				}); 
				//Redirect on success after pausing for the message
				setTimeout(function() {
	  				window.location.href="<?php echo $_SESSION['home_url'] . '?tab=mygroup'; ?>";
				}, 2300);
			} 
		}); 
//	} //end if
};

function convertCurrency() {
	window.open("http://www.xe.com/pca/input.php", "Currency Converter", "width=600,height=500");
};

</script>

<div class="clear" style="height: 15px;"></div>
<h1>Post a Need</h1>

<div id="messages"></div>
<div id="new-left">
	<div id="forms">
		<form method="post" action="modules/cc-accounts.php" id="frmPostNeed" name="frmPostNeed">
		<input type="hidden" name="action" value="add_request">
		<input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id']; ?>">
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
		<input type="hidden" name="num_members" value="<?php echo $cc_vars['num_members']; ?>">

		<div id="recipient">
			<fieldset>
			<legend><h3>Recipient</h3></legend>
			<input type="hidden" id="recipient_id" name="recipient_id">
			<div id="recipient-lookup">
				<label for="lookup" class="label">Name:</label>
				<input type="text" id="lookup" class="input-dark input-250">
				<button type="button" class="btn-grey2 text-small" onClick="window.location.href='?tab=create_recipient';">Create Recipient</button>
			</div><!-- .recipient-lookup -->
			<div id="recipient_address"></div>
			</fieldset>
		</div><!-- .recipient -->

		<div class="clear" style="height: 15px;"></div>
		<div class="float-lt" style="width: 100%;">
			<fieldset>
			<legend><h3>Request Details</h3></legend>
			<p><label for="title" class="label">Title:</label>
			<input id="title" name="title" class="input-dark input-350 required" MAXLENGTH="140" minlength="2"></p>
			<div class="float-lt">
				<label for="process" class="label">Response Process:</label>
				<select id="process" name="process" class="input-dark" onChange="setRequestProcess(this.value);">
					<OPTION value="default">Please Select</OPTION>
					<OPTION value="brainstorm">Brainstorm With Me</OPTION>
					<OPTION value="help">Need Help Creating a Solution</OPTION>
					<OPTION value="request">Request</OPTION>
					<OPTION value="specific-request">Specific Request</OPTION>
				</select>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-type" class="float-lt" style="display: none;">
				<label for="type" class="label">Request Type:</label>
				<select id="type" name="type" class="input-dark">
					<OPTION value="default">Please Select</OPTION>
					<OPTION value="Emergency">Crises/Emergency</OPTION>
					<OPTION value="Basic">Basic Living & Preventative</OPTION>
					<OPTION value="Kindness">Gifts of Kindness</OPTION>
					<OPTION value="Luxury">Little Luxuries</OPTION>
				</select>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-cat" class="float-lt" style="display: none;">
				<label for="cat" class="label">Category:</label>
				<select id="cat" name="cat" class="input-dark">
					<OPTION value="default">Please Select</OPTION>
					<OPTION value="Basic Living"><img src="images/icon-basic-living.png" class="icon-24x24">Basic Living / Survivor</OPTION>					<OPTION value="Education"><img src="images/icon-education.png" class="icon-24x24">Education</OPTION>					<OPTION value="Gap Grant"><img src="images/icon-gap-grant.png" class="icon-24x24">Gap Grant</OPTION>					<OPTION value="Health Care Expenses"><img src="images/icon-healthcare-expenses.png" class="icon-24x24">Health Care Expenses</OPTION>					<OPTION value="Housing"><img src="images/icon-housing.png" class="icon-24x24">Housing</OPTION>					<OPTION value="Parental Support"><img src="images/icon-parental-support.png" class="icon-24x24">Parental Support</OPTION>					<OPTION value="Professional Expenses"><img src="images/icon-professional-expenses.png" class="icon-24x24">Professional Expenses</OPTION>					<OPTION value="Self-Sustaining Initiatives"><img src="images/icon-self-sustaining-initiatives.png" class="icon-24x24">Self-Sustaining Initiatives</OPTION>					<OPTION value="TBD"><img src="images/icon-tbd.png" class="icon-24x24">To Be Determined</OPTION>					<OPTION value="Transportation"><img src="images/icon-transportation.png" class="icon-24x24">Transportation & Auto Expense</OPTION>					<OPTION value="Utilities"><img src="images/icon-utilities.png" class="icon-24x24">Utilities</OPTION>					<OPTION value="Other">Other</OPTION>
				</select>
			</div>
			<div class="clear" style="height: 10px;"></div>

			<div id="request-amount" class="float-lt" style="display: none;">
				<label for="amount" class="label">Amount (USD): <a onClick="convertCurrency();" class="text-tiny text-lime" target="_blank">Currency Converter</a></label>
				<input id="amount" name="amount" class="input-dark input-125">
				<span class="text-small" style="color: red;">(enter amount in whole dollars)</span>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-date" class="float-lt" style="display: none;">
				<label for="expir" class="label">Date:</label>
				<select id="expir" name="expir" class="input-dark input-125">
					<option value="48hours">48 hours</option>
					<option value="3days">3 days</option>
					<option value="1week">1 week</option>
					<option value="10days">10 days</option>
					<option value="1month">1 month</option>
				</select>
				<!--<input type="date" id="expir" name="expir" class="input-dark input-125">-->
				<span class="text-small required" style="color: red;">(users must weigh-in by this date)</span>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-desc" style="display: none;">
				<div class="float-lt">
					<p><label for="request-desc" class="label">Description:</label>
					<textarea rows=10 id="request-desc" name="content" class="input-dark input-600 required" minlength="2"></textarea></p>
					<br>
				</div><!-- .float-lt -->
				<div class="clear"></div>

				<div class="clear" style="height: 10px;"></div>
				<div class="float-lt">
					<button type="submit" name="create-submit" class="btn-green" onClick="sendFormNeed();">Submit Request</button><br /><br />
					<input type="checkbox" id="tos" name="tos" class="input-dark required"><span class="text-small">Yes, I agree to the <a href="tos.html" rel="#overlay" class="text-lime text-small">Terms of Use</a>.</span><br />
				</div><!-- .float-lt -->
			</div><!-- .request-desc -->
			</fieldset>

		</div><!-- .left -->

		</form>
	</div>	

</div>

<div class="clear" style="height: 50px;"></div>
