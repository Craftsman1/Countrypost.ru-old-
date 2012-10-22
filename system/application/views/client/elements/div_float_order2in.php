<div class='table' id="lay4_block" style="width:400px; position:fixed; z-index: 1000; display:none; top:200px;">
	<form class='admin-inside' action="/syspay/addOrder2In/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td>Сумма:</td>
				<td><input type="text" name="amount" onkeyup="countAmount(this.value)" />$</td>
			</tr>
			<tr>
				<td>Комиссия:</td>
				<td><span id="tax">0</span>$</td>
			</tr>
			<tr>
				<td>Итого:</td>
				<td><span id="total">0</span>$</td>
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
	function lay4(){
		
		var $offsetLeft	= window.innerWidth / 2 - 300;
		
		$('#lay4_block').css({
			'left'	:$offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#lay4_block').fadeIn("slow");
		
		if (!fmclick){
			fmclick = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay4_block').fadeOut("slow");
			})
		}
	}
	
	
	function countAmount($amount){
		
		$tax	= $amount * <?=BM_IN_TAX?> / 100;
		$total	= $amount * 1 + $tax;
		
		$('#tax').html($tax);
		$('#total').html($total);
		
	}
</script>