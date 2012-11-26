<form id="filterForm" action="<?= $selfurl ?>filterOrders/0/ajax/" class='admin-inside' method="POST">
	<div class='table' style="position:relative;background:#fff;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div class='filter-box'>
			<div>
				<span class="label">Поиск заказа:</span>
				<input style="width:180px;" class="textbox first-input" type='text' id='order_number' maxlength="11" name="search_id" value="<?=$filter->search_id?>">
				<span class="label">&nbsp;&nbsp;&nbsp;Статус:</span>
				<select id='id_status' name='id_status' class='select' style="width:190px;">
					<option value=''>все статусы</option>
					<? foreach ($filter->order_statuses as $status_id => $status_name) : ?>
					<option value='<?= $status_id ?>' <? if ( ! empty($filter->id_status) AND $status_id == $filter->id_status) : ?>selected='selected'<? endif; ?>><?= $status_name ?></option>
					<? endforeach; ?>
				</select>
			</div>
			<br />
			<br />
			<div>
				<span class="label">По:</span>
				<select id="id_type" name="id_type" class='select first-input' style="width: 190px;">
					<option value="">выбрать...</option>
					<option value="order" <? if ('order' == $filter->id_type) : ?>selected="selected"<?
					endif; ?>>Номеру заказа</option>
					<option value="client" <? if ('client' == $filter->id_type) : ?>selected="selected"<?
					endif; ?>>Номеру клиента</option>
				</select>
				<span class="label">&nbsp;&nbsp;&nbsp;Доставка в:</span>
				<select id="country_to" name="country_to" class='select' style="width:190px;">
					<option value="0">все страны</option>
					<? foreach ($countries as $country) : ?>
					<option value="<?= $country->country_id ?>"  title="/static/images/flags/<?= $country->country_name_en ?>.png" <? if (isset($filter->country_to) AND $filter->country_to == $country->country_id) : ?>selected<? endif; ?>><?= $country->country_name ?></option>
					<? endforeach; ?>
				</select>
			</div>
			<br style="clear:both;" />
			<br />
			<div>
				<span class="label"></span>
				<div style="">
					<input type='submit' id="filterSubmit" value='Найти' style="width:91px;height: 27px;font: 13px sans-serif;vertical-align: top;margin-bottom:4px;margin-right:10px;"/>
					<img class="float" id="ordersProgress" style="display:none;margin:0px;margin-top:-5px;" src="/static/images/lightbox-ico-loading.gif"/>
				</div>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function() {
		$("#id_status").msDropDown({mainCSS:'idd'});
		$("#id_type").msDropDown({mainCSS:'idd'});
		$("#country_to").msDropDown({mainCSS:'idd'});

		$('#filterForm input:text').keypress(function(event){validate_number(event);});

		$('#filterForm').ajaxForm({
			target: '<?= $selfurl ?>filterOrders/0/ajax/',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#ordersProgress").show();
			},
			success: function(response)
			{
				//$("div.pages").remove();
				$('#ordersForm').remove();
				$('#filterForm').after($(response));
				$("#ordersProgress").hide();
			},
			error: function(response)
			{
				$("#ordersProgress").hide();
			}
		});
	});
</script>