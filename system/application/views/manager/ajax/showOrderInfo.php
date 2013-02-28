<script>
	var is_closing_order = false;
	var noty_message = 'сохранен';

	function prepareOrderFormHandlers()
	{
		$("textarea#tracking_no").change(function() {
			$('#close_order,label[for=close_order]').show();
		});

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
				success('top', 'Заказ №<?= $order->order_id ?> успешно ' + noty_message + '!');

				if (is_closing_order)
				{
					$('select.order_status').val('completed');
				}
			},
			error: function(response)
			{
				$("#orderProgress").hide();
				error('top', 'Заказ №<?= $order->order_id ?> не ' + noty_message + '. Попробуйте еще раз.');
			}
		});
	}

	function closeOrder()
	{
		$('#close_order,label[for=close_order]')
				.attr('disabled', 'disabled')
				.attr('readonly', 'readonly');

		is_closing_order = true;
		noty_message = 'отправлен';
		$('#orderForm').attr('action', '<?= $selfurl ?>closeOrder/<?= $order->order_id ?>');
		$('#orderForm').submit();
	}

	function updateOrder()
	{
		is_closing_order = false;
		noty_message = 'сохранен';
		$('#orderForm').attr('action', '<?= $selfurl ?>updateOrder/<?= $order->order_id ?>');
		$('#orderForm').submit();
	}
</script>
<? View::show("/manager/ajax/showOrderInfoAjax"); ?>