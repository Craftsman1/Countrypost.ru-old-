<div class='content smallheader'>
	<h2>Посредники (<?= $this->paging_count ?>)</h2>
	<? View::show('main/ajax/showDealers', array(
		'managers' => $managers,
		'pager' => $pager)); ?>
</div>
<style>
.top-block {
	min-height: 0!important;
}
</style>