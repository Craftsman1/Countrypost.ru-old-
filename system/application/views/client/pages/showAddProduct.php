<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Добавление товара в
		<a href="<?= $this->config->item('base_url') . "client/order/$order->order_id" ?>">заказ №<?= $order->order_id ?></a>
	</h2>
	<? View::show("/client/elements/showAddProduct/$order->order_type"); ?>
</div>
<script type="text/javascript">
	$(function() {
		// ссылка на скриншот
		$('.screenshot_link_box img').click(function() {
			$('.screenshot_link_box,.screenshot_uploader_box').hide('slow');
			$('.screenshot_switch').show('slow');
		});

		$('form.orderForm').ajaxForm({
			dataType:'json',
			iframe:true,
			beforeSubmit: showProgress,
			error: errorAddProduct,
			complete: hideProgress,
			success: function(response) {
				if (response)
				{
					if (response.error == 0)
					{
						success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в заказ.');
						window.location = '<?= $this->config->item('base_url') . "client/order/$order->order_id" ?>';
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
	});
</script>