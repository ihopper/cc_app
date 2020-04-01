<?php
include_once 'modules/cc-threads.php';


//Check for the request method
switch($_SERVER['REQUEST_METHOD'])
{
case 'GET': $the_request = &$_GET; break;
case 'POST': $the_request = &$_POST; break;
default: $the_request = &$_POST; break;
}

$request_id = strip_tags($the_request['rid']);


//Assign variables
$cc_vars['group_id'] 	= strip_tags($the_request['group_id']);
$cc_vars['user_id'] 	= $_SESSION['user_id'];
$cc_vars['request_id'] 	= $request_id;

?>
<?php GET_THREADS($cc_vars); ?>
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

<?php
	echo "<script type='text/javascript'>";
		echo "$('#all').text('(" . $iAll . ")');";
		echo "$('#approve').text('(" . $iApproved . ")');";
		echo "$('#disapprove').text('(" . $iDisapproved . ")');";
		echo "$('#discuss').text('(" . $iDiscuss . ")');";
	echo "</script>";
?>

<script type="text/javascript">
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
</script>