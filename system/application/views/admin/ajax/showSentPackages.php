			<a name="pagerScroll"></a>
			<form class='admin-inside' id="packagesForm" action="<?=$selfurl?>updateSentPackagesStatus" method="POST">
				<ul class='tabs'>
					<li><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
					<li class='active'><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
					<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
					<li><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
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
						<col width='auto' />
						<col width='auto' />
						<col width='auto' />
						<col width='auto' />
						<tr>
							<th>Номер посылки</th>
							<th>Партнер, страна</th>
							<th>ФИО / Адрес доставки</th>
							<th>Способ доставки</th>
							<th>Цена доставки</th>
							<th>Декларация / Страховка</th>
							<th>Tracking номер</th>
							<th>Статус</th>
						</tr>
						<? if ($packages) : foreach($packages as $package) : ?>
						<tr>
							<td>
								<b>№ <?=$package->package_id?></b>
								<br />
								<nobr<? if (intval($package->package_day) > 6) : ?> class="red-color"<? endif; ?>>
									<?=$package->package_date?>
								</nobr>
								<br />
								<input class="package_weight" type="text" id="package_weight<?=$package->package_id?>" name="package_weight<?=$package->package_id?>" value="<?=$package->package_weight?>">&nbsp;кг
								<br />
								Прошло:
								<br />
								<?=$package->package_day == 0 ? "" : $package->package_day.' '.humanForm((int)$package->package_day, "день", "дня", "дней")?> <?=$package->package_hour == 0 ? "" : $package->package_hour.' '.humanForm((int)$package->package_hour, "час", "часа", "часов")?>
							</td>
							<td><?=$package->package_manager_login?> / <?=$package->package_manager_country?></td>
							<td>
								<b>№ <?=$package->package_client?></b>&nbsp;<?=$package->package_address?>
								<br /><br />
								<? if (empty($package->order_id)) : ?>
								<b>самостоятельный заказ</b>
								<? else :?>
								<a href="<?= $selfurl.'showOrderDetails/'.$package->order_id ?>" ><b>Заказ <?= $package->order_id ?></b></a>
								<? endif; ?>
							</td>
							<td><?=$package->package_delivery_name ?></td>
							<td><? if (!$package->package_delivery_cost || $package->declaration_status == 'not_completed') : ?><? else : ?>
								<?=$package->package_cost?>$
								<a href="javascript:void(0)" onclick="$('#pre_<?=$package->package_id?>').toggle()">Подробнее</a>
								<pre class="pre-href" id="pre_<?= $package->package_id ?>">
									<nobr>
										$<?= $package->package_delivery_cost ?><!-- стоимость доставки -->
										<img class="tooltip_package_delivery" src="/static/images/mini_help.gif" />
									</nobr>
									+
									<nobr>
										$<?= $package->package_comission ?><!-- комиссия за пересылку -->
										<img class="tooltip_package_comission" src="/static/images/mini_help.gif" />
									</nobr>
									<? if ($package->package_join_cost) : ?>
									+
									<nobr>
										$<?= $package->package_join_cost ?><!-- комиссия за объединение посылок -->
										<img class="tooltip_package_join" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_declaration_cost) : ?>
									+
									<nobr>
										$<?= $package->package_declaration_cost ?><!-- комиссия за декларацию -->
										<img class="tooltip_package_declaration" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_insurance_comission) : ?>
									+
									<nobr>
										$<?= $package->package_insurance_comission ?><!-- комиссия за страховку -->
										<img class="tooltip_package_insurance" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_foto_cost) : ?>
									+
									<nobr>
										$<?= $package->package_foto_cost ?><!-- комиссия за запросы фото от клиента -->
										<img class="tooltip_package_foto" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_foto_cost_system) : ?>
									+
									<nobr>
										$<?= $package->package_foto_cost_system ?><!-- комиссия за фото от партнера/админа -->
										<img class="tooltip_package_foto_system" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($package->package_special_cost_usd) : ?>
									+
									<nobr>
										$<?= $package->package_special_cost_usd ?><!-- комиссия за доп.услуги -->
										<img class="tooltip_package_special_cost" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
								</pre>
								<? endif; ?>
							</td>
							<td>
								<? if ($package->declaration_status == 'not_completed') : ?>
									<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Заполнить</a>
								<? else : ?>
									<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
								<? endif; ?>
							</td>
							<td><b><?=$package->package_trackingno?></b></td>
							<td align="center">
								<select name="package_status<?=$package->package_id?>">
									<? foreach ($package_statuses as $key => $value) : ?>
									<option value="<?= $key ?>"<?= ($key == $package->package_status) ? ' selected' : '' ?>>
										<?= $value ?>
									</option>
									<? endforeach; ?>
								</select>
								<? if ($package->package_cost < $package->package_cost_payed) : ?><br />$<?=$package->package_cost_payed?>
								<br />
								<div class='float'>	
									<div class='submit' style="margin-right:0;">
										<div>
											<input type='button' onclick="refundItem('<?=$package->package_id?>');" value='Возместить $<?=$package->package_cost_payed - $package->package_cost?>' />
										</div>
									</div>
								</div>
								<? endif; ?>
								<br />
								<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>">Подробнее</a>
								<? if ($package->comment_for_client OR $package->comment_for_manager) : ?>
								<br />
								Добавлен новый комментарий
								<? endif; ?>
								<hr />
								<a href="javascript:deleteItem('<?=$package->package_id?>');" class='delete'>
									<img title="Удалить" border="0" src="/static/images/delete.png">
								</a>
							</td>
						</tr>
						<?endforeach; endif;?>
						<tr class='last-row'>
							<td colspan='9'>
								<div class='float'>	
									<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</form>
			<? if (isset($pager)) echo $pager ?>
<script>
	$(function() {
		put_package_hints();
	});
</script>