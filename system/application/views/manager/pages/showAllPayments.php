<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2>Все заявки на оплату</h2>
	<? View::show('manager/ajax/showAllOpenPayments'); ?>
</div>
<? View::show('manager/elements/payments/scripts'); ?>