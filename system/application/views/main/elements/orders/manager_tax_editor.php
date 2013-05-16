<span>
	Комиссия посредника:
</span>
<? if ($order->order_type == 'online' OR $order->order_type == 'offline') : ?>
<select name="">
	<option ></option>
</select>
<? else : ?>
<span class="manager_tax_editor">
	<input type="text int"
		   name="manager_tax"
		   class="textbox manager_tax"
		   maxlength='11'>
	<b class="currency">
		<?= $order->order_currency ?>
	</b>
</span>
<? endif; ?>
<!--
<span style="display: none;" class="manager_tax_editor">
	<input type="text int"
		   name="manager_tax"
		   class="textbox manager_tax"
		   maxlength='11'>
	<b class="currency">
		<?= $order->order_currency ?>
	</b>
	или
	<input type="text int"
		   name="manager_tax_percentage"
		   class="textbox manager_tax_percentage"
		   maxlength='3'
		   style="width: 40px;">
	<b class="percent">
		%&nbsp;&nbsp;от общей стоимости товаров и местной доставки
	</b>

</span>
<span class="manager_tax_plaintext">
	<b class="manager_tax_percentage"></b>%
	(<b class="manager_tax"></b> <?= $order->order_currency ?>)
	<a href="javascript:editManagerTax();">изменить</a>
</span>
-->