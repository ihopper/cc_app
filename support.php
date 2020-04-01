

<script type="text/javascript">
$(document).ready(function() {

	//Generate the slider control
	$( "#slider" ).slider({
			value:20,
			min: 0,
			max: 100,
			step: 5,
			slide: function( event, ui ) {
				$( "#amount" ).val( "$" + ui.value );
				smile(ui.value);
			},
			change: function(event, ui) {
				//smile(ui.value);
            }
		});
		$( "#amount" ).val( "$" + $( "#slider" ).slider( "value" ) );

	//Tooltips
	$(".tip").tooltip();

});

function supportCC() {
	//Set the amount
	don_amt = $('#amount').val();

	//Strip the dollar sign
	don_amt = don_amt.substring(1);

	//Redirect
	window.location.href='?tab=payment&amount='+don_amt;
};

</script>

<div style="width: 500px; margin: 60px auto;">
<center><h1>What is Common Change worth to you?</h1></center>

<div class="clear" style="height: 25px;"></div>

<div style="float: left; width: 300px; margin-top: 10px; margin-left: 70px;">
	<div id="slider"></div>
</div>
<div style="float: left; margin-left: 4px;">
	<div id="smiley" class="smiley-neutral"></div>
</div>

<div class="clear" style="height: 25px;"></div>

<center>
<label for="amount" class="label text-lime" style="width: 150px; line-height: 32px; margin-left: 30px;">Monthly Contribution:<a href="#" class="text-green tip" style="border: 1px solid #ccc; padding: 2px; background-color: #f0f0f0;" title="Use the sliding scale or enter any amount into the amount field.">?</a></label>
<input type="text" id="amount" name="amount" style="border:1px solid #ccc; background-color: #f0f0f0; color: #333; height: 22px; width: 50px;" class="float-lt">
<button type="button" class="btn-green text-small" onClick="supportCC();">Support Common Change</button>
</center>
</div>