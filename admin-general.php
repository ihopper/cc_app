<?php
	echo '<script type="text/javascript">';
		//Mark the current tab as active
		echo 'document.getElementById("admin-gen").className = "admin-menu-on";';
		echo 'document.getElementById("admin").className = "menu-tab-on";';
	echo '</script>';


	//Include modules
	include_once 'modules/cc-admin.php';

	//Run functions necessary to build the page
	SHOW_APPROVAL_MATRIX($cc_vars);

?>
<h1>General</h1>
<hr>

<div class="clear" style="height: 15px;"></div>
<fieldset>
	<legend class="text-small">Request Approval</legend>
	<div class="fht-table-wrapper">
	<table id="tbl-thread-approval" align="center">
		<thead>
			<tr align="left">
				<th>Members</th>
				<th>0 - 5</th>
				<th>6 - 13</th>
				<th>14 - 20</th>
				<th>21 - 99</th>
				<th>100+</th>
			</tr>
		</thead>
		<tbody>
			<tr class="row-dark">
				<td>
					<input class="tbl-input-sm" size="3" maxlength="3" value="" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>-</span>
					<input class="tbl-input-sm" size="3" maxlength="3" value="9" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>%</span>
				</td>
				<td><input class="tbl-input" value="80" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="66" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="51" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="25" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="10" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
			</tr>
			<tr>
				<td>
					<input class="tbl-input-sm" size="3" maxlength="3" value="10" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>-</span>
					<input class="tbl-input-sm" size="3" maxlength="3" value="19" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>%</span>
				</td>
				<td><input class="tbl-input" value="80" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="66" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="60" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="35" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="30" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
			</tr>
			<tr class="row-dark">
				<td>
					<input class="tbl-input-sm" size="3" maxlength="3" value="20" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>-</span>
					<input class="tbl-input-sm" size="3" maxlength="3" value="35" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>%</span>
				</td>
				<td><input class="tbl-input" value="80" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="75" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="75" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="51" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="40" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
			</tr>
			<tr>
				<td>
					<input class="tbl-input-sm" size="3" maxlength="3" value="36" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>-</span>
					<input class="tbl-input-sm" size="3" maxlength="3" value="45" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>%</span>
				</td>
				<td><input class="tbl-input" value="100" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="75" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="80" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="67" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="51" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
			</tr>
			<tr class="row-dark">
				<td>
					<input class="tbl-input-sm" size="3" maxlength="3" value="46" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>-</span>
					<input class="tbl-input-sm" size="3" maxlength="3" value="100" onFocus="this.className='tbl-input-sm-on'" onBlur="this.className='tbl-input-sm'">
					<span>%</span>
				</td>
				<td><input class="tbl-input" value="100" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="90" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="95" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="75" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
				<td><input class="tbl-input" value="67" onFocus="this.className='tbl-input-on'" onBlur="this.className='tbl-input'"></td>
			</tr>
		</tbody>
	</table>
	</div>

	<div class="clear" style="height: 15px;"></div>
	<button class="btn-green">Add Range</button>

	<div class="clear" style="height: 15px;"></div>
	<label for="thread-approved-notify" class="label text-med" style="width: 200px;">Approved Needs Notification:</label><br />
	<input id="thread-approved-notify" name="thread-approved-notify" class="input-dark input-300 required">
</fieldset>


<div class="clear" style="height: 50px;"></div>
