<div style="display:block;margin:0;" class='top-block order_auth'>
	<form class='autorization new_order_auth' method="post" action='<?= $this->config->item('base_url') ?>user/loginAjax' style="float:left;">
		<input type="hidden" name="segment" value="<?= ($this->uri->segment(3) != '') ? $this->uri->segment(3) : $this->uri->segment(2) ?>" />
		<h2>Авторизация</h2>
		<div class='text-field'><div><input type='text' name="login" value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
		<div class='text-field'><div><div class='password'><input type='password' name="password" id="password" value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
		<div class='submit'>
			<div>
				<input type='submit' value='Войти' />
			</div>
		</div>
		<a href='<?= $this->config->item('base_url') ?>user/showPasswordRecovery' class='remember-password'>Напомнить</a>
		<a href='<?= $this->config->item('base_url') ?>user/showRegistration' class='register'>Регистрация</a>
	</form>
	<img class="float login_progress" style="display:none;margin-left:10px;margin-top:119px;" src="/static/images/lightbox-ico-loading.gif"/>
</div>
<br style="clear:both;">
<script type="text/javascript">
	$(function() {
		$('form.new_order_auth').ajaxForm({
			target: '<?= $this->config->item('base_url') ?>user/loginAjax',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('div.order_auth')
						.find('img.login_progress')
						.show('slow');
			},
			success: function(response)
			{
				$('div.order_auth')
						.find('img.login_progress')
						.hide('slow');

				if (response)
				{
					success('top', 'Вы успешно залогинились в Countrypost.ru.');

					$('div.top-block:first')
							.hide('slow')
							.after(response);

					$('div.order_auth').hide('fast');

                    var cart = window.cpCart;
                    if (cart && cart.length)
                    {
                        $('.checkOutOrderBlock').show('fast');
                    }
				}
				else
				{
					error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');
				}
			},
			error: function(response)
			{
				error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');

				$('div.order_auth')
						.find('img.login_progress')
						.hide('slow');
			}
		});
	});
</script>