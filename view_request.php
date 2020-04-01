<?php

//Mark the current tab as active
//echo '<script type="text/javascript">'
//   , 'document.getElementById("mygroup").className = "menu-tab-on";'
 //  , '</script>';

//Include modules
include_once 'modules/cc-groups.php';
include_once 'modules/cc-accounts.php';
include_once 'modules/cc-threads.php';
include_once 'modules/cc-people.php';

//Check for the request method
switch($_SERVER['REQUEST_METHOD'])
{
case 'GET': $the_request = &$_GET; break;
case 'POST': $the_request = &$_POST; break;
default: $the_request = &$_POST; break;
}

//Get the page parameters passed through GET
$request_id = strip_tags($the_request['rid']);

//Assign variables
$cc_vars['group_id'] 	= $_SESSION['group_id'];
$cc_vars['user_id'] 	= $_SESSION['user_id'];
$cc_vars['request_id'] 	= $request_id;

//Run the functions needed for this page.
LIST_MEMBERS ($cc_vars);
REQUEST_SHOW ($cc_vars);
	$cc_vars['recipient_id'] 	= $request_info['recipientid']; //Set the recipient.
	$cc_vars['request_amount'] 	= $request_info['amount'];
	$cc_vars['num_members'] 	= $request_info['num_members'];
GET_VOTES ($cc_vars);
$has_voted = CHECK_VOTE ($cc_vars);
SHOW_RECIPIENT ($cc_vars);

//Get the category and assign an icon
switch ($request_info['category']) {
	case 'Basic Living': $icon = 'images/icon-basic-living.png'; break;
	case 'Education': $icon = 'images/icon-education.png'; break;
	case 'Gap Grant': $icon = 'images/icon-gap-grant.png'; break;
	case 'Health Care Expenses': $icon = 'images/icon-healthcare-expenses.png'; break;
	case 'Housing': $icon = 'images/icon-housing.png'; break;
	case 'Parental Support': $icon = 'images/icon-parental-support.png'; break;
	case 'Professional Expenses': $icon = 'images/icon-professional-expenses.png'; break;
	case 'Self-Sustaining Initiatives': $icon = 'images/icon-self-sustaining-initiatives.png'; break;
	case 'TBD': $icon = 'images/icon-tbd.png'; break;
	case 'Transportation': $icon = 'images/icon-transportation.png'; break;
	case 'Utilities': $icon = 'images/icon-utilities.png'; break;
	case 'Other': $icon = ''; break;
}
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
	
	
	
	//Modal Voting & Comment Form
	var triggers = $(".modalInput").overlay({
	
		// some mask tweaks suitable for modal dialogs
		mask: {
			color: '#ffffff',
			loadSpeed: 200,
			opacity: 0.9
		},
		
		closeOnClick: false
	});


	//Submit a comment when a user hits enter in the comment box
	$('#add-comment').keydown(function (e) {
        if (e.keyCode == 13) {
			//Submit the comment
			sendComment();
		}
    });


});

//AJAX Form Submissions
function sendFormVote() {
	//Close the modal dialog
	$(".modalInput").eq(1).overlay().close();

	//Submit the form in the background
	$('#frmVote').ajaxForm( {
		target: '#messages', 
		success: function(msg) { 
			showNotification({
				message: "Your vote has been recorded.",
				type: "warning",
				autoClose: true,
				duration: 5
			});

			//Pause while we display the message, then reload the page
			//setTimeout(function() {window.location.href='?tab=view_request&rid=<?php echo $request_id; ?>';},5000);

		} 
	}); 

	//Reload the page

};

