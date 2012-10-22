<? View::show('elements/hints'); ?>
<div class='content'>
	<? View::show($viewpath.'elements/div_float_upload_package'); ?>
	<h3>Оплаченные посылки</h3>
	<? View::show($viewpath.'elements/package_filter', array(
		'handler' => 'filterPayedPackages',
	)); ?>
	<? View::show($viewpath.'ajax/showPayedPackages', array(
		'packages' => $packages,
		'pager' => $pager)); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('#pagerForm input:text').keypress(function(event){validate_number(event);});
	});
	
	$('#packagesForm').submit(function() {
		if ($('#packagesForm input:checkbox:checked').size() == 0)
		{
			alert('Выберите посылки для отправки.');
			return false;
		}
		
		if (!confirm('Вы уверены, что хотите отправить выбранные посылки?'))
		{
			return false;
		}
	});
	
	function refundItem(id) {
		if (confirm("Возместить клиенту посылку №" + id + "?")){
			window.location.href = '<?=$selfurl?>refundPackage/' + id;
		}
	}
	
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
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
</script>