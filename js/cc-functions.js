//Function to change donation status (mark as approved / declined)
function donationStatus(donationid, status, amount) {
	rowNum = "#grpDonation"+donationid;
	$(rowNum).effect("highlight", {}, 1000);
	$.ajax({
		type: 'POST',
		url: "modules/cc-financial.php",
		data: { action: "approve_donation", donation_id: donationid, donation_status: status, donation_amount: amount },
		success: function(msg){
			//reload the page
			window.location.href='?tab=admin&page=finance';
		}
	});
}
 
//Function to change group status
function grpStatus(groupid, status) {
	rowNum = "#row"+groupid;
	$(rowNum).effect("highlight", {}, 1000);
	$.ajax({
		type: 'POST',
		url: "modules/cc-groups.php",
		data: { action: "suspend_group", group_id: groupid, status: status },
		success: function(msg){
			//reload the page
			window.location.href='?tab=admin&page=groups';
		}
	});
}

//Function to delete a group
function delGroup(groupid) {
	var r=confirm("Are you sure you want to delete this group?");
	if (r==true) {
		rowNum = "#row"+groupid;
		$(rowNum).effect("highlight", {}, 1000);
		$.ajax({
			type: 'POST',
			url: "modules/cc-groups.php",
			data: { action: "delete_group", group_id: groupid},
			success: function(msg){
				$(rowNum).fadeOut("slow");
				//reload the page
				window.location.href='?tab=admin&page=groups';
			}
		});
	 } else {
  	//Do nothing
  } 
}

//Function to delete a request
function delRequest(requestid) {
	var r=confirm("Are you sure you want to delete this request?");
	if (r==true) {
		rowNum = "#row"+requestid;
		$(rowNum).effect("highlight", {}, 1000);
		$.ajax({
			type: 'POST',
			url: "modules/cc-accounts.php",
			data: { action: "del_request", request_id: requestid},
			success: function(msg){
				$(rowNum).fadeOut("slow");
				//reload the page
				window.location.href='?tab=admin&page=threads';
			}
		});
	 } else {
  	//Do nothing
  } 
}

//Function to mark a request as paid
function payRequest(requestid) {
	var r=confirm("Are you sure you want to mark this request paid?");
	if (r==true) {
		rowNum = "#row"+requestid;
		$(rowNum).effect("highlight", {}, 1000);
		$.ajax({
			type: 'POST',
			url: "modules/cc-accounts.php",
			data: { action: "pay_request", request_id: requestid},
			success: function(msg){
				//reload the page
				window.location.href='?tab=admin&page=threads';
			}
		});
	 } else {
  	//Do nothing
  } 
}


//Function to remove a user from a group.
//Needs error handling for failure.
function delMember(memberid, groupid) {
	rowNum = "#row"+memberid;
	$(rowNum).effect("highlight", {}, 1000);
	$.ajax({
		type: 'POST',
		url: "modules/cc-accounts.php",
		data: { action: "del_member", group_id: groupid, member_id: memberid },
		success: function(msg){
			$(rowNum).fadeOut("slow");
		}
	});
}

//Function to update a member's role within a group.
function updateRole(memberid, groupid, obj) {
	rowNum = "#row"+memberid;	
	mrole = obj.options[obj.selectedIndex].text;

	$.ajax({
		type: 'POST',
		url: "modules/cc-accounts.php",
		data: { action: "update_role", group_id: groupid, member_id: memberid, role: mrole },
		success: function(msg){
			$(rowNum).effect("highlight", {}, 1000);
		}
	});
}

//Function to flag/unflag an item as important.
	function star(obj, itemID) {
		//Update the database
		$.ajax({ 
			type: 'POST',
			url: "functions.php",
			data: { action: "star", id: itemID },
			success: function(msg){
         	
			}
		})
		// or use this-> 
		//$.post("functions.php", { action: "star", id: itemID },
		//	function(data) {
	    //		alert("Data Loaded: " + data);
		//   	} );

		//Change the image
		if (obj.className == "star-on") {
			obj.className = "star-off";
		} else if (obj.className == "star-off") {
			obj.className = "star-on";
		}
	};

//Function to toggle ascending/descening sorts
	function toggle_sort(obj) {
		if (obj.className == "sort-desc") {
			obj.className = "sort-asc";
		} else if (obj.className == "sort-asc") {
			obj.className = "sort-desc";
		}

	}

//Function to change smiley based on slider value.
	function smile(amnt) {
		
		//Convert the value to int
		//amnt = parseInt(aaa, 10);
		obj = document.getElementById("smiley"); 
		//obj.className = "smiley-sad";
		//Change the image
		if (amnt <= 5) {
			obj.className = "smiley-sad";
		}
		else if(amnt > 5 && amnt <= 9) {
			obj.className = "smiley-neutral";
		}
		else if(amnt > 9 && amnt <= 49) {
			obj.className = "smiley-happy1";
		}
		else if(amnt > 49) {
			obj.className = "smiley-happy2";
		}
		else
		{
			obj.className = "smiley-neutral";
		};

	};

