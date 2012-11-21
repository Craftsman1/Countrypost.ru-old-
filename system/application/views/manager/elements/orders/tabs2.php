<ul class='tabs'>
	<li <? if (strval($selected_submenu) == 'new_orders' OR
			strval($selected_submenu) == 'payed_orders' OR
			strval($selected_submenu) == 'sent_orders') : ?>class='active'<? endif; ?>>
		<div>
			<a href='javascript:goto_page("<?= $selfurl  ?>showOpenOrders/0/ajax");'>Мои заказы</a>
		</div>
	</li>
	<li <? if (strval($selected_submenu) == 'bid_orders') : ?>class='active'<? endif; ?>>
	<div>
			<a href='javascript:goto_page("<?= $selfurl  ?>showBidOrders/0/ajax");'>Заказы с моими предложениями</a>
		</div>
	</li>
</ul>