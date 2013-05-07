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

    $(function() {
		$("select.country").msDropDown({mainCSS:'idd'});

		$('form.orderForm').ajaxForm({
			dataType: 'json',
			iframe: true,
			beforeSubmit: showProgress,
			error: errorAddProduct,
			complete: hideProgress,
			resetForm: true,
			success: function(response) {
				if (response)
				{
					if (response.error == 0)
					{
						success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в заказ.');
						$('table.products tr:first').after(response.product);

						$('div.checkout,tr.totals').show('slow');
						$('tr.missing_products').hide();
					}
					else {
						error('top', response.error);
					}
				}
				else
				{
					errorAddProduct();
				}
			}
		});

		$('form#orderForm').ajaxForm({
			beforeSubmit: showProgress,
			error: errorAddOrder,
			complete: hideProgress,
			resetForm: true,
			success: function(response) {
				if (response == '')
				{
					success('top', 'Заказ №<?= $order->order_id ?> успешно cформирован!');
					window.location = '<?= BASEURL . "client/order/$order->order_id" ?>';
				}
				else {
					window.location = '#';
					error('top', response);
				}
			}
		});
	});
</script>