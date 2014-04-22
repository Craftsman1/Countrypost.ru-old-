			<a name="pagerScroll"></a>
			<form class='admin-inside' id="ordersForm" action="<?=$selfurl?>updateSentOrdersStatus" method="POST">
				<ul class='tabs'>
					<li class='active'><div><a href='<?=$selfurl?>extraPayments'>Выполненные</a></div></li>
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
							<th>Номер</th>
							<th>Отправитель</th>
							<th>Получатель</th>
							<th>Способ оплаты</th>
							<th>Назначение</th>
							<th>Комментарий</th>
							<th>Сумма ($)</th>
							<th>Сумма (руб.)</th>
							<th>Комиссия</th>
							<th></th>
						</tr>
						<?if ($payments) : foreach($payments as $payment) : ?>
						<tr>
							<td nowrap>
								<b>№ <?=$payment->extra_payment_id?></b>
								<br />
								<?=$payment->extra_payment_date?>
							</td>
							<td>
								<?if ($payment->extra_payment_from) : ?>
								<?if ($payment->extra_payment_from == 1) : ?>
								Администратор
								<? elseif (isset($payment->extra_payment_from_login)) : ?>
								Партнер: <b><?=$payment->extra_payment_from_login?></b>
								<? else : ?>
								Клиент: <b><?=$payment->extra_payment_from?></b>
								<? endif; ?>
								<? else : ?>
								-
								<? endif; ?>
							</td>
							<td>
								<?if ($payment->extra_payment_to) : ?>
								<?if ($payment->extra_payment_to == 1) : ?>
								Администратор
								<? elseif (isset($payment->extra_payment_to_login)) : ?>
								Партнер: <b><?=$payment->extra_payment_to_login?></b>
								<? else : ?>
								Клиент: <b><?=$payment->extra_payment_to?></b>
								<? endif; ?>
								<? else : ?>
								-
								<? endif; ?>
							</td>
							<td>
								<?=isset($payment->extra_payment_type) ? $payment->extra_payment_type : '-'?>
							</td>
							<td>
								<?=isset($payment->extra_payment_purpose) ? $payment->extra_payment_purpose : '-'?>
							</td>
							<td>
								<?=isset($payment->extra_payment_comment) ? $payment->extra_payment_comment : '-'?>
							</td>
							<td>
								<b>$<?=$payment->extra_payment_amount?>
								<? if ($payment->extra_payment_amount_local) : ?>
								<br /><?=$payment->extra_payment_currency.(strpos($payment->extra_payment_currency, ';') === FALSE ? ';' : '').$payment->extra_payment_amount_local?>
								<? endif; ?></b>
							</td>
							<td>
								<?=isset($payment->extra_payment_amount_ru) ? $payment->extra_payment_amount_ru.' руб.' : '-'?>
							</td>
							<td>
								<?=isset($payment->extra_payment_comission) ? $payment->extra_payment_comission : '-'?>
								<?=isset($payment->extra_payment_comission_local) ? '<br />'.$payment->extra_payment_comission_local : ''?>							</td>
							<td align="center">
								<a href="javascript:deleteItem('<?=$payment->extra_payment_id?>');"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
							</td>
						</tr>
						<?endforeach; else : ?>
						<tr class="last-row">
							<td colspan="10">
								<br />Дополнительных платежей не найдено.
							</td>
						</tr>
						<?endif;?>
					</table>
				</div>
			</form>
			<?php if (isset($pager)) echo $pager ?>