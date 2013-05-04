<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<? if ( ! $order_type) : ?>
    <h2 id='page_title'>Выберите вид заказа:</h2>
	<? View::show('main/elements/orders/order_type_selector'); ?>
	<? else : ?>
    <? View::show("main/elements/orders/$order_type"); ?>
	<h3>Товары в заказе:</h3>
	<? View::show('client/ajax/showOrderDetails'); ?>
	<? View::show('client/elements/orders/scripts'); ?>
	<? endif; ?>
</div>
<script>
	var currencies = <?= json_encode($countries); ?>;
	var selectedCurrency = '<?= isset($order_currency) ? $order_currency : '' ?>';

    $(function() {
		$('div.online_order').bind('click', function() {
            document.location = '/main/createorder/online';
        });

        $('div.offline_order').bind('click', function() {
            document.location = '/main/createorder/offline';
        });

        $('div.service_order').bind('click', function() {
            document.location = '/main/createorder/service';
        });

        $('div.delivery_order').bind('click', function() {
            document.location = '/main/createorder/delivery';
        });

        $('div.mail_forwarding_order').bind('click', function() {
            document.location = '/main/createorder/mail_forwarding';
        });

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

						$('div.checkout').show('slow');
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
					error('top', response.error);
				}
			}
		});
	});
</script>