function sendComment() {
	//Get the comment text
	comment = $('#add-comment').val();
	cat = "undecided";

	//Empty the comment box
	$('#add-comment').val('');

	$.ajax({
		type: 'POST',
		url: "modules/cc-threads.php",
		data: { action: "create_thread", 
				rid: "<?php echo $request_id; ?>", 
				group_id: "<?php echo $_SESSION['group_id']; ?>", 
				user_id: "<?php echo $_SESSION['user_id']; ?>",
				thread_category: cat,
				thread_content: comment },
		success: function(msg){
			//reload the page
			//window.location.href='?tab=view_request&rid=<?php echo $request_id; ?>';
			$("#reload").load("comments.php", {group_id: <?php echo $_SESSION['group_id']; ?>, user_id: <?php echo $_SESSION['user_id']; ?>, rid: <?php echo $request_id; ?>});
		}
	});
};

function loadComments(){
	$("#reload").load("comments.php", {group_id: <?php echo $_SESSION['group_id']; ?>, user_id: <?php echo $_SESSION['user_id']; ?>, rid: <?php echo $request_id; ?>});	 
};

</script>

<div id="messages"></div>

<!-- user voting dialog -->
<div class="modalform" id="prompt">
	<h2>Vote & Comment</h2>
	
	<!-- input form. you can press enter too -->
	<form method="post" action="modules/cc-accounts.php" id="frmVote">
		<input type="hidden" name="action" value="set_vote">
		<input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
		<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
		<input type="hidden" name="group_id" value="<?php echo $_SESSION['group_id']; ?>">
		<input type="hidden" name="num_members" value="<?php echo $cc_vars['num_members']; ?>">
		<input type="hidden" name="request_amount" value="<?php echo $cc_vars['request_amount']; ?>">

		<p><label for="vote" class="label">Your Vote:</label><br />
		<div style="background-color: #E8E8E8; padding: 4px 0px;">
		<center>
			<input type="radio" name="vote" value="approve" checked /> <span class="text-small">Approve</span>
			<input type="radio" name="vote" value="disapprove" /> <span class="text-small">Disapprove</span>
			<input type="radio" name="vote" value="discuss" /> <span class="text-small">More Discussion</span>
		</center>
		</div>
		</p>		
		<p><label for="vcomment" class="label">Comments:<span class="text-tiny">(required)</span></label> <br />
			<textarea rows=10 id="vcomment" name="vcomment" class="input-dark input-300 required"></textarea>
		</p>

		<center>
			<button type="submit" class="modalInput" onClick="sendFormVote();"> Submit </button>
			<button type="button" class="close"> Cancel </button>
		</center>
	</form>
	<br />
	
</div>

<h1>
	<?php echo $request_info['title']; ?>
	<?php if ($request_info['ownerid'] == $_SESSION['user_id']) { ?>
		<a href="<? echo $_SESSION['home_url']; ?>?tab=edit_request&rid=<?php echo $request_info['requestid']; ?>" class="text-small text-lime" style="margin-left: 10px;">Edit Request</a>
	<?php } /*end if*/ ?>
</h1>
<div id="view-thread">
<div class="float-lt">
	<div id="view-thread-left" class="float-lt">
		<?php if($request_info['status']=='closed'){ ?>
			<h2 style="margin-top: 0px;">Expired on <?php echo date('F j, Y',strtotime($request_info['expiration'])); ?></h2>
		<?php } else { ?>
			<h2 style="margin-top: 0px;">Expires on <?php echo date('F j, Y', strtotime($request_info['expiration'])); ?></h2>
		<?php } /*end if*/ ?>
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
		</div><!-- .view-thread-left-top -->
			

	</div><!-- .view-thread-left -->

	<div class="float-lt">
		<center><span class="text-green text-huge bold" style="width: 158px; height: 44px; margin: 0px auto;"><?php echo $request_info['status']; ?></span></center>
	
		<div style="width: 200px; margin-top: 20px;">
			<span class="float-lt text-med bold"><?php echo $vote_totals['no'];?></span>
			<span class="float-rt text-med bold"><?php echo $vote_totals['yes'];?></span>

			<div class="clear" style="height: 2px;"></div>
			<div id="slider"></div>

			<div class="clear" style="height: 2px;"></div>
			<img src="images/img-thumb-down.png" class="float-lt">
				<span class="text-small" style="margin-left: 35px;">Awaiting <?php echo count($members) - ($vote_totals['total'] + 1); ?> responses</span>
			<img src="images/img-thumb-up.png" class="float-rt">
		</div>
	</div><!-- .float-lt -->

	<div class="clear"></div>
	<div id="view-thread-left-bottom">
		<span class="text-med bold">Description:</span><br />
		<p class="text-med"><?php echo $request_info['content']; ?></p>
	</div><!-- .view-thread-left-bottom -->
