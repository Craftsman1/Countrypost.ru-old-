<form id="filterForm" action="<?=$selfurl?><?= $handler ?>" method="POST">
	<div class='sorting'>
		<span class='first-title'>Поиск заказа:</span>
		<div class='text-field first-input'><div><input type='text' maxlength="11" name="search_id" value="<?=$filter->search_id?>" value='Введите текст поиска' /></div></div>
		<span>по:</span>
		<select name="id_type" class='select first-input'>
			<option value="">выбрать...</option>
			<option value="order" <? if ('order' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру заказа</option>
			<option value="client" <? if ('client' == $filter->id_type) : ?>selected="selected"<? endif; ?>>Номеру клиента</option>
		</select>	
		<? if ( ! empty($show_status_filter)) : ?>
		<span>статус:</span>
		<select name='id_status' class='select'>
			<option value=''>выбрать...</option>
			<? foreach ($filter->order_statuses as $status_id => $status_name) : ?>
			<option value='<?= $status_id ?>' <? if ( ! empty($filter->id_status) AND $status_id == $filter->id_status) : ?>selected='selected'<? endif; ?>><?= $status_name ?></option>
			<? endforeach; ?>
		</select>
		<? endif; ?>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filterForm select').change(function() {
			document.getElementById('filterForm').submit();	
		});
		
		$('#filterForm input:text').keypress(function(event){validate_number(event);});
	});
</script>