<?php

//Include modules
include_once 'modules/cc-groups.php';
include_once 'modules/cc-accounts.php';

//Check for the request method
switch($_SERVER['REQUEST_METHOD'])
{
case 'GET': $the_request = &$_GET; break;
case 'POST': $the_request = &$_POST; break;
default: $the_request = &$_POST; break;
}

//Get the page parameters passed through GET
$group_id = strip_tags($the_request['gid']);


//Run the functions needed for this page.
$cc_vars['group_id'] = $group_id;
$cc_vars['user_id'] = $_SESSION['user_id'];

GROUP_DETAIL($cc_vars);
LIST_MEMBERS($cc_vars);
GET_REQUESTS($cc_vars);
GET_MEMBER_REQUESTS($cc_vars);

/* Store some of the information for later. */
if (empty($join_requests)) {
	//do nothing
} else {
	foreach ($join_requests as $join_request) {
		if ($join_request['userid'] == $_SESSION['user_id']) {
			$request_made = true;
		} //end if
	} //end foreach
} //end if

?>

<script type="text/javascript">
$(document).ready(function() {
	//Format grid
	//$('#Grid1').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });

	//Covenant popup
	//$('button[rel]').overlay();

	//Hover fix. Bind the hover event to image elements.
	$('img[id]').hover(function() {});
});

function sendJoinRequest() {
	$.ajax({
		type: 'POST',
		url: "modules/cc-groups.php",
		data: { action: "join_group", group_id: <?php echo $cc_vars['group_id']; ?>, user_id: <?php echo $_SESSION['user_id']; ?> },
		success: function(data) { 
			showNotification({
				message: "Group request sent.",
				type: "warning",
				autoClose: true,
				duration: 5
			});
			//Remove the button so the user can't resubmit the request.
			$('#btnJoin').remove();
		},
		error:function (xhr, ajaxOptions, thrownError){
			showNotification({
				message: thrownError,
				type: "error",
				autoClose: true,
				duration: 5
			});
        }    
	}); 
}

</script>

<h1><?php echo $group_info['name']; ?></h1>

<div id="mygroup-top">
	<div class="float-lt">
		<h2>Members: <?php echo count($members);?></h2>
		<div class="float-lt">
		<?php 
			if (empty($members)) {
				//Display message
				echo 'No group members were found.';
			} else {
				//Return the results
				$iRow = 1;
				foreach ($members as $member) {
		?>		
		<img id="<?php echo 'thumb' . $member['requestid']; ?>" src="<?php echo $thumb_dir . $member['thumb']; ?>" class="thumb-46x46 float-lt" onMouseOver="showHovercard('<?php echo 'thumb' . $member['requestid']; ?>','<?php echo 'badge' . $member['requestid']; ?>');"></img>
			<!-- tooltip -->
			<div id="<?php echo 'badge' . $member['user_id']; ?>" class="tooltip float-lt">
				<div class="tooltip-top float-lt">
					<div class="float-lt" style="margin-top: 10px;">
						<img src="<?php echo $thumb_dir . $member['thumb']; ?>" class="thumb-46x46 float-lt">
					</div>
					<div class="float-lt" style="margin-left: 4px;">
						<h3><?php echo $member['fname'] . " " . $member['lname']; ?></h3>
						<p><strong>Member since: </strong><?php echo $member['created']; ?><br />
						<strong>Location: </strong><?php echo $member['city']; ?>, <?php echo $member['state']; ?><br />
						<?php echo $member['country']; ?><p/>
					</div>
				</div>
	
				<div class="clear"></div>
				<div class="tooltip-bottom">
					<?php if ($member['share_fb']==1){; ?>
					<button class="icon-fb-32x32" onClick="window.location.href='<?php echo 'http://' . $member['facebook']; ?>';" alt="<?php echo $member['facebook']; ?>"></button>
					<?php } ?>
					<?php if ($member['share_twit']==1){; ?>
					<button class="icon-twit-32x32" onClick="window.location.href='<?php echo 'http://' . $member['twitter']; ?>';"></button>
					<?php } ?>
					<button class="icon-email-32x32" onClick="window.location.href=mailto:'<?php echo $member['email']; ?>';"></button>
				</div>
				<div class="clear"></div>
			</div><!-- .row -->
			<!-- .tooltip -->
			<?php			
				//Keep the thumbs to three per line
				if ($iRow == 3) {
					echo '</div>';
					echo '<div class="clear"></div>';
					echo '<div class="float-lt">';
					$iRow = 1;
				} else {
					$iRow++;
				}
			?>
			<?php } /*end foreach*/ ?>
		<?php } /*end if*/ ?>	
		</div><!--.float-lt-->
		<!--<div class="thumb-46x46 float-lt"><div style="text-align: center; margin: 10px auto 0px auto;"><a href="#" class="text-small underline">view all</a></div></div>-->
			<div class="clear" style="height:4px;"></div>
		<!--<button id="btnCovenant" class="btn-grey2 float-lt" rel="#covenant">Group Covenant</button>-->
			<div class="clear" style="height:4px;"></div>
	</div>
	<div class="float-lt" style="margin-left: 4px; width: 300px; text-align: center;">
		<div style="margin: 25px auto 0px 15px;">
		<button class="btn-grey text-big bold">$<?php echo number_format(intval($group_info['funds_shared']));?><br /><span class="text-med normal">Shared</span></button>
		<button class="btn-grey text-big bold">$<?php echo number_format(intval($group_info['funds_available']));?><br /><span class="text-med normal">Available</span></button>
		</div>
	</div>
	<div class="float-lt" style="margin-left: 4px; width: 300px; text-align: center;">
		<div>
			<?php if ($_SESSION['group_id'] == 0){ ?>
				<?php if($request_made) { ?>
					<button class="btn-grey2">You have requested to join this group</button>
				<?php } else { ?>
					<button type="button" id="btnJoin" class="btn-green" style="padding: 15px; margin-top: 25px;" onclick="sendJoinRequest();" >Request to Join</button>
				<?php } /*end if*/ ?>
			<?php } else { ?>
				<!--<button class="btn-grey2 text-big" style="padding: 15px; margin-top: 25px;" onclick="window.location.href='?tab=mygroup'">You are already in a group</button>-->
			<?php } /*end if*/ ?>
		</div>
	</div>
	<div class="clear" style="height:4px;"></div>
	<div id="covenant" class="modal">
		<h3><strong>Group Covenant</strong></h3>
		<p class="text-med"><?php echo $group_info['covenant']; ?></p>
	</div><!-- .covenant -->

	<div class="float-lt" id="messages">
	</div>
