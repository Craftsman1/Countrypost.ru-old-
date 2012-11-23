<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2 id='page_title'>Мои заказы</h2>
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