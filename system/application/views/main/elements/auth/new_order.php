<div style="display:block;margin:0;" class='top-block order_auth'>
	<form class='autorization new_order_auth' method="post" action='<?= BASEURL ?>user/loginManagerAjax' style="float:left;">
		<h2>Авторизация</h2>
		<div class='text-field'><div><input type='text' name="login" value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
		<div class='text-field'><div><div class='password'><input type='password' name="password" id="password" value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
	<div id=vkontakte style="float:right; margin-right:20px;"><a href='http://oauth.vkontakte.ru/authorize?client_id=<?=VK_APP_ID; ?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]; ?>/user/vkontakte&scope=0&response_type=code' target=_blank><img src="/static/images/vkontakte.png" /></a></div>
	<div id=facebook style="float:right; margin-right:5px;"><a href='https://www.facebook.com/dialog/oauth?client_id=<?=FB_APP_ID; ?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]; ?>/user/facebook&scope=email,read_stream,user_location' target=_blank><img src="/static/images/facebook.png" /></a></div>
		<div class='submit'>
			<div>
				<input type='submit' value='Войти' />
			</div>
		</div>
		<a href='<?= BASEURL ?>user/showPasswordRecovery' class='remember-password'>Напомнить</a>
		<a href='<?= BASEURL ?>user/showRegistration' class='register'>Регистрация</a>
	</form>
	<img class="float login_progress" style="display:none;margin-left:10px;margin-top:119px;" src="/static/images/lightbox-ico-loading.gif"/>
</div>
<br style="clear:both;">
<script type="text/javascript">
	$(function() {
		$('form.new_order_auth').ajaxForm({
			target: '<?= BASEURL ?>user/loginManagerAjax',
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
