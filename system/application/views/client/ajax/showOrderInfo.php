<script>
	function prepareOrderFormHandlers()
	{
		$('#orderForm').ajaxForm({
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#orderProgress").show();
			},
			success: function(response)
			{
				$("#orderProgress").hide();
				success('top', 'Заказ №<?= $order->order_id ?> успешно сохранен!');
			},
			error: function(response)
			{
				$("#orderProgress").hide();
				error('top', 'Заказ №<?= $order->order_id ?> не сохранен. Попробуйте еще раз.');
			}
		});
	}

	function updateOrder()
	{
		$('#orderForm').submit();
	}
</script>
<? View::show("/client/ajax/showOrderInfoAjax"); ?>

