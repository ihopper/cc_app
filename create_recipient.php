<script type="text/javascript">
$(document).ready(function() {
    // Validate group creation form
    $("#frmCreateRecipient").validate({
   		messages: {
     		"fname": "Please enter the recipient's first name.",
     		"lname": "Please enter the recipient's last name.",
     		"email": "Please enter the recipient's email.",
     		"address1": "Please enter the recipient's address.",
     		"city": "Please enter the recipient's city.",
     		"state": "Please enter the recipient's state.",
     		"zip": "Please enter the recipient's zip code.",
     		"country": "Please enter the recipient's country."
		},
		errorElement: "div"
	})
});

function sendFormRecipient() {
	$('#frmCreateRecipient').ajaxForm( {
		target: '#messages', 
		success: function() { 
			showNotification({
				message: "Recipient added successfully.",
				type: "warning",
				autoClose: true,
				duration: 5
			});

			//Redirect on success after pausing for the message
			setTimeout(function() {
  				window.location.href="<?php echo $_SESSION['home_url'] . '?tab=create_request'; ?>";
			}, 2300);
		} 
	}); 

};
</script>

<div class="clear" style="height: 15px;"></div>
<h1>Create a Recipient</h1>

<div id="messages"></div>

<div id="create-recipient">
	<form method="post" action="modules/cc-people.php" id="frmCreateRecipient">
	<input type="hidden" name="action" value="add_recipient">
	<input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id']; ?>">
	<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

	<p><label for="prefix" class="label">Title:</label>
	<select id="prefix" name="prefix" class="input-dark">
			<OPTION value="Mr">Mr</OPTION>
			<OPTION value="Mrs">Mrs</OPTION>
			<OPTION value="Ms">Ms</OPTION>
			<OPTION value="Rev">Rev</OPTION>
			<OPTION value="Dr">Dr</OPTION>
			<OPTION value="Rev Dr">Rev Dr</OPTION>
	</select></p>
	<p><label for="fname" class="label">First Name:</label>
	<input id="fname" name="fname" class="input-dark input-350 required" minlength="2" maxlength="45"></p>
	<p><label for="lname" class="label">Last Name:</label>
	<input id="lname" name="lname" class="input-dark input-350 required" minlength="2" maxlength="45"></p>
	<p><label for="email" class="label">Email:</label>
	<input id="email" name="email" class="input-dark input-350 email" minlength="2"></p>
	<p><label for="address1" class="label">Address:</label>
	<input id="address1" name="address1" class="input-dark input-350 required" minlength="2" maxlength="45"></p>
	<p><label for="address2" class="label">&nbsp;</label>
	<input id="address2" name="address2" class="input-dark input-350"></p>
	<p><label for="city" class="label">City:</label>
	<input id="city" name="city" class="input-dark input-350 required" minlength="2" maxlength="45"></p>
	<p><label for="state" class="label">State:</label>
	<select name="state" class="input-dark required">
		<option value="NA">Non-US Resident
		<option value="AL" Selected>Alabama
		<option value="AK">Alaska
		<option value="AZ">Arizona
		<option value="AR">Arkansas
		<option value="CA">California
		<option value="CO">Colorado
		<option value="CT">Connecticut
		<option value="DE">Delaware
		<option value="FL">Florida
		<option value="GA">Georgia
		<option value="HI">Hawaii
		<option value="ID">Idaho
		<option value="IL">Illinois
		<option value="IN">Indiana
		<option value="IA">Iowa
		<option value="KS">Kansas
		<option value="KY">Kentucky
		<option value="LA">Louisiana
		<option value="ME">Maine
		<option value="MD">Maryland
		<option value="MA">Massachusetts
		<option value="MI">Michigan
		<option value="MN">Minnesota
		<option value="MS">Mississippi
		<option value="MO">Missouri
		<option value="MT">Montana
		<option value="NE">Nebraska
		<option value="NV">Nevada
		<option value="NH">New Hampshire
		<option value="NJ">New Jersey
		<option value="NM">New Mexico
		<option value="NY">New York
		<option value="NC">North Carolina
		<option value="ND">North Dakota
		<option value="OH">Ohio
		<option value="OK">Oklahoma
		<option value="OR">Oregon
		<option value="PA">Pennsylvania
		<option value="RI">Rhode Island
		<option value="SC">South Carolina
		<option value="SD">South Dakota
		<option value="TN">Tennessee
		<option value="TX">Texas
		<option value="UT">Utah
		<option value="VT">Vermont
		<option value="VA">Virginia
		<option value="WA">Washington
		<option value="DC">Washington D.C.
		<option value="WV">West Virginia
		<option value="WI">Wisconsin
		<option value="WY">Wyoming
	</select></p>
	<p><label for="zip" class="label">Zip:</label>
	<input id="zip" name="zip" class="input-dark input-125 required" minlength="5" maxlength="10"></p>
	<p><label for="country" class="label">Country:</label>
	<select id="country" name="country" class="input-dark input-125 required">
		<option selected>United States</option>
	</select></p>


	<button type="submit" name="create-submit" class="btn-green" style="margin-left: 100px;" onClick="sendFormRecipient();">Submit</button><br /><br />

</div><!-- .create-recipient -->

<div class="clear" style="height: 50px;"></div>