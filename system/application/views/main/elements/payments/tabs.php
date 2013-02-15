<ul class='tabs'>
	<li <? if (strval($selected_submenu) == 'open_payments') : ?>class='active'<? endif; ?>>
		<div>
			<a href='javascript:goto_page("<?= $selfurl ?>showOpenPayments/<?= $order->order_id ?>/0");'>Новые <?=
				empty($open_orders2in) ?
					'(0)' :
					 "($open_orders2in)" ?></a>
		</div>
	</li>
	<li <? if (strval($selected_submenu) == 'payed_payments') : ?>class='active'<? endif; ?>>
		<div>
			<a href='javascript:goto_page("<?= $selfurl ?>showPayedPayments/<?= $order->order_id ?>/0");'>Выплаченные
				<?=
				empty($payed_orders2in) ?
					'(0)' :
					"($payed_orders2in)" ?></a>
		</div>
	</li>
</ul>