<div class='table' id="sv_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Заявка на пополнение счета</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<p>
		Оплата заказа через <b><?= SV_SERVICE_NAME ?></b>:
		<br />
		<br />
		Вам нужно перевести <b><b class="sv_amount_ru"></b> рублей</b> на карту <?= SV_IN_ACCOUNT ?> (Москва).
	</p>
	<br />
	<form class='admin-inside' action="/client/addOrder2In/<?= $order->order_id ?>" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="payment_service" value="sv" />
		<input type="hidden" name="total_ru" class="sv_amount_ru" value="" />
		<input type="hidden" name="total_usd" class="sv_amount_usd" value="" />
		<table>
			<tr>
				<td>Номер карты:</td>
				<td>
					<input type="text" name="account" maxlength="20" value="" />
					<i>Пример: 7790****2198</i>
				</td>
			</tr>
			<tr>
				<td>Фото квитанции:
					<br />(максимальный размер 3Mb)
				</td>
				<td><input type="file" name="userfile" value="" /></td>
			</tr>
			<tr class='last-row'>
				<td colspan='2'>
					<div class='float'>	
						<div class='submit'>
							<div>
								<input type='submit' name="add" value='Добавить заявку' />
							</div>
						</div>
						<div class='submit'>
							<div>
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#sv_block').fadeOut('slow');"/>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var sv_click = 0;

	function openSvPopup(user_id, amount_usd, amount_ru)
	{
		$('#sv_user_id').html(user_id);
		$('.sv_amount_usd').val(amount_usd);
		$('.sv_amount_ru').html(amount_ru).val(amount_ru);
		
		var offsetLeft	= window.innerWidth / 2 - 280;
		
		$('#sv_block').css({
			'left' : offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#sv_block').fadeIn("slow");
		
		if (!sv_click)
		{
			sv_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#sv_block').fadeOut("slow");
			})
		}
	}
</script>