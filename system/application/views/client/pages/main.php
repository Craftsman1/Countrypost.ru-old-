<script type="text/javascript" src="/static/js/jquery.form.js"></script>
<div class='content home'>
	<? if (isset($news) && count($news) > 0) : ?>
		<h2>Новости и объявления</h2>
		<div class='forward'>
			<a href='<?=$selfurl?>showNewsList'>
				<span>Все новости</span>
			</a>
		</div>
		<div class='news'>
		<? foreach ($news as $item) : ?>
			<div class='this-news'>
				<span class='date'>
					<?= date('d/m/Y', strtotime($item->news_addtime)) ?>
				</span>
				<a href='<?=$selfurl.'showNewsList/1/0/'.$item->news_id;?>' class='title'>
					<?= mb_strimwidth($item->news_body, 0 , 200, '...', 'UTF-8') ?>
				</a>
			</div>
		<? endforeach; ?>
		</div>
	<? endif; ?>		
	<? if ($just_registered) : ?>
		<h2>Добро пожаловать, <?=$user->user_login;?></h2>
		<span>
		Спасибо за регистрацию на Countrypost.ru<br/>
		Как сделать заказ Вы можете посмотреть <a href='/main/showHowItWork'>тут</a>.<br/>
		Чтобы сделать заказ самостоятельно на наш адрес, посмотрите все доступные адреса <a href='/client/showAddresses'>тут</a>.<br/>
		Перед тем, как сделать заказ, рекомендуем Вам пополнить счет заранее. Способы пополнения счета можно посмотреть <a href='#'>тут</a>.<br/> 
		Желаем приятного шопинга.
		</span>
	<? else : ?>
		<h2>Сделать заказ</h2>
		<div class='status-packet'>
			<a href='<?= $selfurl ?>showOpenPackages' class='add_package'>
				<em>Жду посылку на склад</em>
				<span>"Mail Forwarding"</span>
			</a>
			<a href='<?= $selfurl ?>showOpenOrders' class='order_processing'>
				<em>Добавить заказ</em>
				<span>"Помощь в покупке"</span>
			</a>
		</div>
		<h2>Статус посылок</h2>
		<div class='status-packet'>
			<a href='<?= $selfurl ?>showOpenPackages' class='package_processing'>
				<em>Посылки, ожидающие отправки</em>
				<span>Посылок: <?= $package_open ?></span>
			</a>
			<a href='<?= $selfurl ?>showPayedPackages' class='package_payed'>
				<em>Оплаченные посылки</em>
				<span>Посылок: <?= $package_payed ?></span>
			</a>
			<a href='<?=$selfurl?>showSentPackages' class='package_sent'>
				<em>Отправленные посылки</em>
				<span>Посылок: <?= $package_sent ?></span>
			</a>
		</div>
		<h2>Дополнительные услуги</h2>
		<? View::show('/client/elements/main/success'); ?>
		<? View::show('/client/elements/main/taobao_register'); ?>
		<? View::show('/client/elements/main/alipay_refill', array(
			'alipay_refill_tax' => $alipay_refill_tax,
			'cny_rate' => $cny_rate
			)); ?>
		<? View::show('/client/elements/main/taobao_payment', array(
			'taobao_payment_tax' => $taobao_payment_tax,
			'cny_rate' => $cny_rate
			)); ?>
		<div class='status-packet extra-service'>
			<a href='#' class='taobao_register'>
				<em>
					Регистрация аккаунта
					<br />
					на Taobao
				</em>
				<span>$<?= $taobao_register_tax ?></span>
			</a>
			<a href='#' class='alipay_refill'>
				<em>
					Пополнение Alipay
					<br />
					на любую сумму
				</em>
				<span><?= $alipay_refill_tax ?>%</span>
			</a>
			<a href='#' class='taobao_payment'>
				<em>
					Оплата Вашего
					<br />
					заказа (счета) на Taobao
				</em>
				<span><?= $taobao_payment_tax ?>%</span>
			</a>
		</div>
	<? endif; ?>
</div>
<script type="text/javascript">
	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]/;
		if (!regex.test(key))
		{
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
</script>