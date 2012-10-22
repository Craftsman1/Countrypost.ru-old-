<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<h3>Заказы “Помощь в покупке”</h3>
	<? View::show($viewpath.'elements/order_filter', array(
		'handler' => 'filterOpenOrders',
		'show_status_filter' => TRUE
	)); ?>
	<? View::show($viewpath.'ajax/showOpenOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
	<h3>Все новые заказы</h3>
	<br />
	<? View::show($viewpath.'ajax/showUnassignedOrders', array(
		'orders' => $unassigned_orders)); ?>
</div>
<script type="text/javascript">
	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить заказ №" + id + "?")){
			window.location.href = '<?=$selfurl?>deleteOrder/' + id;
		}
	}

	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]/;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
</script>