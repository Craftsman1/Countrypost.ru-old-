<ul class='tabs'>
	<li <? if (strval($selected_submenu) == 'open_orders') : ?>class='active'<? endif; ?>>
		<div>
			<a  name='new' id='new' href='javascript:goto_page("<?= $selfurl  ?>showOpenOrders/0/ajax");'>Новые (<?=  empty($new_orders) ?
			0 : $new_orders ?>)</a>
		</div>
	</li>
	<li <? if (strval($selected_submenu) == 'payed_orders') : ?>class='active'<? endif; ?>>
	<div>
			<a name='payed' id='payed' href='javascript:goto_page("<?= $selfurl  ?>showPayedOrders/0/ajax");'>Оплаченные (<?=  empty($payed_orders) ? 0 : $payed_orders ?>)</a>
		</div>
	</li>
	<li <? if (strval($selected_submenu) == 'sent_orders') : ?>class='active'<? endif; ?>>
	<div>
			<a name='submitted' id='submitted' href='javascript:goto_page("<?= $selfurl  ?>showSentOrders/0/ajax");'>Отправленные (<?=  empty
			($sent_orders) ? 0 : $sent_orders ?>)</a>
		</div>
	</li>
</ul>