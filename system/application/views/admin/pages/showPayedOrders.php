<div class='content'>
	<? View::show($viewpath.'elements/div_float_new_package'); ?>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<h3>Оплаченные заказы</h3>
	<? View::show($viewpath.'elements/order_filter', array(
		'handler' => 'filterPayedOrders',
	)); ?>
	<? View::show($viewpath.'ajax/showPayedOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
</div>
<script type="text/javascript">
	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить заказ №" + id + "?")){
			window.location.href = '<?=$selfurl?>deleteOrder/' + id;
		}
	}
	
	function refundItem(id) {
		if (confirm("Возместить клиенту заказ №" + id + "?")){
			window.location.href = '<?=$selfurl?>refundOrder/' + id;
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