//Function to open a DIV element as a popup dialog box.
	function openDialog(obj) {
		$.fx.speeds._default = 500;
		$(function() {
			$( "#covenant" ).dialog({
				autoOpen: false,
				show: "fold",
				hide: "fold"
			});
	
			$( "#covenant" ).dialog( "open" );
	
		});
	};

//Function to set the active tab on the user account page
	function setAcctTab(obj) {
		if (obj == "acct-det") {
			document.getElementById("acct-det").style.display = "block";
			document.getElementById("acct-fin").style.display = "none";
			document.getElementById("acct-admin").style.display = "none";
		}
		else if (obj == "acct-fin") {
			document.getElementById("acct-det").style.display = "none";
			document.getElementById("acct-fin").style.display = "block";
			document.getElementById("acct-admin").style.display = "none";
		}
		else if (obj == "acct-admin") {
			document.getElementById("acct-det").style.display = "none";
			document.getElementById("acct-fin").style.display = "none";
			document.getElementById("acct-admin").style.display = "block";
		}
		else {
			document.getElementById("acct-det").style.display = "block";
			document.getElementById("acct-fin").style.display = "none";
			document.getElementById("acct-admin").style.display = "none";
		};
	};


//Function to set the active tab on the financials admin page
	function setFinTab(obj) {
		if (obj == "fin-group") {
			document.getElementById("fin-group").style.display = "block";
			document.getElementById("fin-seed").style.display = "none";
			document.getElementById("fin-reports").style.display = "none";
		}
		else if (obj == "fin-seed") {
			document.getElementById("fin-group").style.display = "none";
			document.getElementById("fin-seed").style.display = "block";
			document.getElementById("fin-reports").style.display = "none";
		}
		else if (obj == "fin-reports") {
			document.getElementById("fin-group").style.display = "none";
			document.getElementById("fin-seed").style.display = "none";
			document.getElementById("fin-reports").style.display = "block";
		}
		else {
			document.getElementById("fin-group").style.display = "block";
			document.getElementById("fin-seed").style.display = "none";
			document.getElementById("fin-reports").style.display = "none";
		};
	};

//Function to set the visible elements on the create request page
	function setRequestProcess(obj) {
		if (obj == "brainstorm") {
			document.getElementById("request-type").style.display = "none";
			document.getElementById("request-cat").style.display = "none";
			document.getElementById("request-amount").style.display = "none";
			document.getElementById("request-date").style.display = "none";
			document.getElementById("recipient").style.display = "none";
			document.getElementById("request-desc").style.display = "block";
		}
		else if (obj == "help") {
			document.getElementById("request-type").style.display = "block";
			document.getElementById("request-cat").style.display = "block";
			document.getElementById("request-amount").style.display = "block";
			document.getElementById("request-date").style.display = "block";
			document.getElementById("recipient").style.display = "block";
			document.getElementById("request-desc").style.display = "block";
		}
		else if (obj == "request") {
			document.getElementById("request-type").style.display = "block";
			document.getElementById("request-cat").style.display = "block";
			document.getElementById("request-amount").style.display = "block";
			document.getElementById("request-date").style.display = "block";
			document.getElementById("recipient").style.display = "block";
			document.getElementById("request-desc").style.display = "block";
		}
		else if (obj == "specific-request") {
			document.getElementById("request-type").style.display = "block";
			document.getElementById("request-cat").style.display = "block";
			document.getElementById("request-amount").style.display = "block";
			document.getElementById("request-date").style.display = "block";
			document.getElementById("recipient").style.display = "block";
			document.getElementById("request-desc").style.display = "block";
		}
		else {
			document.getElementById("request-type").style.display = "none";
			document.getElementById("request-cat").style.display = "none";
			document.getElementById("request-amount").style.display = "none";
			document.getElementById("request-date").style.display = "none";
			document.getElementById("recipient").style.display = "none";
			document.getElementById("request-desc").style.display = "none";
		};
	};


//Show / Hide Objects
function showElement(obj) {
	object = "#"+obj;
	$(object).show('fast');
};


//Hover card
function showHovercard(obj, divTip) {
	obj = "#"+obj;
	divTip = "#"+divTip;

	$(obj).tooltip({tip: divTip, offset: [-5, 50], delay: 200});
};

//Display group covenant
function showCovenant() {
	$('button[rel]').overlay();
};

function doUpload() {
	$('button[rel]').overlay({
       onBeforeLoad: function() {
       	// grab wrapper element inside content
       	var wrap = this.getOverlay().find(".contentWrap");
 
        // load the page specified in the trigger
        wrap.load('upload.php');
        }
	});
};
