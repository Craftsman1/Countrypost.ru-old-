<? if ( ! empty($manager->manager_description)) : ?>
<div class="mail_forwarding dealer_tab" style="display:none;">
	<h3>Пример заполнения адреса</h3>
	<div class="table">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<p>
			<?= html_entity_decode($manager->manager_description) ?>
		</p>
	</div>
</div>
<? endif; ?>