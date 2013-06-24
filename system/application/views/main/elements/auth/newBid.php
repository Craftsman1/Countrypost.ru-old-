<div style="display:block;" class='top-block bid_auth'>
	<form class='autorization bidAuthForm' method="POST" action='<?= $this->config->item('base_url') ?>user/loginAjax/newBid/<?=
	$order->order_id ?>' style="float:left;">
    	<input type="hidden" name="segment" value="<?= $this->uri->segment(2) ?>" />
		<h2>Авторизация</h2>
		<div class='text-field'>
			<div>
				<input type='text' name="login" value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' />
			</div>
		</div>
		<div class='text-field'>
			<div>
				<div class='password'>
					<input type='password' name="password" id="password" value='Пароль' />
				</div>
			</div>
		</div>
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
		$('form.bidAuthForm').ajaxForm({
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('div.bid_auth')
					.find('img.login_progress')
					.show('slow');
			},
			success: function(response)
			{
				$('div.bid_auth')
					.find('img.login_progress')
					.hide('slow');

				if (response)
				{
					success('top', 'Вы успешно залогинились в Countrypost.ru.');
					
					$('div.top-block:first')
						.hide('slow')
						.after(response);
						
					$('div.bid_auth').hide('slow');

					$('div#bid0')
						.detach()
						.appendTo('div#newBidForm');

					showNewBidForm();
				}
				else
				{
					error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');
				}
			},
			error: function(response)
			{
				error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');

				$('div.bid_auth')
					.find('img.login_progress')
					.hide('slow');
			}
		});
	});
</script>
