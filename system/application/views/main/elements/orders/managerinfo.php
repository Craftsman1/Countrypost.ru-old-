<<<<<<< HEAD
<? $is_own_bid = (isset($this->user->user_id) AND		($bid->manager_id == $this->user->user_id OR		$order->order_manager == $this->user->user_id OR 		$order->order_client == $this->user->user_id));?><div class='managerinfo'>	<span class="total">		<? if ( ! empty($this->user->user_id)) : ?>		Итого за заказ: <span class="order_total_cost"><?= $bid->total_cost	?></span> <?= $order->order_currency ?>		<? endif; ?>		<div class="biddetails">			<? if (isset($this->user->user_id) AND $bid->manager_id == $this->user->user_id) : ?>			<div class="edit">				<a class="edit_button"				   href="javascript:editBid('<?= isset($bid) ? $bid->bid_id : 0 ?>');">изменить</a>			</div>			<? endif; ?>			<? if ($is_own_bid) : ?>			<div class="expand">				<a href="javascript:expandBidDetails('<?= isset($bid) ? $bid->bid_id : 0 ?>');">подробнее</a>			</div>			<div>				<b>Расходы по заказу:</b>			</div>			<div>				<span class="order_products_cost">					<?= $order->order_products_cost + $order->order_delivery_cost ?>				</span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Общая стоимость товаров в заказе">			</div>			<div>				<span class="manager_tax"><?= $bid->manager_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Комиссия посредника">			</div>			<div class="foto_total_box">				<span class="manager_foto_tax"><?= $bid->foto_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Фото товаров">			</div>			<? if ($order->order_country_to) : ?>			<div class="delivery_total_box">				<span class="order_delivery_cost"><?= $bid->delivery_cost ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Стоимость международной доставки<?= empty($bid->delivery_cost) ? '' : ", $bid->delivery_name" ?>">			</div>			<? endif; ?>			<? $extras = array();			if ( ! empty($bid->extra_taxes))			{				foreach ($bid->extra_taxes as $extra)				{					$extras[] = $extra->extra_name;				}			}			$extra_hint = empty($extras) ? 'Дополнительные расходы' : implode(', ', $extras);			?>			<div>				<span class="extra_tax"><?= $bid->extra_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip extra_tax_hint"					 src="/static/images/mini_help.gif"					 title="<?= $extra_hint ?>">			</div>			<div>				<b>					Итого:					<span class="order_total_cost"><?= $bid->total_cost ?></span>					<?= $order->order_currency ?>				</b>			</div>			<div class="collapse">				<a href="javascript:collapseBidDetails('<?= $bid->bid_id ?>');">свернуть</a>			</div>			<? endif; ?>		</div>	</span>	<? if (isset($bid->statistics)) : ?>	<img src="/main/avatar_big/<?= $bid->statistics->manager_user; ?>" width="56px" height="56px">	<a href="/<?= $bid->statistics->login ?>"><?= $bid->statistics->fullname ?></a>	(<?= $bid->statistics->login ?>)	&nbsp;&nbsp;&nbsp;	<? View::show('/main/elements/ratings/reviews', array(		'positive' =>  $bid->statistics->positive_reviews,		'neutral' =>  $bid->statistics->neutral_reviews,		'negative' =>  $bid->statistics->negative_reviews,	)); ?>	<br>	<div>		<span class='label'			  style="margin-right: 10px;">			<?= isset($bid->created) ? date('d.m.Y H:i', strtotime($bid->created)) : date('d.m.Y H:i')?>		</span>		<span class='label'>			Выполненных заказов:			<?= $bid->statistics->completed_orders ?>		</span>	</div>	<br style="clear: both;height: 0;line-height: 0;">	<? endif; ?>	<!--div class="status">		<? if (isset($bid->statistics)) : ?>		<span class='label status'>			<center style="width: 58px; margin-right: 4px;">				100%<br>CASHBACK			</center>		</span>		<? endif; ?>		<span class='label'><?= isset($bid->created) ? date('d.m.Y H:i', strtotime($bid->created)) : date('d.m.Y H:i')?></span>	</div--></div>
=======
<? $is_own_bid = (isset($this->user->user_id) AND		($bid->manager_id == $this->user->user_id OR		$order->order_manager == $this->user->user_id OR 		$order->order_client == $this->user->user_id));?><div class='managerinfo'>	<span class="total">		<? if ( ! empty($this->user->user_id)) : ?>		Итого за заказ: <span class="order_total_cost"><?= $bid->total_cost	?></span> <?= $order->order_currency ?>		<? endif; ?>		<div class="biddetails">			<? if ($is_own_bid) : ?>			<div class="expand">				<a href="javascript:expandBidDetails('<?= isset($bid) ? $bid->bid_id : 0 ?>');">подробнее</a>			</div>			<div>				<b>Расходы по заказу:</b>			</div>			<div>				<span class="order_products_cost">					<?= $order->order_products_cost + $order->order_delivery_cost ?>				</span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Общая стоимость товаров в заказе">			</div>			<div>				<span class="countrypost_tax"><?= $bid->countrypost_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Комиссия Countrypost">			</div>			<div>				<span class="manager_tax"><?= $bid->manager_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Комиссия посредника">			</div>			<div class="foto_total_box">				<span class="manager_foto_tax"><?= $bid->foto_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Фото товаров">			</div>			<? if ($order->order_country_to) : ?>			<div class="delivery_total_box">				<span class="order_delivery_cost"><?= $bid->delivery_cost ?></span>				<?= $order->order_currency ?>				<img class="tooltip"					 src="/static/images/mini_help.gif"					 title="Стоимость международной доставки<?= empty($bid->delivery_cost) ? '' : ", $bid->delivery_name" ?>">			</div>			<? endif; ?>			<? $extras = array();			if ( ! empty($bid->extra_taxes))			{				foreach ($bid->extra_taxes as $extra)				{					$extras[] = $extra->extra_name;				}			}			$extra_hint = empty($extras) ? 'Дополнительные расходы' : implode(', ', $extras);			?>			<div>				<span class="extra_tax"><?= $bid->extra_tax ?></span>				<?= $order->order_currency ?>				<img class="tooltip extra_tax_hint"					 src="/static/images/mini_help.gif"					 title="<?= $extra_hint ?>">			</div>			<div>				<b>					Итого:					<span class="order_total_cost"><?= $bid->total_cost ?></span>					<?= $order->order_currency ?>				</b>			</div>			<div class="collapse">				<a href="javascript:collapseBidDetails('<?= $bid->bid_id ?>');">свернуть</a>			</div>			<? endif; ?>			<? if (isset($this->user->user_id) AND				$bid->manager_id == $this->user->user_id AND					empty($is_new_bid)) : ?>				<div class="edit">					<a class="edit_button"					   href="javascript:editBid('<?= isset($bid) ? $bid->bid_id : 0 ?>');">изменить</a>				</div>			<? endif; ?>		</div>	</span>	<? if (isset($bid->statistics)) : ?>	<img src="/main/avatar/<?= $bid->manager_id ?>" width="56px" height="56px">	<a href="/<?= $bid->statistics->login ?>"><?= $bid->statistics->fullname ?></a>	(<?= $bid->statistics->login ?>)	&nbsp;&nbsp;&nbsp;	<? View::show('/main/elements/ratings/reviews', array(		'positive' =>  $bid->statistics->positive_reviews,		'neutral' =>  $bid->statistics->neutral_reviews,		'negative' =>  $bid->statistics->negative_reviews,	)); ?>	<br>	<div>		<span class='label'			  style="margin-right: 10px;">			<?= isset($bid->created) ? date('d.m.Y H:i', strtotime($bid->created)) : date('d.m.Y H:i')?>		</span>		<span class='label'>			Выполненных заказов:			<?= $bid->statistics->completed_orders ?>		</span>	</div>	<br style="clear: both;height: 0;line-height: 0;">	<? endif; ?>	<!--div class="status">		<? if (isset($bid->statistics)) : ?>		<span class='label status'>			<center style="width: 58px; margin-right: 4px;">				100%<br>CASHBACK			</center>		</span>		<? endif; ?>		<span class='label'><?= isset($bid->created) ? date('d.m.Y H:i', strtotime($bid->created)) : date('d.m.Y H:i')?></span>	</div--></div>
>>>>>>> parent of 6c2ba62... Задачи: 16+37+35+33+30+31
