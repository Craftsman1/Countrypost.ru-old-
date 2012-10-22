<div class='table' id="lay5_block" style="width:400px; position:fixed; z-index: 1000; display:none; top:200px;">
	<h2>Добавление подтверждения</h2>
	<form class='admin-inside' action="/client/addBillFoto/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td colspan="2">
					<input type="file" name="userfile" size="40">
					<input type="hidden" name="order_id" id="order_id">
				</td>
			</tr>			
			<tr class='last-row'>
				<td>
					<div id='delete'></div>
				</td>
				<td>
					<div class='float'>
						<div class='submit'><div>
							<input type='submit' name="add" value='Сохранить' />
						</div></div>
					</div>
				</td>
			</tr>
		</table>
		<p>
			Чтобы мы смогли найти Ваш платеж (перевод), Вам нужно сделать и добавить подтверждение.
			<br />
			<br />
			Подтверждение - это скан, фото или скриншот квитанции.
			<br />
			<br />
			Если Вы перевели деньги через банкомат, сделайте фото или скан квитанции, которую дал Вам банкомат.
			<br />
			<br />
			Если Вы веревели деньги через Сбербанк Онл@айн, то сделайте скриншот квитанции, которая появится после перевода.
		</p>
	</form>
</div>

<script type="text/javascript">
	var fmclick = 0;
	function lay5(order_id){
		
		var $offsetLeft	= 430;
		
		var scans = $('#scans_' + order_id);
		
		$('#lay5_block').css({
			'left'	:$offsetLeft
		}).find('#delete').html(scans).find('div').css('display', 'block');
		
		var scans = $('#scans_' + order_id);
		
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
		lay5(order_id);
	}
</script>