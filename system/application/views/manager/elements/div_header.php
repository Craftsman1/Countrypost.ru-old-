<div class='top-block'>
	<div class='block-user'>
		<div class='left-block'>
			<h2>Авторизация</h2>
			<p>Здравствуйте, <span class='big-text'><a href='<?=$user ? BASEURL.$user->user_group : BASEURL.'user/showRegistration';?>'><?=$user->user_login;?></a></span></p>
			<div class='submit'><div><input type='submit' value='Выйти' onclick="javascript:window.location='<?=BASEURL?>user/logout';" /></div></div>
		</div>
		<div class='right-block'>
		<?	$credit_local = $this->session->userdata('manager_credit_local');
			$credit_date_local = $this->session->userdata('manager_credit_date_local');
			if (isset($credit_local) && $credit_local && isset($credit_date_local) && $credit_date_local) : ?>
			<p><? $date = new DateTime($credit_date_local); echo $date->format('d.m.Y');?> Кредит на <span class='big-text'><?=$this->session->userdata('manager_currency')?><?=$credit_local?></span></p>
		<? endif; ?>
		<?	$credit = $this->session->userdata('manager_credit');
			$credit_date = $this->session->userdata('manager_credit_date');
			if (isset($credit) && $credit && isset($credit_date) && $credit_date) : ?>
			<p><? $date = new DateTime($credit_date); echo $date->format('d.m.Y');?> Кредит на <span class='big-text'>$<?=$credit?></span></p>
		<? endif; ?>
		</div>
		<div class='center-block'>
			<h3>ВАШ НОМЕР НА САЙТЕ: <?=$user->user_id;?></h3>
			<p>Общий баланс в местной валюте: <span class='big-text'><?=$this->session->userdata('manager_currency')?><?=$this->session->userdata('manager_balance_local')?></span></p>
			<p>Баланс в долларах: <span class='big-text'>$<?=$user->user_coints?></span></p>
			<p><a href='/<?=$user->user_group?>/showPaymentHistory'>Статистика платежей</a></p>
			<p><a href='/<?=$user->user_group?>/showOutMoney'>Заявка на вывод денег</a></p>
		</div>
	</div>
</div>