</div><!-- .mygroup-top -->

<div class="clear"></div>

<div id="mygroup-bottom" class="height250">
<table id="Grid1" class="table table-bordered table-striped table-hover sortable">
	<thead>
		<tr>
			<th></th>
			<th></th>
			<th>Title</th>
			<th>Time Remaining</th>
			<th>Status</th>
			<th>Created</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			if (empty($requests)) {
				//Display message
				echo '<tr class="inactive">';
				echo	'<td></td>';
				echo	'<td></td>';
				echo	'<td>No requests.</td>';
				echo	'<td></td>';
				echo	'<td></td>';
				echo	'<td></td>';
				echo '</tr>';
			} else {
				//Return the results
				foreach ($requests as $request) {
		?>
		<tr class="active" id="<?php echo $request['requestid']; ?>">
			<td>
				<img id="<?php echo 'img' . $request['requestid']; ?>" src="<?php echo $thumb_dir . $request['thumb']; ?>" class="thumb-16x16" onMouseOver="showHovercard('<?php echo 'img' . $request['requestid']; ?>','<?php echo 'tip' . $request['requestid']; ?>');">
			</td>
			<td><div class="star-off"></div></td>
			<td><?php echo $request['title']; ?></td>
			<td>
				<?php 
					//Dispay the time remaining on this request
					$datetime1 = new DateTime($request['expiration']);
					$datetime2 = new DateTime("now");
					$time_left = $datetime1->diff($datetime2);
					echo $time_left->format('%R%a days');
				?>
			</td>
			<td><?php echo $request['status']; ?></td>
			<td><?php echo $request['modified']; ?></td>
		</tr>

		<!-- tooltip -->
		<div id="<?php echo 'tip' . $request['requestid']; ?>" class="tooltip float-lt">
			<div class="tooltip-top float-lt">
				<div class="float-lt" style="margin-top: 10px;">
					<img src="<?php echo $thumb_dir . $request['thumb']; ?>" class="thumb-46x46 float-lt">
				</div>
				<div class="float-lt" style="margin-left: 4px;">
					<h3><?php echo $request['fname'] . " " . $request['lname']; ?></h3>
					<p><strong>Member since: </strong><?php echo $request['created']; ?><br />
					<strong>Location: </strong><?php echo $request['city']; ?>, <?php echo $request['state']; ?><br />
					<?php echo $request['country']; ?><p/>
				</div>
			</div>

			<div class="clear"></div>
			<div class="tooltip-bottom">
				<?php if ($request['share_fb']==1){; ?>
				<button class="icon-fb-32x32" onClick="window.location.href='<?php echo 'http://' . $request['facebook']; ?>';" alt="<?php echo $request['facebook']; ?>"></button>
				<?php } ?>
				<?php if ($request['share_twit']==1){; ?>
				<button class="icon-twit-32x32" onClick="window.location.href='<?php echo 'http://' . $request['twitter']; ?>';"></button>
				<?php } ?>
				<button class="icon-email-32x32" onClick="window.location.href=mailto:'<?php echo $request['email']; ?>';"></button>
			</div>
			<div class="clear"></div>
		</div><!-- .row -->
		<!-- .tooltip -->

			<?php } /*end foreach*/ ?>
		<?php } /*end if*/ ?>	
	</tbody>
</table>
</div>

<div class="clear" style="height: 50px;"></div>
