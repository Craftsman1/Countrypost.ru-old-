<div title="<?= $title ?>"
	 class="payment_type">
	<label for="delayed_<?= $service_code ? $service_code : $service_code_usd  ?>" <? if ($selected) :
		?>class="payment_selected"<?
	endif; ?>>
		<img src="/static/images/<?= $image ?>" />
		<br style="clear: both;">
		<span>
			<? if ($service_code) : ?>
			<input type="radio"
				   rel="delayed"
				   id="delayed_<?= $service_code ?>"
				   name="payment_selector" <? if ($selected) : ?>checked<? endif; ?>>
			<div class="payment_system_name totals total_<?= $service_code ?>"></div>
			<br style="clear: both;">
			<? endif; ?>
			<? if ($service_code_usd) : ?>
			<input type="radio"
				   rel="delayed"
				   id="delayed_<?= $service_code_usd ?>"
				   name="payment_selector">
			<div class="payment_system_name totals total_<?= $service_code_usd ?>"></div>
		</span>
		<? endif; ?>
	</label>
</div>