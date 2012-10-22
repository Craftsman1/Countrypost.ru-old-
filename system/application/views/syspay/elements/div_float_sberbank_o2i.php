<div class='table' id="sberbank_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Заявка на пополнение счета</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<p>
		Пополнение счета переводом с карты на карту через Сбербанк:
		<br />
		<br />
		Вам нужно перевести <b><b class="sberbank_amount_ru"></b> рублей</b> на карту <?= BM_IN_ACCOUNT ?> (Москва). После перевода сохраните квитанцию.
	</p>
	<br />
	<form class='admin-inside' action="/client/addOrder2In/" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="payment_service" value="bm" />
		<input type="hidden" name="total_ru" class="sberbank_amount_ru" value="" />
		<input type="hidden" name="total_usd" class="sberbank_amount_usd" value="" />
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
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#sberbank_block').fadeOut('slow');"/>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var sberbank_click = 0;

	function openSberbankPopup(user_id, amount_usd, amount_ru)
	{
		$('#sberbank_user_id').html(user_id);
		$('.sberbank_amount_usd').val(amount_usd);
		$('.sberbank_amount_ru').html(amount_ru).val(amount_ru);
		
		var offsetLeft	= window.innerWidth / 2 - 280;
		
		$('#sberbank_block').css({
			'left' : offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#sberbank_block').fadeIn("slow");
		
		if (!sberbank_click)
		{
			sberbank_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#sberbank_block').fadeOut("slow");
			})
		}
	}
</script>