<div class='table' id="lay2_block" style="width:400px; position:absolute; z-index: 1000; display:none; top:390px; left:290px;">
</div>
	
<script type="text/javascript">

	var fmclick = 0;
	function lay2(credentials){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#lay2_block').html('<h2>Реквизиты платежа:</h2>' + credentials).fadeIn("slow");
		
		if (!fmclick){
			fmclick = 1;
			$('#lay,#lay2_block').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay2_block').fadeOut("slow");
			})
		}
	}
</script>