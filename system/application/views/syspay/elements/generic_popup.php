<div class='table'
	 id="generic_block"
	 style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Заявка на пополнение счета</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<p>
		Оплата заказа через <b class="generic_name"></b>:
		<br />
		<br />
		Вам нужно перевести
		<b><b class="generic_amount_ru"></b> RUB</b>
		<b><b class="generic_amount_usd"></b> USD</b>
		<b style="display: none;"><b class="generic_amount_uah"></b> UAH</b>
		<b class="generic_account" style="font-weight: normal;"></b>.
		После перевода сохраните квитанцию.
	</p>
	<br />
	<form class='admin-inside' action="/client/addOrder2In/<?= $order->order_id ?>" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="payment_service" class="generic_service" value="" />
		<input type="hidden" name="total_ru" class="generic_amount_ru" value="" />
		<input type="hidden" name="total_usd" class="generic_amount_usd" value="" />
		<input type="hidden" name="total_uah" class="generic_amount_uah" value="" />
		<input type="hidden" name="total_local" class="generic_amount_local" value="" />
		<table>
			<tr>
				<td>
					<b class="generic_type" style="font-weight: normal;"></b>
				</td>
				<td>
					<input type="text" name="account" maxlength="20" value="" />
					<i class="generic_example"></i>
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
								<input type='button' value='Отмена' onclick="$('#lay').click();"/>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var generic_click = 0;

	function openGenericPopup(generic_name,
							  generic_account,
							  generic_type,
							  generic_example,
							  generic_service,
							  amount_ru,
							  amount_usd,
							  payment_amount,
							  currency)
	{
		$('.generic_service').val(generic_service).html(generic_service);
		$('.generic_name').html(generic_name).val(generic_name);
		$('.generic_account').html(generic_account).val(generic_account);
		$('.generic_type').html(generic_type).val(generic_type);
		$('.generic_example').html(generic_example).val(generic_example);

		$('.generic_amount_usd')
			.val(amount_usd)
			.html(amount_usd);

		$('.generic_amount_local')
			.val(payment_amount)
			.html(payment_amount);

		$('.generic_amount_ru')
			.val(amount_ru)
			.html(amount_ru);

		$('.generic_amount_uah')
			.val(amount_ru)
			.html(amount_ru);

		if (amount_usd == 0 && amount_ru != 0)
		{
			$('.generic_amount_usd')
				.parent()
				.hide();

			$('.generic_amount_ru')
				.parent()
				.show();
		}
		else if (amount_usd != 0 && amount_ru == 0)
		{
			$('.generic_amount_ru')
				.parent()
				.hide();

			$('.generic_amount_usd')
				.parent()
				.show();
		}

		if (currency == 'UAH')
		{
			$('.generic_amount_ru')
				.parent()
				.hide();

			$('.generic_amount_usd')
				.parent()
				.hide();

			$('.generic_amount_uah')
				.parent()
				.show();
		}

		var offsetLeft	= window.innerWidth / 2 - 280;
		
		$('#generic_block').css({
			'left' : offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#generic_block').fadeIn("slow");
		
		if ( ! generic_click)
		{
			generic_click = 1;

			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#generic_block').fadeOut("slow");
			})
		}
	}
</script>