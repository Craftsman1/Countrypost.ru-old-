<div class="delivery table dealer_tab" style="display:none;">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="delivery_box">
		<span class="float-left" style=" margin-top: 6px; margin-right: 10px; ">Страна:</span>
		<span>
			<select id="delivery_country" name="delivery_country" onchange="showPricelist();">
				<option value="0">выберите страну...</option>
				<? foreach ($countries as $country_id => $country) : ?>
				<option value="<?= $country_id ?>"  title="/static/images/flags/<?= $countries_en[$country_id] ?>.png" <? if (isset($filter->country_from) AND $filter->country_from == $country_id) : ?>selected<? endif; ?>><?= $countries[$country_id] ?></option>
				<? endforeach; ?>
			</select>
		</span>
	</div>
	<div class="delivery_description"></div>
</div>
<script type="text/javascript">
	var deliveries = [];

	<? if ($deliveries AND count($deliveries)) : foreach ($deliveries as $key => $value) : ?>
	deliveries['<?= $key ?>'] = '<?= html_entity_decode($value) ?>';
		<? endforeach; endif; ?>

	$(function() {
		$("#delivery_country").msDropDown({mainCSS:'idd'});
	});
	
	function showPricelist()
	{
		if (deliveries)
		{
			var country_id = $('#delivery_country option:selected').val();
			
			$('div.delivery_description').html('');
			
			if (deliveries[country_id] != undefined)
			{
				$('div.delivery_description').html('<br>' + deliveries[country_id]);
			}else{
                $.ajax({
                    type: "POST",
                    url: "profile/getPriceTemplateOfCountry",
                    data: "country="+country_id,
                    success: function(msg){
                        if (msg != ""){
                            $('div.delivery_description').html('<br>' + msg);
                        }
                    }
                });
            }
		}
	}
</script>
