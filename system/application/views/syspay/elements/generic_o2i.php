<div title="Оплата переводом с карты на карту через <?= $service_name ?>"
	 class="payment_type">
	<label for="delayed_<?= $service_code ?>" <? if ($selected) : ?>class="payment_selected"<? endif; ?>>
		<input type="radio"
			   rel="delayed"
			   id="delayed_<?= $service_code ?>"
			   name="payment_selector" <? if ($selected) : ?>checked<? endif; ?>>
		<img src="/static/images/<?= $image ?>" />
		<div class="payment_system_name totals total_<?= $service_code ?>">
		</div>
	</label>
</div>