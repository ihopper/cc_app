<?php
	echo '<script type="text/javascript">';
		//Mark the current tab as active
		echo 'document.getElementById("admin-threads").className = "admin-menu-on";';
		echo 'document.getElementById("admin").className = "menu-tab-on";';
	echo '</script>';


	//Include modules
	include_once 'modules/cc-groups.php';
	include_once 'modules/cc-accounts.php';

	//Run functions for this page
	GET_GROUPS($cc_vars);

?>
<script type="text/javascript">
$(document).ready(function() {
	//$('#tbl-admin-threads').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });	
});

function showRequest(id) {
	//Load and display the group page
	$("#request-detail").load('view_request.php?rid=' + id);

};
</script>

<h1>Requests</h1>
<hr>

<div class="height250">
<table id="tbl-admin-threads" class="table table-bordered table-striped table-hover sortable">
	<thead>
		<tr>
			<th>Group Name</th>
			<th>Thread Title</th>
			<th>Amount</th>
			<th>Expiration</th>
			<th>Status</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if (empty($groups)) {
			//Display message
			echo 'There are no groups currently listed in Common Change. Go to your user profile to create the first one.';
		} else {
			//Return the results
			foreach($groups as $group) {
				//Get the group requests
				$cc_vars['group_id'] = $group['groupid'];
				GET_REQUESTS($cc_vars);
				if (!empty($requests)) {
					foreach($requests as $request) {	
		?>
					<tr id="row<?php echo $request['requestid']; ?>" class="active">
						<td nowrap><?php echo $group['name']; ?></td>
						<td><a onClick="showRequest('<?php echo $request['requestid']; ?>');"><?php echo $request['title']; ?></a></td>
						<td><?php echo $request['amount']; ?></td>
						<td nowrap><?php echo $request['expiration']; ?></td>
						<td><?php echo $request['status']; ?></td>
						<?php if ($request['status'] == 'approved') { ?>
							<td>
								<button type="button" onClick="payRequest('<?php echo $request['requestid']; ?>');" class="btn-green text-small">Pay/Close</button>
								<button type="button" onClick="delRequest('<?php echo $request['requestid']; ?>');" class="btn-green text-small">Delete</button>
							</td>
						<?php } else { ?>
							<td><button type="button" onClick="delRequest('<?php echo $request['requestid']; ?>');" class="btn-green text-small">Delete</button></td>
						<?php } /*end if*/ ?>
					</tr>
					<?php } /*end foreach*/ ?>
				<?php } /*end if*/ ?>
			<?php } /*end foreach*/ ?>
		<?php } /*end if*/ ?>	
	</tbody>
</table><!-- .tbl-admin-email -->
</div><!-- .height 250 -->


<div class="clear" style="height: 20px;"></div>

<h1>Request Details</h1>
<hr>

<div id="request-detail"></div>

<!--<div id="view-thread-left" class="float-lt">
	<h2>Group: &nbsp;</h2>
	<span class="text-med"><strong>Created: </strong>&nbsp;</span>&nbsp; <span class="text-med"><strong>Category: </strong>[Category]</span>
	<div id="view-thread-left-top" class="float-lt">
		<div class="float-lt thumb-48x48" style="margin-top: 10px;">
			<img src="">		
		</div>
		<div class="float-lt">
			<p class="text-small">Presented by [name]</p>
			<p class="text-small">[City], [State], [Country]</p>
			<p class="text-small">Posted on &nbsp;</p>
		</div>
	</div><!-- .view-thread-left-top -->

<!--	<div class="clear"></div>
	<div id="view-thread-left-bottom">
		<span class="text-med">Description:</span><br />
		<p class="text-small">The thread description goes here, and scrolls when content exceeds the available space.</p>
	</div><!-- .view-thread-left-bottom -->
<!--</div><!-- .view-thread-left -->


<!--<div style="width: 250px;" class="float-rt"><h3>Last day to decide: [Day of week]</h3></div>
<!--<div id="view-thread-right" class="float-rt">
	<div class="btn-grey" style="width: 158px; height: 44px; margin: 0px auto;"><center><br />$[amount]</center></div>
	<p class="text-med">[Recipient Name]</p>
	<p class="text-med">[Recipient Address]</p>
	<p class="text-med">[City, State]</p>
	<p class="text-med">[Country]</p>
	<br />
	<center><button class="btn-green">Visit Thread Page</button></center>
</div><!-- .view-thread-right -->

<div class="clear" style="height: 50px;"></div>

