<? View::show('elements/hints'); ?>
<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<? View::show($viewpath.'elements/div_float_preview_package'); ?>
	<? View::show($viewpath.'elements/div_float_upload_package'); ?>
	<h3>Оплаченные посылки</h3>
	<? View::show($viewpath.'elements/package_filter', array(
		'handler' => 'filterPayedPackages'
	)); ?>
	<? View::show($viewpath.'ajax/showPayedPackages', array(
		'packages' => $packages,
		'pager' => $pager)); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('#packagesForm input.package_weight').keypress(function(event){validate_number(event);});
	});
	
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}

	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить посылку №" + id + "?")){
			window.location.href = '<?= $selfurl ?>deletePackage/' + id;
		}
	}
	
	function updateStatus(id){
		var selectedStatus = $('#declaration_status option:selected');
		if (selectedStatus.val() != '-1'){
			if ($('#packagesForm input:checkbox:checked').size() == 0){
				alert('Выберите посылки со статусом декларации "Заполнить самостоятельно".');
				return;
			}
			
			if (confirm('Вы уверены, что хотите изменить статус деклараций выбранных посылок на "' 
				+ $(selectedStatus).text() + '"?'))
			{
				document.getElementById('packagesForm').submit();
			}
		}
	}
	
	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	
	function refundItem(id) {
		if (confirm("Возместить клиенту посылку №" + id + "?")){
			window.location.href = '<?= $selfurl ?>refundPackage/' + id;
		}
	}
</script>