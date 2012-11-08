<div class="delivery dealer_tab" style="display:none;">
	<form action="/manager/saveDelivery" id="deliveryForm" method="POST">
		<div class="table">
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
			<br>
			<div class="delivery_box">
				<span class="float-left" style=" margin-top: 6px; margin-right: 10px; ">Подробное описание и тарифы на
					доставку:</span>
			</div>
			<br>
			<textarea maxlength="65535" id='delivery_description' name="delivery_description"></textarea>
		</div>
		<br>
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Сохранить">
			</div>
		</div>
		<img class="float" id="deliveryProgress" style="display:none;margin:0px;margin-top: -2px;margin-left: 8px;"
			 src="/static/images/lightbox-ico-loading.gif"/>
	</form>
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
			var oEditor = FCKeditorAPI.GetInstance('delivery_description');

			if (deliveries[country_id] != undefined)
			{
				oEditor.SetData(deliveries[country_id]);
			}
			else
			{
				oEditor.SetData('');
			}
		}
	}

	$(function() {
		$('#deliveryForm').ajaxForm({
			target: '/manager/saveDelivery',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#deliveryProgress").show();

				var country_id = $('#delivery_country option:selected').val();

				var oEditor = FCKeditorAPI.GetInstance('delivery_description');
				deliveries[country_id] = oEditor.GetHTML();
			},
			success: function(response)
			{
				$("#deliveryProgress").hide();
				success('top', 'Описание доставки успешно сохранено!');
			},
			error: function(response)
			{
				$("#deliveryProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('delivery_description', 200, 920, 'PackageComment') ?>
</script>