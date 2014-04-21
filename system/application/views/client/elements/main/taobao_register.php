<div class='table' id='taobao_register_block' style="width:370px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Регистрация аккаунта на Taobao.com</h3>
	</center>
	<p>
		Если у Вас нет аккаунта на Taobao.com, Вы можете заказать его у нас (стоимость $25). 
		Мы зарегистрируем его и отправим Вам на email.
		Если выбранный Вами логин уже занят, мы добавим к нему одну или несколько цифр.
		<br />
		<br />
		<center>
			Укажите данные для регистрации аккаунта:
		</center>
	</p>
	<br />
	<form class='admin-inside' id='taobao_register_form' action="/client/taobaoRegister/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td>
					Логин<em class='login_star'>*</em> :
				</td>
				<td>
					<input type="text" name="taobao_login" maxlength="20" value="" />
				</td>
			</tr>
			<tr>
				<td>
					Пароль<em class='password_star'>*</em> :
				</td>
				<td>
					<input type="text" name="taobao_password" maxlength="20" value="" />
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='2'>
					<br />
					<em>
						* обязательны для заполнения
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
								<input type='button' value='Отмена' onclick="$('#lay').fadeOut('slow');$('#taobao_register_block').fadeOut('slow');"/>
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
	var taobao_register_click = 0;

	function openTaobaoRegisterPopup()
	{
		var offsetLeft	= (window.innerWidth - $('#taobao_register_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#taobao_register_block').height()) / 2;
		
		$('#taobao_register_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#taobao_register_form em.message')
			.addClass('green-color')
			.removeClass('red-color')
			.html('')
			.hide();
				
		$('#taobao_register_form em').removeClass('red-color');
		$('#taobao_register_form input:text').val('');

		$('#lay').fadeIn("slow");
		$('#taobao_register_block').fadeIn("slow");
		
		if (!taobao_register_click)
		{
			taobao_register_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#taobao_register_block').fadeOut("slow");
			})
		}
	}
	
	$(function() {
		$('#taobao_register_form').ajaxForm({
			target: $('#taobao_register_form').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#taobao_register_form em')
					.removeClass('red-color');

				$('#taobao_register_form em.message')
					.html('')
					.hide();

				$('#taobao_register_form img.progressbar').show();
			},
			success: function(response)
			{
				$('#taobao_register_form img.progressbar').hide();
				
				if (response)
				{
					$('#taobao_register_form em.message')
						.html(response + '<br /><br />')
						.addClass('red-color')
						.show();
					
					if (response === 'Введите логин для регистрации аккаунта.')
					{
						$('#taobao_register_form em.login_star').addClass('red-color');
					}
					else if (response === 'Введите пароль для регистрации аккаунта.')
					{
						$('#taobao_register_form em.password_star').addClass('red-color');
					}
				}
				else
				{
					$('#taobao_register_block').fadeOut('slow');
					
					$('#taobao_register_form em.message')
						.addClass('green-color')
						.html('')
						.hide();
					
					openSuccessPopup();
				}
			},
			error: function(response)
			{
				$('#taobao_register_form img.progressbar').hide();
				
				$('#taobao_register_form em.message')
					.html('Заявка на регистрацию аккаунта не добавлена. Попробуйте еще раз.<br /><br />')
					.addClass('red-color')
					.show();
			}
		});

		$('a.taobao_register').click(function(e) {
			e.preventDefault();
			$('#alipay_refill_block').fadeOut('slow');
			$('#taobao_payment_block').fadeOut('slow');
			openTaobaoRegisterPopup();
		});
	});
</script>