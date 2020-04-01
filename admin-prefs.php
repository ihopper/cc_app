<?php
	echo '<script type="text/javascript">';
		//Mark the current tab as active
		echo 'document.getElementById("admin-prefs").className = "admin-menu-on";';
		echo 'document.getElementById("admin").className = "menu-tab-on";';
	echo '</script>';

?>
<h1>Common Change Preferences</h1>
<hr>


<h2>Email Settings</h2>

<p><label for="email-invite" style="text-align: left;">CC Invitation Email:</label>
<textarea rows=10 id="email-invite" name="email-invite" class="input-dark input-600 required" minlength="2"></textarea></p>

<p><label for="email-group-invite" style="text-align: left;">Group Invitation Email:</label>
<textarea rows=10 id="email-invite" name="email-group-invite" class="input-dark input-600 required" minlength="2"></textarea></p>

<p><label for="email-registration" style="text-align: left;">Registration Confirmation Email:</label>
<textarea rows=10 id="email-registration" name="email-registration" class="input-dark input-600 required" minlength="2"></textarea></p>

<p><label for="email-admin" class="label">Administrator Email:</label>
<input id="email-admin" name="email-admin" class="input-dark input-300 required" minlength="2" maxlength="45"></p>


<div class="clear" style="height: 25px;"></div>
<h2>Application Settings</h2>


<p><label for="message-welcome" style="text-align: left;">Welcome Message:</label>
<textarea rows=10 id="message-welcome" name="message-welcome" class="input-dark input-600" minlength="2"></textarea></p>

<p><label for="message-alert" style="text-align: left;">Alert Message:</label>
<textarea rows=10 id="message-alert" name="message-alert" class="input-dark input-600" minlength="2"></textarea></p>


<center><button type="submit" class="btn-green" onClick="">Save Preferences</button></center>