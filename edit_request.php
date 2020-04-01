<?php

//Mark the current tab as active
echo '<script type="text/javascript">'
   , 'document.getElementById("mygroup").className = "menu-tab-on";'
   , '</script>';

//Include modules
include_once 'modules/cc-groups.php';
include_once 'modules/cc-accounts.php';
include_once 'modules/cc-threads.php';
include_once 'modules/cc-people.php';

//Assign variables
$cc_vars['group_id'] 	= $_SESSION['group_id'];
$cc_vars['user_id'] 	= $_SESSION['user_id'];
$cc_vars['request_id'] 	= $request_id;

//Run the functions needed for this page.
LIST_MEMBERS ($cc_vars);
REQUEST_SHOW ($cc_vars);
	$cc_vars['recipient_id'] = $request_info['recipientid']; //Set the recipient.
GET_VOTES ($cc_vars);
SHOW_RECIPIENT ($cc_vars);
?>

<script type="text/javascript">
$(document).ready(function() {
	//Generate the slider control
	$( "#slider" ).slider({
			value:<?php if($vote_totals['yes']==0){ echo "0"; } else { echo $vote_totals['yes']; } ?>,
			min: 0,
			max: <?php echo count($members); ?>,
			step: 1,
			slide: function( event, ui ) {
	
			},
			change: function(event, ui) {
				//smile(ui.value);
	           }
		});
	
	//Tabbed comments area
	$('#tabs > div').hide();
	$('#tabs > div:first').show();
	$('#tabs ul li:first').addClass('active');
	 
	$('#tabs ul li a').click(function(){
		$('#tabs ul li').removeClass('active');
		$(this).parent().addClass('active');
		var currentTab = $(this).attr('href');
		$('#tabs > div').hide();
		$(currentTab).show();
		return false;
	});
	
});

function sendFormNeed() {

	//Disable the submit button
	//obj.disabled = true;
	//obj.value = "Please wait...";

	//Submit the form in the background
	$('#frmPostNeed').ajaxForm( {
		target: '#messages', 
		success: function() { 
			showNotification({
				message: "The request was successfully updated.",
				type: "warning",
				autoClose: true,
				duration: 5
			}); 
			//Redirect on success after pausing for the message
			setTimeout(function() {
  				window.location.href="<? echo $_SESSION['home_url']; ?>?tab=view_request&rid=<?php echo $request_info['requestid']; ?>";
			}, 2300);
		} 
	}); 

};

function convertCurrency() {
	window.open("http://www.xe.com/pca/input.php", "Currency Converter", "width=600,height=500");
};


</script>

<div id="messages"></div>

<form method="post" action="modules/cc-accounts.php" id="frmPostNeed">
	<input type="hidden" name="action" value="update_request">
	<input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id']; ?>">
	<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
	<input type="hidden" name="request_id" value="<?php echo $cc_vars['request_id']; ?>">

	<p><label for="title" class="label">Title:</label><br />
	<input id="title" name="title" class="input-dark input-200 required" MAXLENGTH="140" minlength="2" value="<?php echo $request_info['title']; ?>">
	<?php if ($request_info['ownerid'] == $_SESSION['user_id']) { ?>
		<button type="submit" class="btn-green" style="margin-left: 10px;" onClick="sendFormNeed();">Save Changes</button> <span class="text-med">&nbsp;|</span>
		<a href="<? echo $_SESSION['home_url']; ?>?tab=view_request&rid=<?php echo $request_info['requestid']; ?>" class="text-med text-lime" style="margin-left: 10px;">Cancel</a>
	<?php } /*end if*/ ?>
	</p>
