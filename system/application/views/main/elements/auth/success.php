<div class='autorization autorization-ok'>
	<h2>Авторизация</h2>
	<p>
		<b>Здравствуйте:</b>
		<br>
		<span class='big-text'>
			<a href='<?= BASEURL . $this->user->user_group?>'><?= $this->user->user_login ?></a>
		</span>
	</p>
	<p>
		<b>Ваш номер на сайте:</b>
		<?= $this->user->user_id ?>
	</p>
	<? if ($this->user->user_group == 'client') : ?>
	<p>
		<a href='<?= BASEURL ?>user/showProfile'>Изменить личные данные</a>
	</p>
	<br>
	<? endif; ?>
	<div class='submit' style="width: 227px!important;">
		<div>
			<input style="width: 211px!important;" type='submit' value='Выйти'  onclick="javascript:window.location='<?= BASEURL ?>user/logout';" />
		</div>
	</div>
</div>