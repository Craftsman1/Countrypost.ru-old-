<div class="delivery table dealer_tab" style="display:none;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="delivery_box">
		<span class="label">Страна:</span>
		<select id="delivery_country" name="delivery_country" class="textbox" onchange="showPricelist();">
			<option value="0">выберите страну...</option>
			<? foreach ($countries as $country_id => $country) : ?>
			<option value="<?= $country_id ?>"  title="/static/images/flags/<?= $countries_en[$country_id] ?>.png" <? if (isset($filter->country_from) AND $filter->country_from == $country_id) : ?>selected<? endif; ?>><?= $countries[$country_id] ?></option>
			<? endforeach; ?>
		</select>
	</div>
</div>
<script type="text/javascript">
	<? if ($deliveries AND count($deliveries)) : ?>
	var deliveries = <?= json_encode($deliveries) ?>;
	<? else : ?>
	var deliveries = false;
	<? endif; ?>

	$(function() {
		$("#delivery_country").msDropDown({mainCSS:'idd'});
	});
	
	function showPricelist()
	{
		if (deliveries)
		{
			var country_id = $('#delivery_country option:selected').val();
			
			$('div.delivery_box p').remove();
			
			if (deliveries[country_id] != undefined)
			{
				alert(deliveries[country_id]);
				
				$('div.delivery_box').append(deliveries[country_id]);
			}
		}
	}
</script>