<div id="view-thread">
<div class="float-lt">
	<div id="view-thread-left" class="float-lt">
		<h3 style="margin-top: 4px;">Status: <?php echo $request_info['status']; ?></h3>
		<div id="view-thread-left-top" class="float-lt">
			<span class="text-med bold">Presented by</span><br />
			<div class="float-lt" style="margin-top: 4px;"> 
				<img src="<?php echo $thumb_dir . $request_info['thumb']; ?>" class="thumb-48x48">		
			</div>
			<div class="float-lt">
				<span class="text-med"> <?php echo $request_info['fname'] . " " . $request_info['lname']; ?><br />
				<?php echo $request_info['city']; ?>, <?php echo $request_info['state']; ?>, <?php echo $request_info['zip']; ?></span><br />
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div class="float-lt">
				<label for="process" class="label">Response Process:</label><br />
				<select id="process" name="process" class="input-dark" onChange="setRequestProcess(this.value);">
					<OPTION id="default" value="default">Please Select</OPTION>
					<OPTION id="brainstorm" value="brainstorm">Brainstorm With Me</OPTION>
					<OPTION id="help" value="help">Need Help Creating a Solution</OPTION>
					<OPTION id="request" value="request">Request</OPTION>
					<OPTION id="specific-request" value="specific-request">Specific Request</OPTION>
				</select>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-type" class="float-lt" style="display: none;">
				<label for="type" class="label">Request Type:</label><br />
				<select id="type" name="type" class="input-dark">
					<OPTION value="default">Please Select</OPTION>
					<OPTION id="Emergency" value="Emergency">Crises/Emergency</OPTION>
					<OPTION id="Basic" value="Basic">Basic Living & Preventative</OPTION>
					<OPTION id="Kindness" value="Kindness">Gifts of Kindness</OPTION>
					<OPTION id="Luxury" value="Luxury">Little Luxuries</OPTION>
				</select>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-cat" class="float-lt" style="display: none;">
				<label for="cat" class="label">Category:</label><br />
				<select id="cat" name="cat" class="input-dark">
					<OPTION value="default">Please Select</OPTION>
					<OPTION value="Basic Living"><img src="images/icon-basic-living.png" class="icon-24x24">Basic Living / Survivor</OPTION>					<OPTION value="Education"><img src="images/icon-education.png" class="icon-24x24">Education</OPTION>					<OPTION value="Gap Grant"><img src="images/icon-gap-grant.png" class="icon-24x24">Gap Grant</OPTION>					<OPTION value="Health Care Expenses"><img src="images/icon-healthcare-expenses.png" class="icon-24x24">Health Care Expenses</OPTION>					<OPTION value="Housing"><img src="images/icon-housing.png" class="icon-24x24">Housing</OPTION>					<OPTION value="Parental Support"><img src="images/icon-parental-support.png" class="icon-24x24">Parental Support</OPTION>					<OPTION value="Professional Expenses"><img src="images/icon-professional-expenses.png" class="icon-24x24">Professional Expenses</OPTION>					<OPTION value="Self-Sustaining Initiatives"><img src="images/icon-self-sustaining-initiatives.png" class="icon-24x24">Self-Sustaining Initiatives</OPTION>					<OPTION value="TBD"><img src="images/icon-tbd.png" class="icon-24x24">To Be Determined</OPTION>					<OPTION value="Transportation"><img src="images/icon-transportation.png" class="icon-24x24">Transportation & Auto Expense</OPTION>					<OPTION value="Utilities"><img src="images/icon-utilities.png" class="icon-24x24">Utilities</OPTION>					<OPTION value="Other">Other</OPTION>
				</select>
			</div>
			<div class="clear" style="height: 10px;"></div>

			<div id="request-amount" class="float-lt" style="display: none;">
				<label for="amount" class="label">Amount (USD): <a onClick="convertCurrency();" class="text-tiny text-lime" target="_blank">Currency Converter</a></label><br />
				<div class="clear"></div>
				<input id="amount" name="amount" class="input-dark input-125" value="<?php echo $request_info['amount']; ?>">
				<span class="text-small" style="color: red;">(enter amount in whole dollars)</span>
			</div>

			<div class="clear" style="height: 10px;"></div>
			<div id="request-date" class="float-lt" style="display: none;">
				<label for="expir" class="label">Date:</label><br />
				<select id="expir" name="expir" class="input-dark input-125">
					<option value="24hours">24 hours</option>
					<option value="3days">3 days</option>
					<option value="1week">1 week</option>
					<option value="10days">10 days</option>
					<option value="1month">1 month</option>
				</select>
				<!--<input type="date" id="expir" name="expir" class="input-dark input-125">-->
				<span class="text-small" style="color: red;">(users must weigh-in by this date)</span>
			</div>
		</div><!-- .view-thread-left-top -->
	</div><!-- .view-thread-left -->

	<div class="float-lt">
		<center><button type="button" class="btn-grey2" style="width: 158px; height: 44px; margin: 0px auto;">Need Undecided</button></center>
	
		<div style="width: 200px; margin-top: 20px;">
			<span class="float-lt text-small">(<?php echo $vote_totals['no'];?>%)</span>
			<span class="float-rt text-small">(<?php echo $vote_totals['yes'];?>%)</span>

			<div class="clear" style="height: 2px;"></div>
			<div id="slider"></div>

			<div class="clear" style="height: 2px;"></div>
			<img src="images/img-thumb-down.png" class="float-lt">
				<span class="text-small" style="margin-left: 35px;">Awaiting <?php echo count($members) - $vote_totals['total']; ?> responses</span>
			<img src="images/img-thumb-up.png" class="float-rt">
		</div>
	</div><!-- .float-lt -->

	<div class="clear"></div>
	<div class="float-lt">
		<div class="clear"></div>
		<div class="float-lt" style="width: 500px;">
			<p><label for="desc" class="label">Description:</label>
			<textarea rows=6 id="content" name="content" class="input-dark input-500 required" minlength="2"><?php echo $request_info['content']; ?></textarea></p>
		</div><!-- .float-lt -->
	</div><!-- .view-thread-left -->
