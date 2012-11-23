<form id="filterForm" action="<?= $selfurl ?>filterOrders" method="POST">
	<div class='sorting'>
		<span class='first-title'>Поиск заказа:</span>
		<div class='text-field first-input'>
			<div>
				<input type='text' maxlength="11" name="search_id" value="<?=$filter->search_id?>">
			</div>
		</div>
		<span>по:</span>
		<select name="id_type" class='select first-input'>
			<option value="">выбрать...</option>
			<option value="order" <? if ('order' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру заказа</option>
			<option value="client" <? if ('client' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру клиента</option>
			<option value="tracking_no" <? if ('tracking_no' == $filter->id_type) : ?>selected="selected"<? endif;
				?>>Трекинг номеру</option>
		</select>
		<span>статус:</span>
		<select name='id_status' class='select'>
			<option value=''>выбрать...</option>
			<? foreach ($filter->order_statuses as $status_id => $status_name) : ?>
			<option value='<?= $status_id ?>' <? if ( ! empty($filter->id_status) AND $status_id == $filter->id_status) : ?>selected='selected'<? endif; ?>><?= $status_name ?></option>
			<? endforeach; ?>
		</select>
	</div>
</form>
<script type="text/javascript">
	$(function() {
		$('#filterForm select').change(function() {
			document.getElementById('filterForm').submit();	
		});
		
		$('#filterForm input:text').keypress(function(event){validate_number(event);});
	});
</script>