<div class='adittional-block' style="    position: absolute;    right: 20px;    top: 40px;    z-index: 1;    width: 262px;font-size:20px;">
	<div class='headlines' style="    margin-right: 20px;    width: 300px;">
		<h2 style="margin-left: 52px;">Курсы валют</h2>
		<br>
		<dl style="width: 222px;">
			<dt>1 <?= $order->order_currency ?> =</dt>
			<dd>
				<?= number_format($rate_rur, 6) ?> RUB
			</dd>
			<dt>1 <?= $order->order_currency ?> =</dt>
			<dd>
				<?= number_format($rate_usd, 6) ?> USD
			</dd>
			<dt>1 <?= $order->order_currency ?> =</dt>
			<dd>
				<?= number_format($rate_kzt, 6) ?> KZT
			</dd>
			<dt>1 <?= $order->order_currency ?> =</dt>
			<dd>
				<?= number_format($rate_uah, 6)?> UAH
			</dd>
		</dl>
	</div>
</div>