<div class='adittional-block' style="    position: absolute;    right: 20px;    top: 40px;    z-index: 1;    width: 262px;">
	<div class='headlines' style="    margin-right: 20px;    width: 300px;">
		<h2 style="margin-left: 52px;">Курсы валют</h2>
		<br>
		<dl style="width: 222px;">
			<dt>Доллар ($)</dt>
			<dd>
				<?= number_format($rate_usd_rur, 2) ?> руб.
			</dd>
			<dt>Евро (&euro;)</dt>
			<dd>
				<?= number_format($rate_eur_rur, 2) ?> руб.
			</dd>
			<dt>Доллар ($)</dt>
			<dd>
				<?= number_format($rate_usd_kzt, 2)?>&nbsp;тенге&nbsp;(<em class="tenge">&nbsp;&nbsp;&nbsp;</em>)
			</dd>
			<dt>Доллар ($)</dt>
			<dd>
				<?= number_format($rate_usd_uah, 2)?>&nbsp;гривен&nbsp;(<em class="grivna">&nbsp;&nbsp;&nbsp;</em>)
			</dd>
			<dt>1 <?= $order->order_currency ?></dt>
			<dd>
				<?= number_format($rate_rur, 2) ?> руб.
			</dd>
			<dt>1 <?= $order->order_currency ?></dt>
			<dd>
				<?= number_format($rate_usd, 2) ?> $
			</dd>
			<dt>1 <?= $order->order_currency ?></dt>
			<dd>
				<?= number_format($rate_kzt, 2) ?>&nbsp;тенге&nbsp;(<em class="tenge">&nbsp;&nbsp;&nbsp;</em>)
			</dd>
			<dt>1 <?= $order->order_currency ?></dt>
			<dd>
				<?= number_format($rate_uah, 2)?>&nbsp;гривен&nbsp;(<em class="grivna">&nbsp;&nbsp;&nbsp;</em>)
			</dd>
		</dl>
	</div>
</div>