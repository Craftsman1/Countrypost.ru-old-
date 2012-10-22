			<form id="pagerForm" class='admin-inside' action='#'>
				<? View::show($viewpath.'elements/div_float_preview_package'); ?>
				<? View::show($viewpath.'elements/packages/tabs'); ?>
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
						<tr class='last-row'>
							<td colspan='9'>
								<div style="margin:0 0 10px 0;height:22px;">
									<div class='floatleft'>
										<? View::show($viewpath.'elements/packages/status_links', array('selected_submenu' => 'payed_packages')); ?>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<th>Номер посылки</th>
							<th>ФИО / Адрес доставки</th>
							<th>Способ доставки</th>
							<th>Декларация</th>
							<th>Оплачено</th>
							<th>Подробнее</th>
						</tr>
						<?if ($packages) : foreach($packages as $package) : ?>
						<tr>
							<td nowrap><b>№ <?=$package->package_id?></b><br/><?=$package->package_date?><br /><?=Func::round2half($package->package_weight)?>кг <?=Func::round2half($package->package_weight) != $package->package_weight ? '('.$package->package_weight.'кг)' : '';?><br />
						<?=$package->package_manager_country?>
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
                            <td><? if ($package->declaration_status == 'not_completed') : ?>
								Не заполнена
								<br />
								<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Заполнить</a>
							<? elseif ($package->declaration_status == 'completed' ||
										($package->declaration_status == 'help') && $package->package_declaration_cost) : ?>
								Заполнена
								<br />
								<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
							<? else : ?>
								Помощь в заполнении
								<br />
								<a href="<?=$selfurl?>showDeclaration/<?=$package->package_id?>">Посмотреть</a>
							<? endif; ?></td>
							<td>
								$<?=$package->package_cost?>
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
										<a href="<?= $selfurl ?>/removeInsurance/<?= $package->package_id ?>"><img class="tooltip_remove_package_insurance" src="/static/images/delete.png" /></a>
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
								<? if ($package->package_status == 'payed' && $package->package_cost > $package->package_cost_payed) : ?>
								<div class='float'>	
									<div class='submit' style="margin-right:0;">
										<div>
											<input type='button' onclick="repayItem('<?=$package->package_id?>');" value='Доплатить $<?=$package->package_cost - $package->package_cost_payed?>' />
										</div>
									</div>
								</div>
								<? endif; ?>
							</td>
							<td align="center">
								<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>">Подробнее</a>
								<br />
								<? if ($package->comment_for_client) : ?>
									Добавлен новый комментарий<br />
								<? endif; ?>
								<a href="<?=$selfurl?>showPackageDetails/<?=$package->package_id?>#comments">Комментарии</a>
							</td>
						</tr>
						<?endforeach; endif;?>						
					</table>
				</div>
			</form>
			<?= $pager ?>
<script>
	$(function(){
		put_package_hints();
		$('#page_title').html('Оплаченные посылки');
	});
</script>			