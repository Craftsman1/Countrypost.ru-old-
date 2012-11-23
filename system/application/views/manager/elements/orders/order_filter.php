<form id="filterForm" action="<?= $selfurl ?>filterOrders/0/ajax/" class='admin-inside' method="POST">
	<div class='table' style="position:relative;background:#fff;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div class='filter-box'>
			<div>
				<span class="label">Поиск заказа:</span>
				<input style="width:180px;" class="textbox" type='text' id='order_number' maxlength="11" name="search_id" value="<?=$filter->search_id?>">
			</div>
			<br />
			<br />
			<div>
				<span class="label">По:</span>
				<select name="id_type" class='select first-input' style="width:190px;">
					<option value="">выбрать...</option>
					<option value="order" <? if ('order' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру заказа</option>
					<option value="client" <? if ('client' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру клиента</option>
				</select>
			</div>
			<br />
			<br />
			<div>
				<span class="label">Статус:</span>
				<select name='id_status' class='select' style="width:190px;">
					<option value=''>выбрать...</option>
					<? foreach ($filter->order_statuses as $status_id => $status_name) : ?>
					<option value='<?= $status_id ?>' <? if ( ! empty($filter->id_status) AND $status_id == $filter->id_status) : ?>selected='selected'<? endif; ?>><?= $status_name ?></option>
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
		$('#filterForm select').change(function() {
		//	document.getElementById('filterForm').submit();
		});

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