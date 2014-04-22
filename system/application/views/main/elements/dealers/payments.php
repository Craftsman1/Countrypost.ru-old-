<div class="payments table dealer_tab" style="display:none;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<p>
		<?= empty($manager->payments_description) ? 'Нет описания.' : html_entity_decode($manager->payments_description); ?>
	</p>
</div>