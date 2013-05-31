<form class='admin-inside' action="/manager/addBid/<?= $order->order_id ?>/<?= $bid->bid_id ?>" id="editBid"	  method="POST">	<input type="hidden" name="extra_tax_counter" class="extra_tax_counter" value="<?= empty($bid->extra_taxes) ?		0 :	count($bid->extra_taxes) ?>">	<input type="hidden" name="comment" value="">	<div class=''>		<h3 style="margin-top: 20px;">Расходы по заказу:</h3>		<div class="new_bid_box">			<div <? if ($order->order_type != 'online' AND					$order->order_type != 'offline') : ?>style="display: none;"<? endif; ?>>				<span class="bold">					Стоимость товаров в заказе + местная доставка:				</span>				<span>					<b class="order_products_cost"></b>					<?= $order->order_currency ?>				</span>			</div>			<div>				<span>					Комиссия Countrypost:				</span>				<span>					<b class="countrypost_tax"></b>					<?= $order->order_currency ?>				</span>			</div>			<div>				<? View::show('main/elements/orders/manager_tax_editor'); ?>			</div>			<div>				<span>					Фото товаров (<b class="requested_foto_count"></b> шт.):				</span>				<span style="display: none" class="foto_tax_editor">					<input type="text int"						   name="foto_tax"						   class="textbox manager_foto_tax"						   maxlength='11'>					<b class="currency">						<?= $order->order_currency ?>					</b>				</span>				<span class="foto_tax_plaintext">					<b class="manager_foto_tax"></b>					<?= $order->order_currency ?>					<a href="javascript:editFotoTax();">изменить</a>				</span>			</div>		</div>		<? if ($order->order_country_to) : ?>		<div class="new_bid_box">			<div>				<span class="bold">					Доставка <b class="order_weight"></b>кг в:					<?= $order->order_city_to ?>					(<?= $order->order_country_to ?>)				</span>			</div>			<div>				<span>					Способ доставки:				</span>				<span>					<input type="text"						   class="textbox order_delivery_name"						   name="delivery_name"						   value="<?= $order->preferred_delivery ?>">				</span>			</div>			<div>				<span>					Стоимость международной доставки:				</span>				<span>					<input type="text"						   name="delivery_cost"						   class="textbox int order_delivery_cost"						   id="delivery_total"						   maxlength='11'>					<b class="currency">						<?= $order->order_currency ?>					</b>				</span>			</div>		</div>		<? endif; ?>		<div class="new_bid_box">			<div>				<span class="bold">					<b>Дополнительные расходы по заказу:</b>				</span>				<span>					<b class="extra_tax"></b>					<?= $order->order_currency ?>				</span>			</div>			<div class="extra_tax_box template" style="display: none;">				<span>					<input type="text"						   class="textbox extra_tax_name"						   name="extra_tax_name"						   maxlength="255"						   style="width: 253px;">				</span>				<span>					<input type="text"						   class="textbox extra_tax_value int"						   maxlength="11"						   name="extra_tax_value">					<b class="currency">						<?= $order->order_currency ?>					</b>					<img src="<?= IMG_PATH ?>delete.png"						 style="cursor: pointer; vertical-align: middle;"						 title="удалить"						 onclick="removeExtraTax(this);">				</span>			</div>			<? if ( ! empty($bid->extra_taxes)) : $i = 0; foreach ($bid->extra_taxes as $extra_tax) : ?>			<div class="extra_tax_box">				<span>					<input type="text"						   class="textbox extra_tax_name"						   name="extra_tax_name<?= $i ?>"						   maxlength="255"						   style="width: 253px;"						   value="<?= $extra_tax->extra_name ?>"						   onchange="updateExtraTax();">				</span>				<span>					<input type="text"						   class="textbox extra_tax_value int"						   maxlength="11"						   name="extra_tax_value<?= $i++ ?>"						   value="<?= $extra_tax->extra_tax ?>"						   onkeyup="validate_float(this);"						   onkeydown="validate_float(this);"						   onchange="updateExtraTax();">					<b class="currency">						<?= $order->order_currency ?>					</b>					<img src="<?= IMG_PATH ?>delete.png"						 style="cursor: pointer; vertical-align: middle;"						 title="удалить"						 onclick="removeExtraTax(this);">				</span>			</div>			<? endforeach; endif; ?>			<div>				<span>					<img src="<?= IMG_PATH ?>plus.png"						 style="cursor: pointer; vertical-align: middle;"						 onclick="addExtraTax();">					<a href="javascript:addExtraTax();">Добавить</a>				</span>			</div>		</div>	</div>	<div style="height: 37px;" class="admin-inside">		<div class="submit">			<div>				<input type="button" value="Сохранить предложение" onclick="updateBid(<?= $bid->bid_id ?>);">			</div>		</div>		<div class="submit">			<div>				<input type="button" value="Отмена" onclick="cancelEditBid(<?= $bid->bid_id ?>);">			</div>		</div>		<div class='floatleft'>			<img class="float bid_progress" style="display:none;margin:0px;margin-top:5px;"				 src="/static/images/lightbox-ico-loading.gif"/>		</div>	</div></form><script type="text/javascript">var countrypost_tax = <?= $bid->countrypost_tax ?>;var manager_tax = <?= $bid->manager_tax ?>;var manager_tax_percentage = <?= $bid->manager_tax_percentage ?>;var min_order_tax = <?= $order->min_order_tax ?>;var products_delivery_tax = <?= $order->products_delivery_tax ?>;var products_tax = <?= $order->products_tax ?>;var manager_foto_tax = <?= $bid->foto_tax ?>;var manager_foto_tax_percentage = <?= $bid->foto_tax_percentage ?>;var requested_foto_count = <?= $bid->requested_foto_count ?>;var order_total_cost = <?= $bid->total_cost ?>;var order_products_cost = <?= $order->order_products_cost + $order->order_delivery_cost ?>;var order_delivery_cost = <?= $bid->delivery_cost ?>;var order_delivery_name = '<?= $bid->delivery_name ?>';var order_weight = <?= $order->order_weight ?>;var order_currency = '<?= $order->order_currency ?>';var extra_tax = <?= $bid->extra_tax ?>;var extra_tax_counter = <?= empty($bid->extra_taxes) ? 0 : count($bid->extra_taxes) ?>;var extra_tax_hint = '<?=  empty($extras) ? 'Дополнительные расходы' : implode(', ', $extras) ?>';var edit_bid_id = <?= $bid->bid_id ?>;</script>