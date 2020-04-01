<?php
	echo '<script type="text/javascript">';
		//Mark the current tab as active
		echo 'document.getElementById("admin-groups").className = "admin-menu-on";';
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
	//$('#tbl-admin-groups').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'row-dark', autoShow: true });
});

function showGroup(id) {
	//Load and display the group page
	$("#group-detail").load('view_group.php?gid=' + id);

};
</script>

<h1>Groups</h1>
<hr>

<div class="height250">
<table id="tbl-admin-groups" class="table table-bordered table-striped table-hover sortable">
	<thead>
		<tr>
			<th>Group Name</th>
			<th>Members</th>
			<th>Requests</th>
			<th>Balance</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if (empty($groups)) {
			//Display message
			echo 'There are no groups currently listed in Common Change. Go to your user profile to create the first one.';
		} else {
			//Return the results
			foreach ($groups as $group) {
				$cc_vars['group_id'] = $group['groupid'];
				$cc_vars['sort'] = '';
				
				//Count the members
				LIST_MEMBERS($cc_vars);
				$num_members = 0;
				$num_members = count($members);
				
				//Count the need requests
				GET_REQUESTS($cc_vars);
				$num_requests = 0;
				$num_requests = count($requests);
				
				//Get group details
				GROUP_DETAIL($cc_vars);
		?>
		<tr id="row<?php echo $group['groupid']; ?>" class="active">
			<td><a onClick="showGroup('<?php echo $group['groupid']; ?>');"><?php echo $group['name']; ?></a></td>
			<td><?php echo $num_members; ?></td>
			<td><?php echo $num_requests; ?></td>
			<td><?php echo $group_info['funds_available']; ?></td>
			<?php if($group['status'] == 'active') { ?>
				<td><button type="button" id="<?php echo $group['groupid']; ?>" type="button" onClick="grpStatus('<?php echo $group['groupid']; ?>', 'suspended');" class="btn-grey2 text-small">Suspend</button></td>
			<?php } else if($group['status'] == 'suspended') { ?>
				<td><button type="button" id="<?php echo $group['groupid']; ?>" type="button" onClick="grpStatus('<?php echo $group['groupid']; ?>', 'active');" class="btn-green text-small">Activate</button></td>
			<?php } /*end if*/ ?>
			<td><button type="button" class="btn-green" onClick="delGroup('<?php echo $group['groupid']; ?>');">Delete</button></td>
		</tr>
			<?php } /*end foreach*/ ?>
		<?php } /*end if*/ ?>	
		
	</tbody>
</table><!-- .tbl-admin-email -->
</div><!-- .height 250 -->


<div class="clear" style="height: 20px;"></div>

<h1>Group Details</h1>
<hr>
<div id="group-detail"></div>

<!--<h1>Group Details</h1>
<hr>
<div id="mygroup-top">
	<div class="float-lt">
		<h2>Group: &nbsp;</h2>
		<span class="text-med"><strong>Created: </strong>&nbsp;</span><br />
		<br />
		<span class="text-med"><strong>Members: </strong>&nbsp;</span><br />
		<img src="" class="thumb-46x46 float-lt"></img>
		<img src="" class="thumb-46x46 float-lt"></img>
		<img src="" class="thumb-46x46 float-lt"></img>
		<img src="" class="thumb-46x46 float-lt"></img>
			<div class="clear" style="height:4px;"></div>
		<img src="" class="thumb-46x46 float-lt"></img>
		<img src="" class="thumb-46x46 float-lt"></img>
		<img src="" class="thumb-46x46 float-lt"></img>
		<div class="thumb-46x46 float-lt"><div style="text-align: center; margin: 10px auto 0px auto;"><a href="#" class="text-small underline">view all</a></div></div>
	</div>
	<div class="float-lt" style="margin-left: 100px; margin-top: 10px; width: 200px;">
		<span class="text-med"><strong>Status: </strong>[Active/Suspended]</span><br />
		<div class="clear" style="height: 10px;"></div>
		<br /><br />
		<span class="text-med"><strong>Balance: </strong>&nbsp;</span><br />
		<span class="text-med"><strong>Shared: </strong>&nbsp;</span><br />
		<br /><br />
		<button class="btn-green">Visit Group Page</button>
	</div>
	<div class="float-lt"></div>
</div><!-- .mygroup-top -->



<div class="clear" style="height: 50px;"></div>

