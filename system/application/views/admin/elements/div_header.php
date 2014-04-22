<div class='top-block'>
	<div class='block-user'>
		<div class='left-block'>
		<h2>Авторизация</h2>
		<p>Здравствуйте, <span class='big-text'><a href='<?=$user ? $this->config->item('base_url').$user->user_group : $this->config->item('base_url').'user/showRegistration';?>'><?=$user->user_login;?></a></span></p>
		<div class='submit'><div><input type='submit' value='Выйти' onclick="javascript:window.location='<?=$this->config->item('base_url')?>user/logout';" /></div></div>
		</div>
		<div class='center-block'>
			<h3>ВАШ НОМЕР НА САЙТЕ: <?=$user->user_id;?></h3>
			<br>
			<? if (isset($_SESSION['countrypost_balance']) AND $_SESSION['countrypost_balance']) : ?>
			<p>
				<a href='<?= $this->config->item('base_url') . $this->user->user_group ?>/taxes'>Комиссия Countrypost: <?= $_SESSION['countrypost_balance'] ?></a>
			</p>
			<? endif; ?>
			<p><a href='<?=$this->config->item('base_url')?>admin/history'>Статистика платежей</a></p>
			<p><a href='<?=$this->config->item('base_url')?>admin/payments'>Заявки на оплату</a></p>
			<p><a href="<?=$this->config->item('base_url')?>admin/showEditFAQ">Редактировать F.A.Q.</a></p>
		</div>
	</div>
</div>