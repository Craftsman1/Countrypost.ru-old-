<div class='top-block'>
	<div class='block-user'>
		<div class='left-block'>
		<h2>Авторизация</h2>
		<p>Здравствуйте, <span class='big-text'><a href='<?=$user ? BASEURL.$user->user_group : BASEURL.'user/showRegistration';?>'><?=$user->user_login;?></a></span></p>
		<div class='submit'><div><input type='submit' value='Выйти' onclick="javascript:window.location='<?=BASEURL?>user/logout';" /></div></div>
		</div>
		<div class='center-block'>
			<h3>ВАШ НОМЕР НА САЙТЕ: <?=$user->user_id;?></h3>
			<p>Общий баланс: <span class='big-text'><?=$user->user_coints;?> $</span></p>
			<p><a href='<?=BASEURL?>admin/showPaymentHistory'>Статистика платежей</a></p>
		</div>
	</div>
</div>