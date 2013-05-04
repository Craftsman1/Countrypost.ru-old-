<?
$is_editable = in_array($order->order_status, $editable_statuses);
$is_joinable = ($is_editable AND isset($joinable_types[$order->order_type]));
$odetail_joint_id = 0;
$odetail_joint_count = 0;
?>
<div class='table centered_td centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table id="new_products" class="products">
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
			<? if ($order->order_type == 'mail_forwarding') : ?>
			<th>Tracking №</th>
			<? else : ?>
			<th>
				Стоимость
			</th>
			<th>
				Местная<br>доставка
			</th>
			<? if ($order->order_type != 'service') : ?>
			<th>
				Вес<br>товара
			</th>
			<? endif; ?>
			<? endif; ?>
			<? if ($order->order_status != 'pending') : ?>
			<th>Статус</th>
			<? endif; ?>
			<? if ($is_editable) : ?>
			<th style="width:1px;"></th>
			<? endif; ?>
		</tr>
		<? if ( ! empty($odetails)) : ?>
		<? foreach ($odetails as $odetail) : ?>
		<? View::show('client/ajax/odetail', array(
				'odetail'				=> $odetail,
				'odetail_joint_id'		=> $odetail_joint_id,
				'odetail_joint_count'	=> $odetail_joint_count,
				'is_joinable'			=> $is_joinable,
				'is_editable'			=> $is_editable,
				'order'					=> $order
			)); ?>
		<? if ($odetail->odetail_joint_id AND $odetail_joint_id != $odetail->odetail_joint_id) :
			$odetail_joint_id = $odetail->odetail_joint_id;
		endif; ?>
		<? endforeach; ?>
		<? else : ?>
		<tr class="missing_products">
			<td colspan="8" align="center">
				Товары не найдены.
			</td>
		</tr>
		<? endif; ?>
		<? if ($order->order_type != 'mail_forwarding') : ?>
		<tr <? if (empty($odetails)) : ?>style="display: none"<? endif; ?>>
			<td colspan="3">&nbsp;</td>
			<td class="price_total product_total">
				<b class="total_product_cost"><?= $order->order_products_cost ?></b>&nbsp;<b class="currency"><?=
				$order->order_currency ?></b>
			</td>
			<td class="delivery_total product_total">
				<b class="total_delivery_cost"><?= $order->order_delivery_cost ?></b>&nbsp;<b class="currency"><?= $order->order_currency ?></b>
			</td>
			<? if ($order->order_type != 'service') : ?>
			<td class="weight_total">
				<b class="total_weight"><?= $order->order_weight ?></b> кг
			</td>
			<? endif; ?>
			<td <? if ($is_editable AND $order->order_type != 'service') : ?>colspan="2"<? endif; ?>>&nbsp;</td>
		</tr>
		<? endif; ?>
		<? // BOF: кнопки
		if ($is_editable) : ?>
		<tr class="last-row">
			<td colspan="8">
				<? if ( ! $order->is_creating) : ?>
				<div class="admin-inside float-left">
					<div class="submit">
						<div>
							<input type="button" value="Добавить товар" onclick="addItem(<?= $order->order_id ?>);">
						</div>
					</div>
				</div>
				<? endif; ?>
				<? if ($order->is_creating) : ?>
				<div class="admin-inside checkout" <? if (empty($odetails)) : ?>style="display: none;"<? endif; ?>>
					<div class="submit">
						<div>
							<input type="button" value="Сформировать заказ" onclick="checkout();">
						</div>
					</div>
				</div>
				<? endif; ?>
				<? if ($is_joinable) : ?>
				<div class="admin-inside float-left">
					<div class="submit">
						<div>
							<input type="button" value="Объединить доставку" onclick="joinProducts();">
						</div>
					</div>
				</div>
				<img class="float-left"
					 id="joinProgress"
					 style="display:none;margin:0px;margin-top:5px;"
					 src="/static/images/lightbox-ico-loading.gif">
				<? endif ?>
				<? if ( ! empty($neighbour_orders)) : ?>
				<div class="admin-inside floatright">
					<div class="submit floatright" style="margin: 7px 0 0 6px;">
						<div>
							<input type="button" value="Перенести" onclick="moveItems(<?= $order->order_id ?>);">
						</div>
					</div>
				</div>
				<div class="floatright" style="margin: 14px 0 0 0;">
					Перенести <b class="move_count">0</b> товаров в заказ №
					<select id="move_to_order" style="margin-top: 0;">
						<option value="0">выберите номер...</option>
						<? foreach ($neighbour_orders as $neighbour) : ?>
						<option value="<?= $neighbour ?>"><?= $neighbour ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<? endif; ?>
			</td>
		</tr>
		<? endif;
		// EOF: кнопки ?>
	</table>
</div>