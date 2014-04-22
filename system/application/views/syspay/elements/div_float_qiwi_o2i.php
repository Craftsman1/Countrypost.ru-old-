<div class='table' id="qiwi_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Заявка на пополнение счета</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<p>
		Пополнение счета через Qiwi с минимальной комиссией:
		<br />
		<br />
		Вам нужно перевести <b><b class="qiwi_amount_ru"></b> рублей</b> на кошелек <?= QW_IN_ACCOUNT ?>. В примечании к переводу обязательно указать: <b>"Пополнение на <b class="qiwi_amount_usd"></b>$. Клиент <b class="qiwi_user_id"></b>"</b> (<i><b class="qiwi_user_id" style="font-weight:normal;"></b> - это Ваш номер на сайте</i>).
	</p>
	<br />
	<form class='admin-inside' action="/client/addOrder2In/<?= $order->order_id ?>" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="payment_service" value="qw" />
		<input type="hidden" name="total_ru" class="qiwi_amount_ru" value="" />
		<input type="hidden" name="total_usd" class="qiwi_amount_usd" value="" />
		<table>
			<tr>
				<td>Номер Qiwi кошелька (счета):</td>
				<td>
					<input type="text" name="account" maxlength="20" value="" />
					<i>Пример: 9161234567</i>
				</td>
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
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#qiwi_block').fadeOut('slow');"/>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var qiwi_click = 0;

	function openQiwiPopup(user_id, amount_usd, amount_ru)
	{
		$('.qiwi_user_id').html(user_id);
		$('.qiwi_amount_usd').html(amount_usd).val(amount_usd);
		$('.qiwi_amount_ru').html(amount_ru).val(amount_ru);
		
		var offsetLeft	= window.innerWidth / 2 - 280;
		
		$('#qiwi_block').css({
			'left' : offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#qiwi_block').fadeIn("slow");
		
		if (!qiwi_click)
		{
			qiwi_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#qiwi_block').fadeOut("slow");
			})
		}
	}
</script>