<div class='top-block'>
	<form class='block-user autorization-inner' action='<?=BASEURL?>user/login' method="POST">
		<h2>Авторизация</h2>
		<div class='text-field'><div><input name="login" type='text' value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
		<div class='text-field'><div><div class='password'><input name="password" type='password' value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
		<div class='submit'><div><input type='submit' value='Войти' /></div></div>
		<a href='<?=BASEURL?>user/showRegistration' class='registration'>Регистрация</a>
		<a href='<?=BASEURL?>user/showPasswordRecovery' class='remember-password'>Напомнить</a>
	</form>
</div>
<div class='content'>
	<h2>подтверждение регистрации</h2>
	<p><?= $result->m ?></p>
</div>