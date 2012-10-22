<a name="pagerScroll"></a>
<form method="POST" action="/syspay/showGate" id="pagerForm">
	<ul class='tabs'>
		<li class='active'><div><a href='<?=$selfurl?>showOpenOrders2In'>Новые</a></div></li>
		<li><div><a href='<?=$selfurl?>showPayedOrders2In'>Выплаченные</a></div></li>
	</ul>

	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		
		<?if(isset($Orders2In) && count($Orders2In)):?>
		<table>
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<tr>
				<th>№ заявки</th>
				<th>Способ оплаты</th>
				<th>Статус заявки</th>
				<th>Комментарии</th>
				<th>Сумма пополнения</th>
				<th>Сумма перевода</th>
				<th width="1">Удалить</th>
			</tr>
			<? foreach ($Orders2In as $order) : ?>
			<tr>
				<td><b>№ <?=$order->order2in_id;?></b>
					<br />
					<?= date("d.m.Y H:i", strtotime($order->order2in_createtime)) ?>
				</td>
				<td>
					<? foreach ($services as $service) : 
					if ($service->payment_service_id == $order->order2in_payment_service) : ?>
					<u><?= $service->payment_service_name ?></u>
					<br />
					<? break; endif; endforeach; ?>
					<? if ($order->order2in_payment_service == 'bm' OR
						$order->order2in_payment_service == 'pb' OR
						$order->order2in_payment_service == 'bta' OR
						$order->order2in_payment_service == 'ccr' OR
						$order->order2in_payment_service == 'kkb' OR
						$order->order2in_payment_service == 'nb' OR
						$order->order2in_payment_service == 'tb' OR
						$order->order2in_payment_service == 'atf' OR
						$order->order2in_payment_service == 'ab' OR
						$order->order2in_payment_service == 'sv' OR
						$order->order2in_payment_service == 'vtb') : ?>
					<b>Номер карты:</b>
					<?= $order->order2in_details ?>
					<br />
					<? elseif ($order->order2in_payment_service == 'rbk' OR
						$order->order2in_payment_service == 'qw') : ?>
					<b>Номер кошелька:</b>
					<?= $order->order2in_details ?>
					<br />
					<? elseif ($order->order2in_payment_service == 'mb') : ?>
					<b>Email отправителя:</b>
					<?= $order->order2in_details ?>
					<br />
					<? else : ?>
					<?= $order->order2in_details ?>
					<br />
					<? endif; ?>
					
					<? if ($order->order2in_payment_service == 'bm' OR
							$order->order2in_payment_service == 'cc' OR
							$order->order2in_payment_service == 'so' OR
							$order->order2in_payment_service == 'op' OR
							$order->order2in_payment_service == 'pb' OR
							$order->order2in_payment_service == 'bta' OR
							$order->order2in_payment_service == 'ccr' OR
							$order->order2in_payment_service == 'kkb' OR
							$order->order2in_payment_service == 'nb' OR
							$order->order2in_payment_service == 'tb' OR
							$order->order2in_payment_service == 'atf' OR
							$order->order2in_payment_service == 'ab' OR
							$order->order2in_payment_service == 'sv' OR
							$order->order2in_payment_service == 'vtb') : ?>
					<b>Скриншот:</b>
					<? if (isset($Orders2InFoto[$order->order2in_id])) : ?>
					<a href="javascript:void(0)" onclick="setRel(<?=$order->order2in_id?>)">
						Посмотреть&nbsp;(<?=count($Orders2InFoto[$order->order2in_id]);?>)<?
						foreach ($Orders2InFoto[$order->order2in_id] as $o2iFoto) : ?><a rel="lightbox_<?=$order->order2in_id?>" href="/client/showOrder2InFoto/<?=$order->order2in_id?>/<?=$o2iFoto?>" style="display:none;">Посмотреть</a><? endforeach; ?></a>
					<br />
					<? endif; ?>
					<a href="javascript:uploadBillFoto(<?=$order->order2in_id?>);">Добавить</a>
					<div style="line-height:21px;display:none;padding-top:10px;" id="scans_<?=$order->order2in_id?>">
					<? if (isset($Orders2InFoto[$order->order2in_id])): ?>
						&nbsp;<?foreach ($Orders2InFoto[$order->order2in_id] as $o2iFoto):?><a href="/client/deleteBillFoto/<?=$order->order2in_id?>/<?=$o2iFoto?>" style="margin-right:8px;"><? $file = parse_url($o2iFoto); echo $file['path'];?><img src="/static/images/delete.png"/></a>&nbsp;<?endforeach;?>
					<? endif; ?>
					</div>
					<? endif; ?>
				</td>
				<td><?=$Orders2InStatuses[$order->order2in_status];?></td>
				<td>
					<a href="/client/showO2iComments/<?=$order->order2in_id;?>">Посмотреть</a>
					<?if ((isset($client) && $order->order2in_2clientcomment) OR ($this->user->user_group == 'admin' && $order->order2in_2admincomment)):?>
						<br />Добавлен новый коментарий
					<?endif;?>
				</td>
				<td>
					$<?= $order->order2in_amount ?>
				</td>
				<td>
					<? if ( ! empty($order->order2in_amount_local)) : ?>
					<?= $order->order2in_amount_local . $order->order2in_currency ?>
					<? elseif ( ! empty($order->order2in_amount_rur)) : ?>
					<?= $order->order2in_amount_rur . "руб." ?>
					<? elseif ( ! empty($order->order2in_amount_kzt)) : ?>
					<?= $order->order2in_amount_kzt . '<em class="tenge">&nbsp;&nbsp;&nbsp;</em>' ?>
					<? else : ?>
					-
					<? endif; ?>
				</td>
				<td align="center">
					<? if ($order->order2in_status != 'payed') : ?>
						<a href="/client/deleteOrder2In/<?=$order->order2in_id;?>"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					<? endif; ?>
				</td>
			</tr>
			<? endforeach; ?>
		</table>
		<? else : ?>
			<div align="center">Заявки отсутствуют</div>
		<? endif; ?>
	</div>
	<?php if (isset($pager)) echo $pager ?>
</form>