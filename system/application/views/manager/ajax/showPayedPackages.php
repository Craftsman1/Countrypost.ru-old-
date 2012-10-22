			<a name="pagerScroll"></a>
			<form id="pagerForm" class='admin-inside' action="<?=$selfurl?>updatePackagesTrackingNo" method="POST">
				<ul class='tabs'>
					<li><div><a href='<?=$selfurl?>showAddPackage'>Добавить посылку</a></div></li>
					<li><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
					<li class='active'><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
					<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
					<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
					<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
					<li><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
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
							<th>Номер клиента</th>
							<th>Номер посылки</th>
							<th>ФИО / Адрес доставки</th>
							<th>Способ доставки</th>
							<th>Цена доставки</th>
							<th>Статус</th>
							<th>Декларация</th>
							<th>Добавление Tracking № (Отправлен)</th>
						</tr>
						<?if ($packages) : foreach($packages as $package) : ?>
						<tr>
							<td><?=$package->package_client?></td>
							<td>
								<b>№ <?=$package->package_id?></b>
								<br />
								<nobr<? if (intval($package->package_day) > 6) : ?> class="red-color"<? endif; ?>>
									<?=$package->package_date?>
								</nobr>
								<br />
								<?=Func::round2half($package->package_weight)?>кг
								<br />
								<input class="package_weight" type="text" id="package_weight<?=$package->package_id?>" name="package_weight<?=$package->package_id?>" value="<?=$package->package_weight?>">&nbsp;кг
								<br />
								<?=$package->package_day == 0 ? "" : 'Прошло:<br />'.$package->package_day.' '.humanForm((int)$package->package_day, "день", "дня", "дней")?> <?=$package->package_hour == 0 ? "" : $package->package_hour.' '.humanForm((int)$package->package_hour, "час", "часа", "часов")?>
							</td>
							<td>
								<?=$package->package_address?>
								<br /><br />
								<? if (empty($package->order_id)) : ?>
								<b>самостоятельный заказ</b>
								<? else :?>
								<a href="<?= $selfurl.'showOrderDetails/'.$package->order_id ?>" ><b>Заказ <?= $package->order_id ?></b></a>
								<? endif; ?>
							</td>
							<td><?=$package->package_delivery_name ?></td>
							<td>$<?=$package->package_manager_cost?>
								<a href="javascript:void(0)" onclick="$('#pre_<?=$package->package_id?>').toggle()">Подробнее</a>
								<pre class="pre-href" id="pre_<?= $package->package_id ?>">
									<nobr>
										$<?= $package->package_delivery_cost ?><!-- стоимость доставки -->
										<img class="tooltip_package_delivery" src="/static/images/mini_help.gif" />
									</nobr>
									+
									<nobr>
										$<?= $package->package_manager_comission ?><!-- комиссия партнера -->
										<img class="tooltip_package_comission" src="/static/images/mini_help.gif" />
									</nobr>
									<? if ($manager->package_foto_tax AND $package->package_foto_cost) : ?>
									+
									<nobr>
										$<?= $package->package_foto_cost ?><!-- комиссия партнера за запросы фото от клиента -->
										<img class="tooltip_package_foto" src="/static/images/mini_help.gif" />
									</nobr>
									<? endif; ?>
									<? if ($manager->package_foto_system_tax AND $package->package_foto_cost_system) : ?>
									+
									<nobr>
										$<?= $package->package_foto_cost_system ?><!-- комиссия партнера за фото от партнера/админа -->
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
							</td>
							<td nowrap>
								<?= $package_statuses[$package->package_status] ?>
								<? if ($package->package_cost < $package->package_cost_payed) : ?>: $<?=$package->package_manager_cost_payed?>
								<br />
								<div class='float'>	
									<div class='submit' style="margin-right:0;">
										<div>
											<input type='button' onclick="refundItem('<?=$package->package_id?>');" value='Возместить $<?=$package->package_manager_cost_payed - $package->package_manager_cost?>' />
										</div>
									</div>
								</div>
								<? endif; ?>
								<? if ($package->package_cost > $package->package_cost_payed) : ?>
								<br />Доплатить $<?=$package->package_manager_cost - $package->package_manager_cost_payed?>
								<? endif; ?>
							</td>
							<td>
								<? if ($package->declaration_status == 'not_completed') : ?>
									<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Заполнить</a>
								<? else : ?>
									<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
								<? endif; ?>
							</td>
							<td nowrap align="center">
								<input type="text" name="package_trackingno<?=$package->package_id?>" /> 
								<input type="checkbox" id="package<?=$package->package_id?>" name="package<?=$package->package_id?>">
								<br />
								<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>">Подробнее</a>
								<? if ($package->comment_for_manager) : ?>
								<br />
								Добавлен новый комментарий
								<? endif; ?>
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