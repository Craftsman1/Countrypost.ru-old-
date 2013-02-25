<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2>Все заявки на оплату</h2>
	<? View::show('admin/ajax/showAllOpenPayments'); ?>
</div>
<? View::show('/admin/elements/div_float_upload_bill'); ?>
<? View::show('admin/elements/payments/scripts'); ?>