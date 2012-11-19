<a name="pagerScroll"></a>
<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2 id='page_title'>Мои заказы</h2>
	<? View::show($viewpath.'elements/orders/errors'); ?>
	<? View::show($viewpath.'elements/div_float_help'); ?>
	<? View::show($viewpath.'elements/div_float_manual'); ?>
	<? View::show($viewpath.'elements/div_order_preview'); ?>
	<? View::show($viewpath.'elements/orders/new_order_warning'); ?>
	<? View::show($viewpath.'elements/orders/import_excel'); ?>
	<div class="admin-inside" style="height:50px;" id='add_order_button'>
		<div class="submit">
			<div>
				<input type="button" onclick="window.location = '/main/createorder';" value="Добавить заказ">
			</div>
		</div>
	</div>
	<? View::show($viewpath.'ajax/showOpenOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
</div>
<? View::show($viewpath.'elements/orders/scripts'); ?>