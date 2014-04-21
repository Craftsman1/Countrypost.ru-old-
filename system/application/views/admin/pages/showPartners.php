<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<h3>Партнеры</h3>
	<div class="h2_link">
		<a href='<?= $selfurl ?>showAddPartner'>Добавить нового партнера</a>
	</div>
	<br />
	<? View::show($viewpath.'ajax/showPartners', array(
		'managers' => $managers,
		'pager' => $pager)); ?>
</div>