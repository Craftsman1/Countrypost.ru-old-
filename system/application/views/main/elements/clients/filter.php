<div id="filterFormContainer">
	<form class='admin-inside' action="<?= $selfurl ?>filterClients" id="filterForm" method="POST">
		<div class='table' style="position:relative;background:#fff;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>	
			<div class='filter-box'>
				<div>
					<span class="label">Страна:</span>
					<select id="country_from" name="country_from" class="textbox">
						<option value="0">выберите страну...</option>
						<? foreach ($countries as $country_id => $country) : ?>
						<option value="<?= $country_id ?>"  title="/static/images/flags/<?= $countries_en[$country_id] ?>.png" <? if (isset($filter->country_from) AND $filter->country_from == $country_id) : ?>selected<? endif; ?>><?= $countries[$country_id] ?></option>
						<? endforeach; ?>
					</select>
				</div>
				<br />
				<br />
				<div>
					<span class="label">Номер:</span>
					<input style="" class="textbox" type='text' id='client_id' name="client_id" maxlength="11" value="<?php if (isset($filter->client_id)) echo $filter->client_id; ?>"/>
				</div>
				<br />
				<br />
				<div>
					<span class="label">Логин:</span>
					<input style="" class="textbox" type='text' id='login' name="login" value="<?php if (isset($filter->login)) echo $filter->login; ?>"/>
				</div>
				<br style="clear:both;" />
				<br />
				<div>
					<span class="label"></span>
					<div style="">
						<input type='submit' id="filterSubmit" value='Найти' style="width:91px;height: 27px;font: 13px sans-serif;vertical-align: top;margin-bottom:4px;margin-right:10px;"/>
						<img class="float" id="filterProgress" style="display:none;margin:0px;margin-top:-5px;" src="/static/images/lightbox-ico-loading.gif"/>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$("#country_from").msDropDown({mainCSS:'idd'});
		$("#country_to").msDropDown({mainCSS:'idd'});
		$("#filterForm").show();
		
		$('#client_id').keypress(function(event){validate_number(event); if ($(this).val()=='0') {$(this).val('')}});
		
		$('#filterForm').ajaxForm({
			target: '<?= $selfurl ?>filterClients',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#filterProgress").show();
			},
			success: function(response)
			{
				//$('#filterForm').append($(response));
				$("#filterProgress").hide();
				
				$("div#partnersFormContainer").replaceWith(response);					
				//$("div#partnersFormContainer").html(response);
			},
			error: function(response)
			{
				$("#filterProgress").hide();
			}
		});
	});
</script>