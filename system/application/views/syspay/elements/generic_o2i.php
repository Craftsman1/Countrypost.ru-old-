<div title="Оплата переводом с карты на карту через <?= $service_name ?>"
	 class="payment_type">
	<label for="delayed_<?= $service_code ?>" <? if ($selected) : ?>class="payment_selected"<? endif; ?>>
		<img src="/static/images/<?= $image ?>" />
		<? if ($service_code) : ?>
		<br style="clear: both;">
		<span>
			<input type="radio"
				   rel="delayed"
				   id="delayed_<?= $service_code ?>"
				   name="payment_selector" <? if ($selected) : ?>checked<? endif; ?>>
			<div class="payment_system_name totals total_<?= $service_code ?>"></div>
			<? endif; ?>
			<? if ($service_code_usd) : ?>
			<br style="clear: both;">
			<input type="radio"
				   rel="delayed"
				   id="delayed_<?= $service_code_usd ?>"
				   name="payment_selector">
			<div class="payment_system_name totals total_<?= $service_code_usd ?>"></div>
		</span>
		<? endif; ?>
	</label>
</div>