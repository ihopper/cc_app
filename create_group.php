<?php
//Include modules
include_once 'modules/cc-profiles.php';

//Run the functions needed for this page.
$cc_vars['user_id'] = $_SESSION['user_id'];
GET_PROFILE($cc_vars);

echo '<script type="text/javascript">';
	//Mark the current tab as active
	echo 'document.getElementById("groups").className = "menu-tab-on";';
echo '</script>';

//Generate JS function for loading address information.
echo "<script type='text/javascript'>";
	echo "function useAddy() {";
		echo "if (document.getElementById('group-use-addy').checked) {";
			echo "document.getElementById('group-city').value='" . $user_profile['city'] . "';";
			echo "document.getElementById('" . $user_profile['state'] . "').selected=true;";
			echo "document.getElementById('group-zip').value='" . $user_profile['zip'] . "';";
			echo "document.getElementById('" . $user_profile['country'] . "').selected=true;";
		echo "} else {";
			echo "document.getElementById('group-city').value='';";
			echo "document.getElementById('NA').selected=true;";
			echo "document.getElementById('group-zip').value='';";
			echo "document.getElementById('NA2').selected=true;";
		echo "}";
	echo "};";
echo "</script>";
?>
 
<script type="text/javascript">
$(document).ready(function() {
    // Validate group creation form
    $("#frmGroupCreate").validate({
   		messages: {
     		"group-name": "Please enter a group name.",
			"group-desc": "Please enter a group description.",
			"group-city": "Please enter a city.",
			"group-state": "Please select a state.",
			"group-zip": "Please enter a zip code.",
			"group-country": "Please select a country."
		},
		errorElement: "div"
	})
});

function sendFormGroup() {
	$('#frmGroupCreate').ajaxForm( {
		target: '#messages', 
		success: function() { 
			showNotification({
				message: "Your group was successfully created.",
				type: "warning",
				autoClose: true,
				duration: 5
			}); 
			//Redirect on success after pausing for the message
			setTimeout(function() {
  				window.location.href="<?php echo $_SESSION['home_url'] . '?tab=mygroup'; ?>";
			}, 5000);

		} 
	}); 

};
</script>

<h1>Create a New Group</h1>

<div id="messages"></div>

<div id="group-create">
	<form method="post" action="modules/cc-groups.php" id="frmGroupCreate">
		<div id="acct-admin-lt" class="float-lt">
			<input type="hidden" name="action" value="create_group">
			<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
			<p><label for="group-name" class="label">Group Name:</label>
			<input id="group-name" name="group-name" class="input-dark input-300 required" minlength="2" maxlength="45"></p>
			<p><label for="group-desc" class="label">Group Description:</label>
			<textarea rows=10 id="group-desc" name="group-desc" class="input-dark input-600 required" minlength="2"></textarea></p>
			<!--<p><label for="group-covenant" class="label">Group Covenant:</label>
			<textarea rows=10 id="group-covenant" name="group-covenant" class="input-dark input-600 required"></textarea></p>-->
			<p><label for="group-tags" class="label">Group Tags:</label>
			<input id="group-tags" name="group-tags" class="input-dark input-250" value="<?php echo $group_info['tags']; ?>"><br />
			<span class="text-small" style="margin-left: 100px;">Please enter tags separated by commas (e.g. tag1, tag2).</span></p>

			<p><label for="group-use-addy" class="label">&nbsp;</label>
			<input type="checkbox" id="group-use-addy" name="group-use-addy" class="input-dark" onClick="useAddy();"><span class="text-small">Use my current address information.</span><br />

			<p><label for="group-city" class="label">City:</label>
			<input id="group-city" name="group-city" class="input-dark input-300 required" minlength="2" maxlength="45"></p>
			<p><label for="group-state" class="label">State:</label>
			<select name="group-state" class="input-dark required">
				<option id="NA" value="NA" Selected>Non-US Resident
				<option id="AL" value="AL">Alabama
				<option id="AK" value="AK">Alaska
				<option id="AZ" value="AZ">Arizona
				<option id="AR" value="AR">Arkansas
				<option id="CA" value="CA">California
				<option id="CO" value="CO">Colorado
				<option id="CT" value="CT">Connecticut
				<option id="DE" value="DE">Delaware
				<option id="FL" value="FL">Florida
				<option id="GA" value="GA">Georgia
				<option id="HI" value="HI">Hawaii
				<option id="ID" value="ID">Idaho
				<option id="IL" value="IL">Illinois
				<option id="IN" value="IN">Indiana
				<option id="IA" value="IA">Iowa
				<option id="KS" value="KS">Kansas
				<option id="KY" value="KY">Kentucky
				<option id="LA" value="LA">Louisiana
				<option id="ME" value="ME">Maine
				<option id="MD" value="MD">Maryland
				<option id="MA" value="MA">Massachusetts
				<option id="MI" value="MI">Michigan
				<option id="MN" value="MN">Minnesota
				<option id="MS" value="MS">Mississippi
				<option id="MO" value="MO">Missouri
				<option id="MT" value="MT">Montana
				<option id="NE" value="NE">Nebraska
				<option id="NV" value="NV">Nevada
				<option id="NH" value="NH">New Hampshire
				<option id="NJ" value="NJ">New Jersey
				<option id="NM" value="NM">New Mexico
				<option id="NY" value="NY">New York
				<option id="NC" value="NC">North Carolina
				<option id="ND" value="ND">North Dakota
				<option id="OH" value="OH">Ohio
				<option id="OK" value="OK">Oklahoma
				<option id="OR" value="OR">Oregon
				<option id="PA" value="PA">Pennsylvania
				<option id="RI" value="RI">Rhode Island
				<option id="SC" value="SC">South Carolina
				<option id="SD" value="SD">South Dakota
				<option id="TN" value="TN">Tennessee
				<option id="TX" value="TX">Texas
				<option id="UT" value="UT">Utah
				<option id="VT" value="VT">Vermont
				<option id="VA" value="VA">Virginia
				<option id="WA" value="WA">Washington
				<option id="DC" value="DC">Washington D.C.
				<option id="WV" value="WV">West Virginia
				<option id="WI" value="WI">Wisconsin
				<option id="WY" value="WY">Wyoming
			</select></p>
			<p><label for="group-zip" class="label">Zip:</label>
			<input id="group-zip" name="group-zip" class="input-dark input-125 required" minlength="5" maxlength="10"></p>
			<p><label for="group-country" class="label">Country:</label>
			<select name="group-country" class="input-dark required">
				<option id="NA2" value="" SelectNAed>Select Country</opton>
				<option id="United States" value="United States">United States</opton>
			</select></p>

			<center><button type="submit" class="btn-green" onClick="sendFormGroup();">Create Group</button></center>
		</div><!-- .acct-admin-left -->	
	</form><!-- .group-update -->
</div><!-- .group-create -->

<div class="clear" style="height: 50px;"></div>