</div><!-- .float-lt -->

	<div id="view-thread-right" class="float-rt">
		<center>
		<button type="button" class="btn-grey text-huge bold" style="width: 158px; height: 44px; margin: 0px auto;">
			$<?php if($request_info['amount']==0){ echo "0"; } else { echo $request_info['amount']; } ?>
		</button>
		</center>

		<div class="clear" style="height: 10px;"></div>
		<img class="float-lt" src="<?php echo $icon; ?>" style="width: 50px;" onMouseOver="$('#cat-name').css('visibility', 'visible');" onMouseOut="$('#cat-name').css('visibility', 'hidden');">
			<span id="cat-name" class="text-med" style="visibility: hidden;"><?php echo $request_info['category'];?></span><br />

		<div class="clear" style="height: 10px;"></div>
		<span class="text-med bold float-lt" style="width: 75px;">Recipient: </span><br />
			<div class="float-lt">
			<?php if($recipient_info['fname'] == ''){ echo "<span class='text-med'>To be defined.</span>"; } else { ?>

			<span class="text-med"><?php echo $recipient_info['fname'] . ' ' . $recipient_info['lname'];?></span><br />
			<span class="text-med"><?php echo $recipient_info['address1'];?></span><br />
			<?php if($recipient_info['address2'] != ''){ ?>
				<span class="text-med"><?php echo $recipient_info['address2'];?></span><br />
			<?php } ?>
			<span class="text-med"><?php echo $recipient_info['city'] . " " . $recipient_info['state'] . " " . $recipient_info['zip'];?></span><br />
			<?php if($recipient_info['country'] != ''){ ?>
				<span class="text-med"><?php echo $recipient_info['country'];?></span><br />
			<?php } ?>
			<?php } ?>
			</div>


		<div class="clear" style="height: 30px;"></div>
		<?php if ($request_info['ownerid'] != $_SESSION['user_id']){ ?>
			<?php if($request_info['status']=='open'){ ?>
				<?php if($has_voted == "false") { ?>
					<center><button class="btn-green modalInput" rel="#prompt">Weigh-In</button></center>
				<?php } /*end if*/ ?>
			<?php } /*end if*/ ?>
		<?php } /*end if*/ ?>
		<div class="clear" style="height: 10px;"></div>
	</div><!-- .view-thread-right -->

	<?php GET_THREADS($cc_vars); ?>
	<div class="clear" style="height: 20px;"></div>
	<div id="comments">
	<div id="reload">
		<i class="icon-refresh float-lt" onClick="loadComments();"></i><a class="float-lt" style="margin-left: 6px;" onClick="loadComments();">Refresh Comments</a>
		<div id="tabs">
			<ul>
				<li><a href="#tab-1" class="text-small">All Comments <span id="all">0</span></a></li>
				<li><a href="#tab-2" class="text-small">Approved <span id="approve">0</span></a></li>
				<li><a href="#tab-3" class="text-small">Disapproved <span id="disapprove">0</span></a></li>
				<li><a href="#tab-4" class="text-small">More Discussion <span id="discuss">0</span></a></li>
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
		</div><!-- .reload -->

		<div class="clear" style="height: 5px;"></div>
		<div class="flaot-lt">
			<textarea id="add-comment" name="add-comment" maxrows="1" minlength="2" class="input-dark" style="width: 790px; color: #999;" onClick="$(this).text('');">Write a comment...</textarea>
		</div><!-- .float-lt -->
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

<div class="clear" style="height: 50px;"></div>