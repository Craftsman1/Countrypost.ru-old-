<colgroup>
	<col style="width: 60px;">
	<col>
	<col>
	<col style="width: 85px;">
	<col style="width: 85px;">
	<col style="width: 85px;">
	<? if ($order->order_status != 'pending') : ?>
		<col style="width: 169px;">
	<? endif; ?>
	<col style="width: 44px">
</colgroup>
<tr>
	<th nowrap>
		№
		<? if ($is_editable AND
			! $order->is_creating) : ?>
			<input type='checkbox' id='select_all'>
		<? endif; ?>
	</th>
	<th>Товар</th>
	<th>Скриншот</th>
	<th>
		Стоимость
	</th>
	<th>
		Местная<br>доставка
	</th>
	<th>
		Вес<br>товара
	</th>
	<? if ($order->order_status != 'pending') : ?>
		<th>Статус</th>
	<? endif; ?>
	<? if ($is_editable) : ?>
		<th style="width:1px;"></th>
	<? endif; ?>
</tr>