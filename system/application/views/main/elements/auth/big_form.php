<div class='top-block'>
	<form class='block-user autorization-inner bigAuthForm'
		  action='<?= BASEURL ?>user/loginAjax'
		  method="POST">
		<h2 style="float: left; margin-right: 50px;">Вход</h2>
		<div class='text-field'>
			<div>
				<input name="login"
					   type='text'
					   value='Логин'
					   onfocus='javascript: if (this.value == "Логин") this.value = "";'
					   onblur='javascript: if (this.value == "") this.value = "Логин";' >
			</div>
		</div>
		<div class='text-field'>
			<div>
				<div class='password'>
					<input name="password"
						   type='password' >
				</div>
			</div>
		</div>
		<div class='submit'>
			<div>
				<input type='button'
					   value='Войти'
						onclick="$('form.bigAuthForm').submit();"/>
			</div>
		</div>
		<a href='<?= BASEURL ?>user/remindpassword' class='remember-password'>Напомнить</a>
		<a href='<?= BASEURL ?>signup' class='register'>Регистрация</a>
		<img class="float login_progress"
			 style="display:none;margin-left:20px;margin-top:8px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</form>
</div>
<script type="text/javascript">
$(function() {
	$('form.bigAuthForm').ajaxForm({
		dataType: 'html',
		iframe: true,
		beforeSubmit: function(formData, jqForm, options)
		{
			$('form.bigAuthForm img.login_progress').show();
		},
		success: function(response)
		{
			$('form.bigAuthForm img.login_progress').hide();

			if (response)
			{
				success('top', 'Вы успешно залогинились в Countrypost.ru.');
				$('div.top-block').replaceWith(response);
			}
			else
			{
				error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');
			}
		},
		error: function(response)
		{
			error('top', 'Логин или пароль введен неверно. Попробуйте еще раз.');
			$('form.bigAuthForm img.login_progress').hide();
		}
	});
});
</script>