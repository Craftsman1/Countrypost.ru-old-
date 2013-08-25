<div class='top-block'>
	<div class='block-user'>
		<div class='left-block'>
			<h2>Авторизация</h2>
			<p>
				Здравствуйте,
				<span class='big-text'>
					<a href='<?= $this->config->item('base_url') . $this->user->user_group ?>'><?= $this->user->user_login ?></a>
				</span>
			</p>
			<p>
				<a href='/profile'>Изменить личные данные</a>
			</p>
			<div class='submit'>
				<div>
					<input type='submit' value='Выйти' onclick="javascript:window.location='<?=
					$this->config->item('base_url') ?>user/logout';" >
				</div>
			</div>
		</div>
		<div class='right-block'>
			<!--p>
			</p-->
		</div>
		<div class='center-block'>
			<h3>
				ВАШ НОМЕР НА САЙТЕ: <?= $this->user->user_id ?>
			</h3>
			<br>
			<? if (isset($_SESSION['countrypost_balance'])) : ?>
			<p>
				<a href='<?= $this->config->item('base_url') . $this->user->user_group ?>/taxes'>Комиссия Countrypost: <?=
					$_SESSION['countrypost_balance'] ? $_SESSION['countrypost_balance'] : 0 ?></a>
			</p>
			<? endif; ?>
			<p>
				<a href='<?= $this->config->item('base_url') . $this->user->user_group ?>/history'>Статистика платежей</a>
			</p>
			<p>
				<a href='<?= $this->config->item('base_url') . $this->user->user_group ?>/payments'>Заявки на оплату заказов</a>
			</p>
		</div>
	</div>
</div>
<?
if (isset($extra_view))
{
	if ($extra_view == 'newBid')
	{
		if (isset($extra_data))
		{
			View::show('main/elements/orders/newBid', array(
				'order' => $extra_data['order'],
				'new_bid' => $extra_data['bid']
			));
		}
	}
}
?>
<script>
var user_id = <?= $this->user->user_id ?>;
var user_group = '<?= $this->user->user_group ?>';
</script>
