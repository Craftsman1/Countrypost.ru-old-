<div class='content table' id="balance_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Баланс по посредникам</h3>
	</center>
	<? View::show('/client/elements/payments/manager_filter'); ?>
	<div id='balance'></div>
	<div class='float'>
		<div class='submit float-left'>
			<div>
				<input type='button'
					   name="add"
					   value='OK'
					   onclick="$('#lay').click();"/>
			</div>
		</div>
	</div>
</div>
<script>
	var balance_click = 0;

	function showBalanceWindow()
	{
		var offsetLeft = window.innerWidth / 2 - 280;

		$('#balance_block').css({
			'left' : offsetLeft
		});

		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#balance_block').fadeIn("slow");
		
		if ( ! balance_click)
		{
			balance_click = 1;

			$('#lay').click(function() {
				$('#lay').fadeOut("slow");
				$('#balance_block').fadeOut("slow");
			});
		}

		$('a#reset_balance').click();
	}
</script>