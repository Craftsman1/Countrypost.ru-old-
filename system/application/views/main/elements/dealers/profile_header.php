<div class='managerinfo admin-inside'>	<img src="/main/avatar_medium/<?= $manager->manager_user ?>" width="90px" height="90px">	<h2 style='margin-bottom: 10px;text-transform: none;'><?= $manager->statistics->fullname ?> (<?=		$manager->statistics->login ?>)	</h2>	<b style='color:orange;font-size:20px;margin-bottom: 20px;height: 10px;'>		<? if ($manager->is_cashback) : ?>		100% CASHBACK (Лимит на заказы: <?= $manager->cashback_limit ?> <?= $manager->statistics->currency ?>)		<? endif; ?>		&nbsp;	</b>	<ul class='tabs'>		<li class='active profile'><div><a class='profile' href='javascript:void(0);'>Профиль</a></div></li>		<li class='client_ratings'><div><a class='client_ratings' href='javascript:void(0);'>Отзывы			клиентов</a></div></li>		<li class='pricelist'><div><a class='pricelist' href='javascript:void(0);'>Тарифы</a></div></li>		<li class='payments'><div><a class='payments' href='javascript:void(0);'>Способы оплаты</a></div></li>		<li class='delivery'><div><a class='delivery' href='javascript:void(0);'>Доставка</a></div></li>		<? if ($manager->is_mail_forwarding) : ?>		<li class='mail_forwarding'><div><a class='mail_forwarding' href='javascript:void(0);'>Mail Forwarding</a></div></li>		<? endif; ?>	</ul></div><script>	$(function() {		$('ul.tabs a')			.click(function(e) {				e.preventDefault();				$('ul.tabs li').removeClass('active');				$('div.dealer_tab').hide();								$('div.' + $(e.target).attr('class')).show();								$(this).parent().parent().addClass('active');			});	});</script>