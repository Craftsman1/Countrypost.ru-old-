<h3>О себе</h3>
<div class="table">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<p>
		<?= empty($manager->about_me) ? 'Нет описания.' : html_entity_decode($manager->about_me); ?>
	</p>
</div>
