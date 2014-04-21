<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Клиенты</h2>
	<? View::show('main/elements/clients/filter', array(
		'filter' => $clients_filter)); ?>
	<? View::show('main/ajax/showClients', array(
		'clients' => $clients,
		'pager' => $pager)); ?>
</div>
<style>
.top-block {
	min-height: 0!important;
}

.pages {
	margin-top: 30px;
	overflow: hidden;
}
</style>