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
			Комиссия за заказ:
		</span>
		<span>
			<b><?= $manager->order_tax ?>%</b>
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
			Комиссия за фото:
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