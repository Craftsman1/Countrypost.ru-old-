<div class='content'>
	<?View::show($viewpath.'elements/div_submenu');?>
	<h3>Клиенты (всего: <?=$clients_count?>)</h3>
	<? View::show($viewpath.'elements/client_filter', array(
		'handler' => 'filterClients'
	)); ?>
	<? View::show($viewpath.'ajax/showClients', array(
		'clients' => $clients,
		'pager' => $pager)); ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filterForm select').change(function() {
			document.getElementById('filterForm').submit();	
		});
	});
	
	function showFilteredItems(item_type, item_status, client_id)
	{
		var queryString = 'manager_user=&period=&id_status=&id_type=client&search_id=' + client_id;
		$.post('<?= $selfurl ?>filter' + item_status + item_type + 's/', queryString, function() {});
	}
</script>