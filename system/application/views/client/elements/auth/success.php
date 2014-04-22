<div class='top-block'>
	<div class='block-user'>
		<div class='left-block'>
			<h2>Авторизация</h2>
			<p>
				Здравствуйте,
				<span class='big-text'>
					<a href='<?= $this->config->item('base_url') . $this->user->user_group ?>'><?=$this->user->user_login;?></a>
				</span>
			</p>
			<p><a href='/profile'>Изменить личные данные</a></p>
			<div class='submit'>
				<div>
					<input type='submit'
						   value='Выйти'
						   onclick="javascript:window.location='<?=	$this->config->item('base_url') ?>user/logout';" />
				</div>
			</div>
		</div>
		<div class='center-block'>
			<h3>ВАШ НОМЕР НА САЙТЕ: <?= $this->user->user_id ?></h3>
			<br>
			<!--?<p>
				<a href='javascript:showBalanceWindow();'>Баланс по посредникам</a>
			</p>?-->
			<p>
				<a href='/client/orders'>Мои заказы</a>
			</p>
			<p>
				<a href='/profile'>Мои адреса доставки</a>
			</p>
			<p>
				<a href='/<?= $this->user->user_group ?>/history'>Статистика платежей</a>
			</p>
		</div>
	</div>
</div>
<? View::show('/client/elements/payments/balance_block'); ?>
<script>
	user = '<?= empty($this->user->user_group) ? '' : $this->user->user_group ?>';
	window.user_group = '<?= empty($this->user->user_group) ? '' : $this->user->user_group ?>';
</script>