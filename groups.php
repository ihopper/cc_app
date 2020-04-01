<?php
//Mark the current tab as active
echo '<script type="text/javascript">'
   , 'document.getElementById("groups").className = "menu-tab-on";'
   , '</script>';

//Include modules
include_once 'modules/cc-groups.php';

//Run the functions needed for this page.
//$cc_vars['sort'] = "name";

GET_GROUPS($cc_vars);


/* Store some of the information for later. */


?>

<script type="text/javascript">
$(document).ready(function() {
	//Hover fix. Bind the hover event to image elements.
	$('img[id]').hover(function() {});
});
</script>

<div id="browse-groups" class="text-small">
	<?php 
		if (empty($groups)) {
			//Display message
			echo 'There are no groups in Common Change that match your search. Please try your search again.';
		} else {
			//Return the results
			foreach ($groups as $group) {
			$cc_vars['group_id'] = $group['groupid'];
			$cc_vars['sort'] = '';
			LIST_MEMBERS($cc_vars);
	?>
	<div class="browse-group-box" id="<?php echo $group['groupid'];?>">
		<div class="browse-group-thumbs">
			<div class="float-lt">
			<?php 
				$iCount = 1; //Number of thumbnails
				$iRow = 1; //Number of rows
				if (empty($members)) {
					//Display message
					echo 'No group members were found.';
				} else {
					//Return the results
					$num_members = 0;
					$num_members = count($members) ;
					foreach ($members as $member) {
						if($iCount == 10) break; //Limit the output to fit our UI dimensions.
						$iCount++;
			?>		
			<img id="<?php echo 'thumb' . $member['userid']; ?>" src="<?php echo $thumb_dir . $member['thumb']; ?>" class="thumb-25x25 float-lt" onMouseOver="showHovercard('<?php echo 'thumb' . $member['userid']; ?>','<?php echo 'badge' . $member['userid']; ?>');"></img>
				<!-- tooltip -->
				<div id="<?php echo 'badge' . $member['userid']; ?>" class="tooltip float-lt">
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
						<button class="icon-fb-32x32" onClick="window.location.href='<?php echo $member['facebook']; ?>';" alt="<?php echo $member['facebook']; ?>"></button>
						<?php } ?>
						<?php if ($member['share_twit']==1){; ?>
						<button class="icon-twit-32x32" onClick="window.location.href='<?php echo 'http://www.twitter.com/' . $member['twitter']; ?>';"></button>
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

		<?php 
			$iMembers = count($members);
			while ($iMembers < 9) {
				echo '<img src="" class="thumb-25x25 float-lt">';

				//Keep the thumbs to three per line
				if ($iRow == 3) {
					echo '</div>';
					echo '<div class="clear" style="height: 3px;"></div>';
					echo '<div class="float-lt">';
					$iRow = 1;
				} else {
					$iRow++;
				}
				$iMembers++;
			} //end while

			echo '</div>';
		?>			
		</div><!-- .browse-group-thumbs -->

		<div class="browse-group-details">
			<strong><?php echo $group['name'];?></strong><br />
			<?php echo $group['city'] . ", " . $group['state'] . ", " . $group['country'];?><br />
			Members: <?php echo $num_members ;?><br /><br />

		<?php if ( $group['groupid'] == $_SESSION['group_id']) { ?>
			<button class="btn-green text-small" onClick="window.location.href='?tab=mygroup'">Go to Group</button>
		<?php } else { ?>
			<button class="btn-green text-small" onClick="window.location.href='?tab=view_group&gid=<?php echo $group['groupid'];?>'">View Group Details</button>
		<?php } /*end if*/ ?>
		</div><!-- .browse-group-details -->

		<div class="clear"></div>
		<div class="browse-group-desc" style="margin-top: 4px;">
			<strong>Description: </strong><br />
			<p><?php echo $group['description'];?></p>
		</div>

	</div><!-- .browse-group-box -->
			<?php } /*end foreach*/ ?>
	<?php } /*end if*/ ?>

</div><!-- .browse-groups -->

<div class="clear" style="height: 50px;"></div>