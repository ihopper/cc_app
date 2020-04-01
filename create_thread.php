<h1>Post a Need</h1>

<div id="new-thread-left">
	<div id="forms">
		<form method="post" action="" id="frmSign">
		<input type="hidden" name="action" value="create_thread">
		<div class="float-lt">
			<p><label for="thread-title" class="label">Title:</label>
			<input id="thread-title" name="thread-title" class="input-dark input-350 required"></p>
			<div class="float-lt">
				<label for="thread-type" class="label">Request Type:</label>
				<select id="thread-type" name="thread-type" class="input-dark">
						<OPTION value="default">Please Select</OPTION>
						<OPTION value=""></OPTION>
				</select>
			</div>
			<div class="float-rt">
				<label for="thread-cat" class="label">Category:</label>
				<select id="thread-cat" name="thread-cat" class="input-dark">
						<OPTION value="default">Please Select</OPTION>
						<OPTION value=""></OPTION>
				</select>
			</div>
			<div class="clear" style="height: 10px;"></div>
			<div class="float-lt">
				<label for="thread-amount" class="label">Amount:</label>
				<select id="thread-amount" name="thread-amount" class="input-dark">
						<OPTION value="default">Please Select</OPTION>
						<OPTION value=""></OPTION>
				</select>
			</div>
			<div class="float-rt">
				<label for="thread-time" class="label">Time:</label>
				<select id="thread-time" name="thread-time" class="input-dark">
						<OPTION value="default">Please Select</OPTION>
						<OPTION value=""></OPTION>
				</select>
			</div>
			<div class="clear" style="height: 15px;"></div>

			<h3>Recipient</h3>
			<p><label for="thread-prefix" class="label">Title:</label>
			<select id="thread-prefix" name="thread-prefix" class="input-dark">
					<OPTION value="Mr">Mr</OPTION>
					<OPTION value="Mrs">Mrs</OPTION>
					<OPTION value="Ms">Ms</OPTION>
					<OPTION value="Rev">Rev</OPTION>
					<OPTION value="Dr">Dr</OPTION>
					<OPTION value="Rev Dr">Rev Dr</OPTION>
			</select></p>
			<p><label for="thread-fname" class="label">First Name:</label>
			<input id="thread-fname" name="thread-fname" class="input-dark input-350 required"></p>
			<p><label for="thread-lname" class="label">Last Name:</label>
			<input id="thread-lname" name="thread-lname" class="input-dark input-350 required"></p>
			<p><label for="thread-email" class="label">Email:</label>
			<input id="thread-email" name="thread-email" class="input-dark input-350 email"></p>
			<p><label for="thread-address1" class="label">Address:</label>
			<input id="thread-address1" name="thread-address1" class="input-dark input-350 required"></p>
			<p><label for="thread-address2" class="label">&nbsp;</label>
			<input id="thread-address2" name="thread-address2" class="input-dark input-350"></p>
			<p><label for="cthread-ity" class="label">City:</label>
			<input id="thread-city" name="thread-city" class="input-dark input-350 required"></p>
			<p><label for="thread-state" class="label">State:</label>
			<select name="thread-state" class="input-dark required">
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
			<p><label for="thread-zip" class="label">Zip:</label>
			<input id="thread-zip" name="zip" class="input-dark input-125 required"></p>
			<p><label for="thread-country" class="label">Country:</label>
			<select id="thread-country" name="thread-country" class="input-dark input-125 required">
				<option selected>United States</option>
			</select></p>
			<p><label for="thread-desc" class="label">Description:</label>
			<textarea rows=10 id="thread-desc" name="thread-desc" class="input-dark input-350 required"></textarea></p>
			<br>
		</div><!-- .left -->
		<div class="float-lt" style="margin-left: 20px;">
			<p><label for="thread-tags" class="label float-lt">Tags:</label>
			<input id="thread-tags" name="thread-tags" class="input-dark input-125 float-lt"></p>
			<div style="margin-top: 654px;">
				<button type="submit" name="thread-create-submit" class="btn-green">Create Thread</button><br /><br />
				<input type="checkbox" id="tos" name="tos" class="input-dark"><span class="text-small">Yes, I agree to the Terms of Use</span><br />
			</div>
		</div><!-- .right -->
		</form>
	</div>	

</div>

<div class="clear" style="height: 50px;"></div>
