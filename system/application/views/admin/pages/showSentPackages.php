<? View::show('elements/hints'); ?>
<div class='content'>
	<? View::show($viewpath.'elements/div_float_preview_package'); ?>
	<? View::show($viewpath.'elements/div_float_upload_package'); ?>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<h3>Отправленные посылки</h3>
	<? View::show($viewpath.'elements/package_filter', array(
		'handler' => 'filterSentPackages'
	)); ?>
	<? View::show($viewpath.'ajax/showSentPackages', array(
		'packages' => $packages,
		'pager' => $pager)); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('#packagesForm input:text').keypress(function(event){validate_number(event);});
	});

	function refundItem(id) {
		if (confirm("Возместить клиенту посылку №" + id + "?")){
			window.location.href = '<?= $selfurl ?>refundPackage/' + id;
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
</script>