</div>

	<div id="view-thread-right" class="float-rt">
		<center>
		<button type="button" class="btn-grey text-huge bold" style="width: 158px; height: 44px; margin: 0px auto;">
			$<?php if($request_info['amount']==0){ echo "0"; } else { echo $request_info['amount']; } ?>
		</button>
		</center>

		<div class="clear" style="height: 10px;"></div>
		<span class="text-med bold" style="width: 75px;">Category: </span>
			<span class="text-med"><?php echo $request_info['category'];?></span><br />
		<span class="text-med bold float-lt" style="width: 75px;">Recipient: </span>
			<div class="float-lt">
			<span class="text-med"><?php echo $recipient_info['fname'] . ' ' . $recipient_info['lname'];?></span><br />
			<span class="text-med"><?php echo $recipient_info['address1'];?></span><br />
			<?php if($recipient_info['address2'] != ''){ ?>
				<span class="text-med"><?php echo $recipient_info['address2'];?></span><br />
			<?php } ?>
			<span class="text-med"><?php echo $recipient_info['city'] . " " . $recipient_info['state'] . " " . $recipient_info['zip'];?></span><br />
			<?php if($recipient_info['country'] != ''){ ?>
				<span class="text-med"><?php echo $recipient_info['country'];?></span><br />
			<?php } ?>

			</div>


		<div class="clear" style="height: 30px;"></div>
		<div class="clear" style="height: 10px;"></div>
	</div><!-- .view-thread-right -->

	<?php GET_THREADS($cc_vars); ?>
	<div class="clear" style="height: 20px;"></div>
	<div id="comments">
		<div id="tabs">
			<ul>
				<li><a href="#tab-1" class="text-small">All Comments <span id="all"></span></a></li>
				<li><a href="#tab-2" class="text-small">Approved <span id="approve"></span></a></li>
				<li><a href="#tab-3" class="text-small">Disapproved <span id="disapprove"></span></a></li>
				<li><a href="#tab-4" class="text-small">More Discussion <span id="discuss"></span></a></li>
			</ul>
			<div id="tab-1">
				<?php 
					if (empty($threads)) {
						//Do nothing
						echo "No Comments."; 
					} else {
						//Return the results
						$iAll = count($threads);
						foreach ($threads as $thread) {
				?>	
				<div class="media">
				  <a href="#" class="img">
					<p class="text-lime text-med"><?php echo $thread['fname']; ?> said...</p>
				    <img src="<?php echo $thumb_dir . $thread['thumb']; ?>" alt="me" class="thumb-48x48" />
				  </a>
					<div class="arrow"></div>
				  <div class="bd text-med">
					<p><?php echo $thread['content']; ?></p>
				  </div>
				</div><!-- .media -->
					<?php } /*end foreach*/ ?>
				<?php } /*end if*/ ?>
			</div><!-- .tab-1 -->
			<div id="tab-2">
				<?php 
					if (empty($threads)) {
						//Do nothing
						echo "No Comments."; 
					} else {
						//Return the results
						$iApproved = 0;
						foreach ($threads as $thread) {
							if ($thread['category'] == 'approve') {
								$iApproved++;
				?>	
				<div class="media">
				  <a href="#" class="img">
					<p class="text-lime text-med"><?php echo $thread['fname']; ?> said...</p>
				    <img src="<?php echo $thumb_dir . $thread['thumb']; ?>" alt="me" class="thumb-48x48" />
				  </a>
					<div class="arrow"></div>
				  <div class="bd text-med">
					<p><?php echo $thread['content']; ?></p>
				  </div>
				</div><!-- .media -->
						<?php } /*end if*/ ?>
					<?php } /*end foreach*/ ?>
				<?php } /*end if*/ ?>
			</div><!-- .tab-2 -->
			<div id="tab-3">
				<?php 
					if (empty($threads)) {
						//Do nothing
						echo "No Comments."; 
					} else {
						//Return the results
						$iDisapproved = 0;
						foreach ($threads as $thread) {
							if ($thread['category'] == 'disapprove') {
								$iDisapproved++;
				?>	
				<div class="media">
				  <a href="#" class="img">
					<p class="text-lime text-med"><?php echo $thread['fname']; ?> said...</p>
				    <img src="<?php echo $thumb_dir . $thread['thumb']; ?>" alt="me" class="thumb-48x48" />
				  </a>
					<div class="arrow"></div>
				  <div class="bd text-med">
					<p><?php echo $thread['content']; ?></p>
				  </div>
				</div><!-- .media -->
						<?php } /*end if*/ ?>
					<?php } /*end foreach*/ ?>
				<?php } /*end if*/ ?>
			</div><!-- .tab-3 -->
			<div id="tab-4">
				<?php 
					if (empty($threads)) {
						//Do nothing
						echo "No Comments."; 
					} else {
						//Return the results
						$iDiscuss = 0;
						foreach ($threads as $thread) {
							if ($thread['category'] == 'discuss') {
								$iDiscuss++;
				?>	
				<div class="media">
				  <a href="#" class="img">
					<p class="text-lime text-med"><?php echo $thread['fname']; ?> said...</p>
				    <img src="<?php echo $thumb_dir . $thread['thumb']; ?>" alt="me" class="thumb-48x48" />
				  </a>
					<div class="arrow"></div>
				  <div class="bd text-med">
					<p><?php echo $thread['content']; ?></p>
				  </div>
				</div><!-- .media -->
						<?php } /*end if*/ ?>
					<?php } /*end foreach*/ ?>
				<?php } /*end if*/ ?>
			</div><!-- .tab-4 -->
		</div><!-- .tabs -->
	</div><!-- .comments -->

<?php
	echo "<script type='text/javascript'>";
		echo "$('#all').text('(" . $iAll . ")');";
		echo "$('#approve').text('(" . $iApproved . ")');";
		echo "$('#disapprove').text('(" . $iDisapproved . ")');";
		echo "$('#discuss').text('(" . $iDiscuss . ")');";
	echo "</script>";
?>

</div><!-- .view-thread -->
</form><!-- .frmPostNeed -->

<script type='text/javascript'>
	<?php echo "$('#process').val('" . $request_info['process'] . "');"; ?>
	<?php echo "$('#type').val('" . $request_info['type'] . "');"; ?>
	<?php echo "$('#cat').val('" . $request_info['category'] . "');"; ?>
	<?php echo "$('#expir').val('" . $request_info['expiration'] . "');"; ?>
	<?php echo "setRequestProcess('" . $request_info["process"] . "');"; ?>
</script>

<div class="clear" style="height: 50px;"></div>