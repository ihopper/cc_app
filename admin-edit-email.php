<?php
	echo '<script type="text/javascript">';
		//Mark the current tab as active
		echo 'document.getElementById("admin-email").className = "admin-menu-on";';
		echo 'document.getElementById("admin").className = "menu-tab-on";';
	echo '</script>';

?>
<h1>Edit Email Template</h1>
<hr>

<form method="post" action="" id="frmUpdateEmailTemplate">
	<input type="hidden" name="action" value="update_email_template">
	<p><label for="email-from" class="label">From:</label>
	<input id="email-from" name="email-from" class="input-dark input-300 required"></p>
	<p><label for="email-subject" class="label">Subject:</label>
	<input id="email-subject" name="email-subject" class="input-dark input-300 required"></p>
	<p><label for="email-message" class="label">Message:</label>
	<textarea rows=10 id="email-message" name="email-message" class="input-dark input-600 required"></textarea></p>

	<div style="margin-left: 100px;">
		<h3>Schedule: <span class="text-small">Leave blank to retain default settings.</span></h3>
		<div class="float-lt">
			<select id="email-schedule" name="email-schedule" class="input-dark">
					<OPTION value="default">Please Select</OPTION>
					<OPTION value=""></OPTION>
			</select>
			<select id="email-schedule2" name="email-schedule2" class="input-dark">
					<OPTION value="default">Please Select</OPTION>
					<OPTION value=""></OPTION>
			</select>
		</div>
	</div>
	
	<div class="clear" style="height: 20px;"></div>
	<center>
		<button type="submit" class="btn-green">Save</button>
		<button class="btn-green" onClick="window.location.href='?tab=admin&action=email'">Cancel</button>
	<center>
</form>

<div class="clear" style="height: 50px;"></div>