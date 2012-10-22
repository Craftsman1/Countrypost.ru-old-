			<a name="pagerScroll"></a>
			<form class='admin-inside' id="ordersForm" action="<?=$selfurl?>updateSentOrdersStatus" method="POST">
				<ul class='tabs'>
					<li><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
					<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
					<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
					<li class='active'><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
					<li><div><a href='<?=$selfurl?>showClients'>Клиенты</a></div></li>
					<li><div><a href='<?=$selfurl?>showPartners'>Партнеры</a></div></li>
				</ul>
				
				<div class='table'>
					<div class='angle angle-lt'></div>
					<div class='angle angle-rt'></div>
					<div class='angle angle-lb'></div>
					<div class='angle angle-rb'></div>
					<table>
						<col width='auto' />
						<col width='auto' />
						<col width='auto' />
						<col width='auto' />
						<col width='200' />
						<col width='auto' />
						<col width='80' />
						<col width='120' />
                        <tr>
							<th>Номер заказа</th>
							<th>Партнер, страна</th>
							<th>Номер клиента</th>
							<th>Номер посылки</th>
							<th>Общая стоимость с местной доставкой</th>
							<th>Статус</th>
							<th>Посмотреть / удалить</th>
						</tr>
						<?if ($orders) : foreach($orders as $order) : ?>
						<tr>
							<td nowrap>
								<b>№ <?=$order->order_id?></b><br /><?=$order->order_date?><br />
								Прошло:<br /><?=$order->package_day == 0 ? "" : $order->package_day.' '.humanForm((int)$order->package_day, "день", "дня", "дней")?> <?=$order->package_hour == 0 ? "" : $order->package_hour.' '.humanForm((int)$order->package_hour, "час", "часа", "часов")?>
							</td>
							<td><?=$order->order_manager_login?> / <?=$order->order_manager_country?></td>
							<td><b>№ <?=$order->order_client?></b></td>
							<td>
								<a href="#" onclick="return addPackage('<?=$order->order_id?>', '<?=$order->order_client?>', '<?=$order->order_manager?>');"><b>Добавить посылку</b></a>
								<? if ( ! empty($order->package_id)) : ?>
								<br />
								<a href="<?= $selfurl.'showPackageDetails/'.$order->package_id ?>" >№ <?= $order->package_id ?></a>
								<? endif; ?>
							</td>
							<td>
								<?=$order->order_cost?>$
								<a href="javascript:void(0)" onclick="$('#pre_<?=$order->order_id?>').toggle()">Подробнее</a>
								<pre class="pre-href" id="pre_<?=$order->order_id?>">
									$<?= $order->order_products_cost ?>
									<? if ($order->order_delivery_cost) : ?>
									+
									* $<?= $order->order_delivery_cost ?>
									<? endif;
									 if ($order->order_comission) : ?>
									+
									** $<?= $order->order_comission ?>
									<? endif; ?>
								</pre>
							</td>
							<td align="center">
								<select name="order_status<?=$order->order_id?>">
									<option value="proccessing" <? if ($order->order_status == 'proccessing') : ?>selected="selected"<?endif;?>>Обрабатывается</option>
									<option value="not_available" <? if ($order->order_status == 'not_available') : ?>selected="selected"<?endif;?>>Нет в наличии</option>
									<option value="not_available_color" <? if ($order->order_status == 'not_available_color') : ?>selected="selected"<?endif;?>>Нет данного цвета</option>
									<option value="not_available_size" <? if ($order->order_status == 'not_available_size') : ?>selected="selected"<?endif;?>>Нет данного размера</option>
									<option value="not_available_count" <? if ($order->order_status == 'not_available_count') : ?>selected="selected"<?endif;?>>Нет указанного кол-ва</option>
									<option value="not_payed" <? if ($order->order_status == 'not_payed') : ?>selected="selected"<?endif;?>>Не оплачен</option>
									<option value="payed" <? if ($order->order_status == 'payed') : ?>selected="selected"<?endif;?>>Оплачен</option>
									<option value="not_delivered" <? if ($order->order_status == 'not_delivered') : ?>selected="selected"<?endif;?>>Не доставлен</option>
									<option value="sended" <? if ($order->order_status == 'sended') : ?>selected="selected"<?endif;?>>Отправлен</option>
								</select>
								<? if ($order->order_cost < $order->order_cost_payed) : ?>
								<br />
								<br />
								$<?=$order->order_cost_payed?>								
								<div class='float'>	
									<div class='submit' style="margin-right:0;">
										<div>
											<input type='button' onclick="refundItem('<?=$order->order_id?>');" value='Возместить $<?=$order->order_cost_payed - $order->order_cost?>' />
										</div>
									</div>
								</div>
								<? endif; ?>
								<br />
								<? if ($order->comment_for_manager || $order->comment_for_client) : ?>
									Добавлен новый комментарий<br />
								<? endif; ?>
								<a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>#comments">Посмотреть</a>
							</td>
							<td align="center">
								<a href="<?=$selfurl?>showOrderDetails/<?=$order->order_id?>">Посмотреть</a><br/>
								<hr />
								<a href="javascript:deleteItem('<?=$order->order_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
								<br/>
							</td>
						</tr>
						<?endforeach; endif;?>
						<tr class='last-row'>
							<td colspan='9'>
								<div id="tableComments" style="text-align:left;float:left;">
									<br />
									* стоимость местной доставки<br />
									** комиссия за помощь в покупке<br />
								</div>
								<div class='float'>	
									<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
								</div>
							</td>
							<td></td>
						</tr>
					</table>
				</div>
			</form>
			<?php if (isset($pager)) echo $pager ?>