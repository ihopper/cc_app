<?php
	echo '<script type="text/javascript">';
		//Mark the current tab as active
		echo 'document.getElementById("admin-email").className = "admin-menu-on";';
		echo 'document.getElementById("admin").className = "menu-tab-on";';
	echo '</script>';

?>
<script type="text/javascript">
$(document).ready(function() {
	$('#tbl-admin-email').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });
});
</script>


<h1>Email</h1>
<hr>

<div style="height: 200px;">
<table id="tbl-admin-email">
	<thead>
		<tr>
			<th></th>
			<th>Template Name<button id="th-name" class="sort-desc" onClick="toggle_sort(this)"></button></th>
			<th>Subject <button id="th-subject" class="sort-desc" onClick="toggle_sort(this)"></th>
			<th>Created <button id="th-created" class="sort-desc" onClick="toggle_sort(this)"></th>
			<th>Assigned <button id="th-assigned" class="sort-desc" onClick="toggle_sort(this)"></th>
			<th>Schedule <button id="th-schedule" class="sort-desc" onClick="toggle_sort(this)"></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr class="active">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option>Unassigned</option>
					<option selected>Dailey Update</option>
					<option>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="active">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option>Unassigned</option>
					<option>Dailey Update</option>
					<option selected>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="active">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option>Unassigned</option>
					<option>Dailey Update</option>
					<option>Email Invite</option>
					<option selected>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="active">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option selected>Unassigned</option>
					<option>Dailey Update</option>
					<option>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="active">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option selected>Unassigned</option>
					<option>Dailey Update</option>
					<option>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="inactive">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option selected>Unassigned</option>
					<option>Dailey Update</option>
					<option>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="inactive">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option selected>Unassigned</option>
					<option>Dailey Update</option>
					<option>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
		<tr class="inactive">
			<td>
				<button class="icon-email-edit float-lt" onClick="window.location.href='?tab=admin&action=edit_email'">
				</button><button class="icon-email-delete float-lt"></button>
			</td>
			<td>[Template Name]</td>
			<td>[Email Subject]</td>
			<td>[Date]</td>
			<td>
				<select id="email-assign" class="input-dark input-100 text-small">
					<option selected>Unassigned</option>
					<option>Dailey Update</option>
					<option>Email Invite</option>
					<option>Confirm Account</option>
				</select>
			</td>
			<td>[Schedule]</td>
			<td>
				<button class="btn-green text-small">Accept</button>
				<button class="btn-green text-small">Send Now</button>
			</td>
		</tr>
	</tbody>
</table><!-- .tbl-admin-email -->
</div><!-- .height 250 -->


<div class="clear" style="height: 20px;"></div>

<h1>New Email Template</h1>
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
		<button class="btn-green">Cancel</button>
	<center>
</form>

<div class="clear" style="height: 50px;"></div>