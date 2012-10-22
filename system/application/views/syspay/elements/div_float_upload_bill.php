<div class='table' id="lay5_block" style="width:400px; position:fixed; z-index: 1000; display:none; top:200px;">
	<form class='admin-inside' action="/client/addBillFoto/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td>
					<input type="file" name="userfile" size="40">
					<input type="hidden" name="order_id" id="order_id">
				</td>
			</tr>			
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'><div>
							<input type='submit' name="add" value='Сохранить' />
						</div></div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>

<script type="text/javascript">

	var fmclick = 0;
	function lay5(){
		
		var $offsetLeft	= window.innerWidth / 2 - 300;
		
		$('#lay5_block').css({
			'left'	:$offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#lay5_block').fadeIn("slow");
		
		if (!fmclick){
			fmclick = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay5_block').fadeOut("slow");
			})
		}
	}
	
	function uploadBillFoto(order_id){
		document.getElementById('order_id').value = order_id;
		lay5();
	}
</script>