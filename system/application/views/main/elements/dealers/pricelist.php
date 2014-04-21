<div class="pricelist pricelist_main table dealer_tab" style="display:none;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div>
		<span>
			<b>ЗАКАЗ:</b>
		</span>
	</div>
	<div>
		<span>
			Комиссия за обычный заказ:
		</span>
		<span>
			<b><?= $manager->order_tax ?>%</b> (от общей стоимости товаров и местной доставки)
		</span>
	</div>
	<div>
		<span>
			Комиссия за самостоятельный заказ:
		</span>
		<span>
			<b><?= $manager->order_mail_forwarding_tax ?> <?= $manager->statistics->currency ?></b>  (Mail Forwarding)
		</span>
	</div>
	<div>
		<span>
			Минимальная комиссия за заказ:
		</span>
		<span>
			<b><?= $manager->min_order_tax ?> <?= $manager->statistics->currency ?></b>
		</span>
	</div>
	<br>
	<br>
	<div>
		<span>
			<b>ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ:</b>
		</span>
	</div>
	<div>
		<span>
			Объединение посылок (консолидация):
		</span>
		<span>
			<b><?= $manager->join_tax ?> <?= $manager->statistics->currency ?></b>
		</span>
	</div>
	<div>
		<span>
			Комиссия за одно фото:
		</span>
		<span>
			<b><?= $manager->foto_tax ?> <?= $manager->statistics->currency ?></b>
		</span>
	</div>
	<div style="margin: 0;">
		<span>
			Оформление страховки:
		</span>
		<span>
			<b><?= $manager->insurance_tax ?>%</b>
		</span>
	</div>
</div>
<div class="pricelist dealer_tab" style="display:none;">
	<h3>Подробное описание</h3>
	<div class="table">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<p>
			<?= empty($manager->pricelist_description) ? 'Нет описания.' : html_entity_decode($manager->pricelist_description) ?>
		</p>
	</div>
</div>