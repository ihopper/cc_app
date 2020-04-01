<?php
	//Maintain session
	session_start();	

	//Mark the current tab as active
	echo '<script type="text/javascript">'
	   , 'document.getElementById("people").className = "menu-tab-on";'
	   , '</script>';

?>

<script type="text/javascript">
$(document).ready(function() {
	$('#btnAdd').click(function() {
		var num     = $('.clonedInput').length; // how many "duplicatable" input fields we currently have
		var newNum  = new Number(num + 1);      // the numeric ID of the new input field being added
		
		//var newNum = num;
		
		// create the new element via clone(), and manipulate it's ID using newNum value
		var newElem = $('#input' + num).clone().attr('id', 'input' + newNum);
		
		// manipulate the name/id values of the input inside the new element
		newElem.children(':first').attr('id', 'fname' + newNum).attr('name', 'fname' + newNum);
		newElem.find('.second-column').attr('id', 'lname' + newNum).attr('name', 'lname' + newNum);
		newElem.find('.third-column').attr('id', 'email' + newNum).attr('name', 'email' + newNum);
		
		// insert the new element after the last "duplicatable" input field
		$('#input' + num).before(newElem);
		$('#fname' + num).val('');
		$('#lname' + num).val('');
		$('#email' + num).val('');
		
		// enable the "remove" button
		//$('#btnDel').attr('disabled','');
		
		//you can only add 5 names
		if (newNum == 5)
			$('#btnAdd').hide();
	});
 });

function sendFormInvite() {
	$('#frmInvite').ajaxForm( {
		target: '#messages', 
		success: function(data) { 
			alert("Your invitations have been sent!");
			//alert(data);
			//Refresh the page to close the modal window.
			location.reload();
		} 
	}); 

};

</script>
<div id="messages"></div>

<div id="invite" style="width: 600px; padding: 8px; margin: 50px auto;">

	<form method="post" action="modules/cc-email.php" id="frmInvite">
		<h1>Invite Your Friends <br />
		<input type="radio" name="invitation_type" value="cc" checked /> <span class="text-med">to Common Change</span>
		<?php if ($_SESSION['group_id'] > 0) { ?>
			<input type="radio" name="invitation_type" value="group" /> <span class="text-med">to your group</span>
		<?php } /*end if*/ ?>
		</h1>

		<div class="clear" style="height: 20px;"></div>
		<input type="hidden" name="action" value="invite">
		<div>
			<label class="label" style="width: 195px;">First Name</label>
			<label class="label" style="width: 195px;">Last Name</label>
			<label class="label" style="width: 195px;">Email Address</label>
		</div>
		<div id="input1" class="float-lt clonedInput" style="margin-bottom: 10px;">
			<input id="fname1" name="fname1" class="float-lt input-dark input-175 required">
			<input id="lname1" name="lname1" class="float-lt input-dark input-175 required second-column" style="margin-left: 10px;">
			<input id="email1" name="email1" class="float-lt input-dark input-175 required third-column" style="margin-left: 10px;">
		</div>

		<div class="clear"></div>

		<div class="float-lt">
			<a id="btnAdd" class="text-lime">+add row</a>
		</div>
		<div class="float-rt" style="margin-right: 25px;">
			<button type="submit" id="invite-send" name="invite-send" class="btn-green" onClick="sendFormInvite();">Send</button>
			<!--<button type="button" id="cancel" name="cancel" class="btn-green close">Cancel</button>-->
		</div>
	</form><!-- .invite -->
</div><!-- .invite -->

<div class="clear" style="height: 50px;"></div>