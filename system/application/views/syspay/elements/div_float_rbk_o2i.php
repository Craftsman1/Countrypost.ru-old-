<div class='table' id="rbk_block" style="width:550px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Заявка на пополнение счета</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<p>
		Чтобы пополнить счет этим способом с минимальной комиссий Вам нужно:
		<br />
		<br />
		<b>1.</b> Зарегистрироваться в платежной системе <a href="http://www.rbkmoney.ru/" target="_blank" >RBKmoney</a>
		<br />
		<b>2.</b> Пополнить свой кошелек одним из <a href="http://www.rbkmoney.ru/kak-popolnit-koshelek" target="_blank" >этих</a> способов
		<br />
		<b>3.</b> Перевести <b><b class="rbk_amount_ru"></b> рублей</b> на кошелек <?= RBK_IN_ACCOUNT ?>. В примечании к переводу <b>обязательно</b> указать: <b>"Пополнение на <b class="rbk_amount_usd"></b>$. Клиент <b class="rbk_user_id"></b>"</b> (<i><b class="rbk_user_id" style="font-weight:normal;"></b> - это Ваш номер на сайте</i>).
	</p>
	<br />
	<form class='admin-inside' action="/client/addOrder2In/" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="payment_service" value="rbk" />
		<input type="hidden" name="total_ru" class="rbk_amount_ru" value="" />
		<input type="hidden" name="total_usd" class="rbk_amount_usd" value="" />
		<table>
			<tr>
				<td>Номер кошелька (счета):</td>
				<td>
					<input type="text" name="account" maxlength="20" value="" />
					<i>Пример: RU606456384</i>
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
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#rbk_block').fadeOut('slow');"/>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var rbk_click = 0;

	function openRbkPopup(user_id, amount_usd, amount_ru)
	{
		$('.rbk_user_id').html(user_id);
		$('.rbk_amount_usd').html(amount_usd).val(amount_usd);
		$('.rbk_amount_ru').html(amount_ru).val(amount_ru);
		
		var offsetLeft	= window.innerWidth / 2 - 280;
		
		$('#rbk_block').css({
			'left' : offsetLeft
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#rbk_block').fadeIn("slow");
		
		if (!rbk_click)
		{
			rbk_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#rbk_block').fadeOut("slow");
			})
		}
	}
</script>