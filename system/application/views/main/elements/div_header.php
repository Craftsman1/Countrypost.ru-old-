<? View::show('main/elements/div_top'); ?><? if (isset($user) && $user && ($pageinfo['mname'] != 'index' OR $this->uri->segment(1) == 'dealers')) :
	View::show($user->user_group.'/elements/div_header');
	elseif (isset($user) AND
		$user AND
		$pageinfo['mname'] == 'index' AND $this->uri->segment(1) != 'dealers') : ?><div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/order_filter'); ?>
	<div class='autorization autorization-ok'>
		<h2>Авторизация</h2>
		<p><b>Здравствуйте:</b><br /><span class='big-text'><a href='<?=BASEURL.$user->user_group?>'><?=$user->user_login;?></a></span></p>
		<p><b>Ваш номер на сайте:</b> <?=$user->user_id;?></p>
		<p><b>Общий баланс:</b><br /><span class='big-text'><?=$user->user_coints;?> $</span></p>
		<? if ($user->user_group == 'client') : ?>
			<p><a href='<?= BASEURL ?>user/showProfile'>�?зменить личные данные</a></p><br />
		<? endif; ?>
		<div class='submit' style="width: 227px!important;"><div><input style="width: 211px!important;" type='submit' value='Выйти'  onclick="javascript:window.location='<?= BASEURL ?>user/logout';" /></div></div>
	</div>
	<? View::show('main/elements/div_social'); ?>
</div><? elseif ($pageinfo['mname'] == 'index' AND $this->uri->segment(1) != 'dealers') : ?><div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/order_filter'); ?>
	<form class='autorization' method="post" action='<?= BASEURL ?>user/login'>
		<h2>Авторизация</h2>
		<div class='text-field'><div><input type='text' name="login" value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
		<div class='text-field'><div><div class='password'><input type='password' name="password" id="password" value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
	<div id=vkontakte style="float:right; margin-right:20px;"><a href='http://oauth.vkontakte.ru/authorize?client_id=<?=VK_APP_ID; ?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]; ?>/user/vkontakte&scope=0&response_type=code' target=_blank><img src=/static/images/vkontakte.png /></a></div>
	<div id=facebook style="float:right; margin-right:5px;"><a href='https://www.facebook.com/dialog/oauth?client_id=<?=FB_APP_ID; ?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]; ?>/user/facebook&scope=email,read_stream,user_location' target=_blank><img src=/static/images/facebook.png /></a></div>
		<div class='submit'>
			<div>
				<input type='submit' value='Войти' />
			</div>
		</div>
		<a href='<?= BASEURL ?>user/showPasswordRecovery' class='remember-password'>Напомнить</a>
		<a href='<?= BASEURL ?>user/showRegistration' class='register'>Регистрация</a>
	</form>				
	<? View::show('main/elements/div_social'); ?>
</div><? else : ?><div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/order_filter'); ?>
	<form class='block-user autorization-inner' action='<?= BASEURL ?>user/login' method="POST">
		<h2>Авторизация</h2>
		<div class='text-field'><div><input name="login" type='text' value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
		<div class='text-field'><div><div class='password'><input name="password" type='password' value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
		<div class='submit'><div><input type='submit' value='Войти' /></div></div>
		<a href='<?= BASEURL ?>user/showRegistration' class='registration'>Регистрация</a>
		<a href='<?= BASEURL ?>user/showPasswordRecovery' class='remember-password'>Напомнить</a>
	</form>
</div><? endif; ?>