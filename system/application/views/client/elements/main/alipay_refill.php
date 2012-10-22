<div class='table' id='alipay_refill_block' style="width:370px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Пополнение счета Alipay</h3>
	</center>
	<p>
		Если Вы сами хотите оплачивать свои заказы на Taobao.com в любое удобное для Вас время, мы можем пополнить Ваш Alipay счет. 
		Если у Вас нет аккаунта на Taobao.com, Вы можете <a href='#' class='taobao_register'>заказать</a> его у нас.
		<br />
		<br />
		<center>
			Укажите данные для пополнения счета:
		</center>
	</p>
	<br />
	<form class='admin-inside' id='alipay_refill_form' action="/client/alipayRefill/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td>
					Логин от Alipay или Taobao<em class='login_star'>*</em> :
				</td>
				<td>
					<input type="text" name="alipay_login" maxlength="20" value="" />
				</td>
			</tr>
			<tr>
				<td>
					Пароль от Alipay или Taobao<em class='password_star'>*</em> :
				</td>
				<td>
					<input type="text" name="alipay_password" maxlength="20" value="" />
				</td>
			</tr>
			<tr>
				<td>
					Сумма пополнения, &yen;<em class='amount_star'>*</em> :
				</td>
				<td>
					<input type="text" class="alipay_amount" name="alipay_amount" maxlength="7" value="" />
				</td>
			</tr>
			<tr>
				<td>
					Комиссия <?= $alipay_refill_tax ?>% :
				</td>
				<td id='alipay_refill_tax'>
				</td>
			</tr>
			<tr>
				<td>
					<b>
						Итого будет зачислено на Alipay :
					</b>
				</td>
				<td>
					<b id='alipay_total'></b>
				</td>
			</tr>
			<tr>
				<td nowrap>
					<b>
						Итого будет списано со счета :
					</b>
				</td>
				<td>
					<b id='alipay_total_usd'></b>
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='2'>
					<br />
					<em>
						* все поля обязательны для заполнения
					</em>
					<br />
					<em class='message' style='display:none;'>
					</em>
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='2'>
					<div class='float'>	
						<div class='submit'>
							<div>
								<input type='submit' name="add" value='Заказать' />
							</div>
						</div>
						<div class='submit'>
							<div>
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#alipay_refill_block').fadeOut('slow');"/>
							</div>
						</div>
					</div>
					<img class="float progressbar" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var alipay_refill_click = 0;

	function openAlipayRefillPopup()
	{
		var offsetLeft	= (window.innerWidth - $('#alipay_refill_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#alipay_refill_block').height()) / 2;
		
		$('#alipay_refill_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#alipay_refill_form em.message')
			.addClass('green-color')
			.removeClass('red-color')
			.html('')
			.hide();
				
		$('#alipay_refill_form em').removeClass('red-color');
		$('#alipay_refill_form input:text').val('');

		$('#lay').fadeIn("slow");
		$('#alipay_refill_block').fadeIn("slow");
		
		if (!alipay_refill_click)
		{
			alipay_refill_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#alipay_refill_block').fadeOut("slow");
			})
		}
	}
	
	$(function() {
		$('#alipay_refill_form').ajaxForm({
			target: $('#alipay_refill_form').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#alipay_refill_form em')
					.removeClass('red-color');

				$('#alipay_refill_form em.message')
					.html('')
					.hide();

				$('#alipay_refill_form img.progressbar').show();
			},
			success: function(response)
			{
				$('#alipay_refill_form img.progressbar').hide();
				
				if (response)
				{
					$('#alipay_refill_form em.message')
						.html(response + '<br /><br />')
						.addClass('red-color')
						.show();
					
					if (response === 'Введите логин от Alipay.')
					{
						$('#alipay_refill_form em.login_star').addClass('red-color');
					}
					else if (response === 'Введите пароль от Alipay.')
					{
						$('#alipay_refill_form em.password_star').addClass('red-color');
					}
					else if (response === 'Введите сумму пополнения.')
					{
						$('#alipay_refill_form em.amount_star').addClass('red-color');
					}
				}
				else
				{
					$('#alipay_refill_form em.message')
						.addClass('green-color')
						.html('')
						.hide();

					$('#alipay_refill_block').fadeOut('slow');
					openSuccessPopup();
				}
			},
			error: function(response)
			{
				$('#alipay_refill_form img.progressbar').hide();
				
				$('#alipay_refill_form em.message')
					.html('Заявка на регистрацию аккаунта не добавлена. Попробуйте еще раз.<br /><br />')
					.addClass('red-color')
					.show();
			}
		});
		
		$('#alipay_refill_form input.alipay_amount')
			.keypress(function(event){validate_number(event);})
			.bind('change keyup click', function() {
				var rate = <?= $cny_rate ?>;
				var tax = <?= $alipay_refill_tax ?>;
				
				var amount = parseInt($(this).val());
				amount = (isNaN(amount) ? 0 : amount);
		
				var alipay_tax = Math.ceil(amount * tax * 0.01);
				var alipay_total = amount - alipay_tax;
				var alipay_total_usd = Math.ceil(amount / rate);
				
				$('#alipay_refill_form td#alipay_refill_tax').html(
					alipay_tax ?
					('&yen;' + alipay_tax) :
					'');
				$('#alipay_refill_form td b#alipay_total').html(
					alipay_total ? 
					('&yen;' + alipay_total) :
					'');
				$('#alipay_refill_form td b#alipay_total_usd').html(
					alipay_total_usd ?
					('$' + alipay_total_usd) :
					'');
			});
		
		$('a.alipay_refill').click(function(e) {
			e.preventDefault();
			openAlipayRefillPopup();
		});
	});
</script>