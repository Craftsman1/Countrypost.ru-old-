<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Посредники</h2>
	<? View::show('main/elements/dealers/filter', array(
		'filter' => $dealers_filter)); ?>
	<? View::show('main/ajax/showDealers', array(
		'managers' => $managers,
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