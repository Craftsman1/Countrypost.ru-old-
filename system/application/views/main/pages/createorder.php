<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
    <? View::show("main/elements/orders/$order_type"); ?>
	<h3>Товары в заказе:</h3>
	<? View::show('client/ajax/showOrderDetails'); ?>
	<? View::show('client/elements/orders/scripts'); ?>
</div>
<script>
	var currencies = <?= json_encode($countries); ?>;
	var selectedCurrency = '<?= isset($order_currency) ? $order_currency : '' ?>';
	var countryControl = false;

    $(function() {
		countryControl = $("select.country").msDropDown({mainCSS:'idd'});

		$('input#delivery_requested').change(function() {
			if ($(this).filter(':checked').length == 1)
			{
				$('.delivery_section').show('slow');
			}
			else
			{
				$('.delivery_section').hide('slow');
				$('input#city_to,input#preferred_delivery').val('');

				if (countryControl[1])
				{
					(countryControl[1]).selectedIndex = 0;
				}
			}
		});

		$('form.orderForm').ajaxForm({
			dataType: 'json',
			iframe: true,
			beforeSubmit: validateAndShowProgress_<?= $order_type ?>,
			error: errorAddProduct,
			complete: hideProgress,
			success: successAddProduct
		});

		$('form#orderForm').ajaxForm({
			beforeSubmit: validateAndShowProgress_<?= $order_type ?>,
			error: errorAddOrder,
			complete: hideProgress,
			resetForm: true,
			success: function(response) {
				if (response == '')
				{
					success('top', 'Заказ №<?= $order->order_id ?> успешно cформирован!');
					window.location = '<?= $this->config->item('base_url') . "client/order/$order->order_id" ?>';
				}
				else {
					window.location = '#';
					error('top', response);
				}
			}
		});
	});
</script>