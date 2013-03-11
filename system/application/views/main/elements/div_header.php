<? View::show('main/elements/div_top'); ?>
<? if (isset($user) AND $user AND
	($pageinfo['mname'] != 'index' OR
		$this->uri->segment(1) == 'dealers' OR
	$this->uri->segment(1) == 'clients' OR
		$this->uri->segment(1) == 'profile' OR
		$this->uri->segment(1) == 'terms')) :
	View::show($user->user_group.'/elements/div_header');
	elseif (isset($user) AND
		$user AND
		$pageinfo['mname'] == 'index' AND
			$this->uri->segment(1) != 'dealers' AND
				$this->uri->segment(1) != 'clients') :
?><div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/orders/filter'); ?>
	<div class='autorization autorization-ok'>
		<h2>Авторизация</h2>
		<p><b>Здравствуйте:</b><br /><span class='big-text'><a href='<?=BASEURL.$user->user_group?>'><?=$user->user_login;?></a></span></p>
		<p><b>Ваш номер на сайте:</b> <?=$user->user_id;?></p>
		<p><!--b>Общий баланс:</b><br /><span class='big-text'><?=$user->user_coints;?> $</span-->&nbsp;</p>
		<? if ($user->user_group == 'client') : ?>
			<p><a href='<?= BASEURL ?>user/showProfile'>Изменить личные данные</a></p><br />
		<? endif; ?>
		<div class='submit' style="width: 227px!important;">
			<div>
				<input style="width: 211px!important;" type='submit' value='Выйти'  onclick="javascript:window.location='<?= BASEURL ?>user/logout';" />
			</div>
		</div>
	</div>
	<? View::show('main/elements/div_social'); ?>
</div><?
	elseif ($pageinfo['mname'] == 'index' AND
			$this->uri->segment(1) != 'dealers' AND
			$this->uri->segment(1) != 'clients' AND
			$this->uri->segment(1) != 'signup' AND
			$this->uri->segment(1) != 'terms') :
?><div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/orders/filter'); ?>
	<form class='autorization' method="post" action='<?= BASEURL ?>user/login'>
		<h2>Авторизация</h2>
		<div class='text-field'><div><input type='text' name="login" value='Логин' onfocus='javascript: if (this.value == "Логин") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Логин";' /></div></div>
		<div class='text-field'><div><div class='password'><input type='password' name="password" id="password" value='Пароль' onfocus='javascript: if (this.value == "Пароль") this.value = "";' onblur='javascript: if (this.value == "") this.value = "Пароль";' /></div></div></div>
		<div class='submit'>
			<div>
				<input type='submit' value='Войти' />
			</div>
		</div>
		<a href='<?= BASEURL ?>user/remindpassword' class='remember-password'>Напомнить</a>
		<a href='<?= BASEURL ?>signup' class='register'>Регистрация</a>
	</form>				
	<? View::show('main/elements/div_social'); ?>
</div><? else : ?><div class='top-block'>
	<? if (isset($filter)) View::show('main/elements/orders/filter'); ?>
	<form class='block-user autorization-inner' action='<?= BASEURL ?>user/login' method="POST">
		<h2 style="float: left;margin-right: 50px;">Вход</h2>
		<div class='text-field'>
			<div>
				<input name="login"
					   type='text'
					   value='Логин'
					   onfocus='javascript: if (this.value == "Логин") this.value = "";'
					   onblur='javascript: if (this.value == "") this.value = "Логин";' />
			</div>
		</div>
		<div class='text-field'>
			<div>
				<div class='password'>
					<input name="password"
						   type='password'
						   value='Пароль' />
				</div>
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
</div><? endif; ?>