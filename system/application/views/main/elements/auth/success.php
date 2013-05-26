<div class='autorization-ok top-block'>
	<div class='autorization'>
		<h2>Здравствуйте</h2>
		<p>
			<span class='big-text'>
				<a href='<?= BASEURL . $this->user->user_group?>'><?= $this->user->user_login ?></a>
			</span>
		</p>
		<p>
			<? if ($this->user->user_group == 'client' OR
				$this->user->user_group == 'manager') : ?>
			<a href='<?= BASEURL ?>profile'>Мой профиль</a>
			<? else : ?>
			<br>
			<? if (isset($_SESSION['countrypost_balance']) AND $_SESSION['countrypost_balance']) : ?>
			<p>
				<a href='/<?= $this->user->user_group ?>/'>Баланс Countrypost.ru: <?= $_SESSION['countrypost_balance'] ?></a>
			</p>
			<? endif; ?>
			<? endif; ?>
		</p>
		<br>
		<div class='submit' style="width: 227px!important;">
			<div>
				<input style="width: 211px!important;" type='submit' value='Выйти'  onclick="javascript:window.location='<?= BASEURL ?>user/logout';" />
			</div>
		</div>
	</div>
</div>