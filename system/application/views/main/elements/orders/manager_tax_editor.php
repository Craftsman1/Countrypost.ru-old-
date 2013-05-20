<span>Комиссия посредника:</span>
<? if ($order->order_type == 'online' OR $order->order_type == 'offline') : ?>
<select name="manager_tax_type"
		class="manager_tax_type textbox"
		onchange="showTaxEditor();"
		<? if (isset($order->bid) AND $bid->manager_tax_type == 'custom') : ?>style="display:none;"<? endif; ?>>
	<option value="products_delivery"
			<? if (isset($order->bid) AND $bid->manager_tax_type == 'products_delivery') : ?>selected<? endif; ?>>стоимость товаров + местная
		доставка -
		<?= $order->manager_tax_percentage ?>%
		(<?= $order->products_delivery_tax ?> <?= $order->order_currency ?>)</option>
	<option value="products"
			<? if (isset($order->bid) AND $bid->manager_tax_type == 'products') : ?>selected<? endif; ?>>стоимость товаров -
		<?= $order->manager_tax_percentage ?>%
		(<?= $order->products_tax ?> <?= $order->order_currency ?>)</option>
	<option value="custom">указать другую комиссию</option>
</select>
<span <? if (empty($bid->manager_tax_type) OR $bid->manager_tax_type != 'custom') : ?>style="display:none;"<? endif; ?>
	  class="manager_tax_editor">
	<input type="text int"
		   name="manager_tax"
		   class="textbox manager_tax"
		   maxlength='11'>
	<b class="currency">
		<?= $order->order_currency ?>
	</b>
	<img src="/static/images/delete.png"
		 style="cursor: pointer; vertical-align: middle;"
		 class="show_type_selector"
		 onclick="showTypeSelector();"
		 title="отмена">
</span>
<script>
	function showTypeSelector()
	{
		$('.manager_tax_type')
			.val('products_delivery')
			.show();
		$('.manager_tax_editor').hide();

		manager_tax = products_delivery_tax;
		//refreshTotals();
		recalculateBid(edit_bid_id);
	}

	function showTaxEditor()
	{
		if ($('select.manager_tax_type').val() == 'custom')
		{
			$('.manager_tax_type').hide();
			$('.manager_tax_editor').show();

			manager_tax = parseFloat($('input.manager_tax').val());
		}
		else if ($('select.manager_tax_type').val() == 'products_delivery')
		{
			manager_tax = products_delivery_tax;
			$('input.manager_tax').val(manager_tax);
		}
		else if ($('select.manager_tax_type').val() == 'products')
		{
			manager_tax = products_tax;
			$('input.manager_tax').val(manager_tax);
		}

		//refreshTotals();
		recalculateBid(edit_bid_id);
	}
</script>
<? else : ?>
<span class="manager_tax_editor">
	<input type="text float"
		   name="manager_tax"
		   class="textbox manager_tax"
		   maxlength='11'>
	<b class="currency">
		<?= $order->order_currency ?>
	</b>
</span>
<? endif; ?>