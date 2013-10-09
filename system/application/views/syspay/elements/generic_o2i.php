<div title="<?= $title ?>" class="payment_type payment_system">
    <div class="payment-container <? if ($selected) : ?>payment_selected<? endif; ?>">
		<img src="/static/images/<?= $image ?>" />
		<br style="clear: both;">
		<span>
			<? if ($service_code) : ?>
			<input type="radio"
                   value=""
				   rel="delayed"
				   id="delayed_<?= $service_code ?>"
				   name="payment_selector" <? if ($selected) : ?>checked<? endif; ?>>
			<label for="delayed_<?= $service_code ?>" class="payment_system_name totals total_<?= $service_code ?>"></label>
			<br style="clear: both;">
			<? endif; ?>
			<? if ($service_code_usd) : ?>
			<input type="radio"
                   value=""
				   rel="delayed"
				   id="delayed_<?= $service_code_usd ?>"
				   name="payment_selector">
			<label for="delayed_<?= $service_code_usd ?>" class="payment_system_name totals total_<?= $service_code_usd ?>"></label>
		</span>
		    <? endif; ?>
    </div>
</div>