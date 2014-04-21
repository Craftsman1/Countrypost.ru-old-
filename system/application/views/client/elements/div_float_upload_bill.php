<div class='table' id="upload_foto_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Добавление подтверждения</h3>
	</center>
	<br>
	<form class='admin-inside' action="/client/addPaymentFoto/" enctype="multipart/form-data" method="POST">
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
		<br>
		<p>
			Чтобы мы смогли найти Ваш платеж (перевод), Вам нужно сделать и добавить подтверждение.
			<br />
			<br />
			Подтверждение - это скан, фото или скриншот квитанции.
			<br />
			<br />
			Если Вы перевели деньги через банкомат, сделайте фото или скан квитанции, которую выдал Вам банкомат.
			<br />
			<br />
			Если Вы перевели деньги через Сбербанк Онл@айн, сделайте скриншот квитанции,
			которая появилась после перевода.
		</p>
	</form>
</div>

<script type="text/javascript">
	var fmclick = 0;
	function upload_foto(order_id){
		var offsetLeft = window.innerWidth / 2 - 280;

		var scans = $('#scans_' + order_id);
		
		$('#upload_foto_block').css({
			'left' : offsetLeft
		}).find('#delete').html(scans).find('div').css('display', 'block');
		
		var scans = $('#scans_' + order_id);
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#upload_foto_block').fadeIn("slow");
		
		if (!fmclick){
			fmclick = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#upload_foto_block').fadeOut("slow");
			})
		}
	}
	
	function uploadBillFoto(order_id){
		document.getElementById('order_id').value = order_id;
		upload_foto(order_id);
	}
</script>