<div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/orders/filter'); ?>
	<form class='autorization smallAuthForm'
		  method="POST"
		  action='<?= BASEURL ?>user/loginAjaxMain'>
		<h2>Вход</h2>
		<img class="float login_progress"
			 style="display: none;left: 88px;top: 10px;position: absolute;"
			 src="/static/images/lightbox-ico-loading.gif"/>
		<div class='text-field'>
			<div>
				<input type='text'
					   name="login"
					   value='Логин'
					   onfocus='javascript: if (this.value == "Логин") this.value = "";'
					   onblur='javascript: if (this.value == "") this.value = "Логин";' />
			</div>
		</div>
		<div class='text-field'>
			<div class='password'>
				<input type='password'
					   name="password"
					   id="password"
					   value='Пароль'>
			</div>
		</div>
		<div class='submit'>
			<div>
				<input type='submit' value='Войти' />
			</div>
		</div>
		<a href='<?= BASEURL ?>user/remindpassword' class='remember-password'>Напомнить</a>
		<a href='<?= BASEURL ?>signup' class='register'>Регистрация</a>
	</form>
	<? View::show('main/elements/div_social'); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('form.smallAuthForm').ajaxForm({
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('form.smallAuthForm img.login_progress').show();
			},
			success: function(response)
			{
				$('form.smallAuthForm img.login_progress').hide();

				if (response)
				{
					success('top', 'Вы успешно вошли в Countrypost.ru.');
					$('form.smallAuthForm').replaceWith(response);
				}
				else
				{
					error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');
				}
			},
			error: function(response)
			{
				error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');
				$('form.smallAuthForm img.login_progress').hide();
			}
		});
	});
</script>