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