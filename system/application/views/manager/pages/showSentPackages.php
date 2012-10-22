<? View::show('elements/hints'); ?>
<div class='content'>
	<h3>Отправленные посылки</h3>
	<? View::show($viewpath.'elements/package_filter', array(
		'handler' => 'filterSentPackages',
	)); ?>
	<? View::show($viewpath.'ajax/showSentPackages', array(
		'packages' => $packages,
		'pager' => $pager)); ?>
</div>
<script type="text/javascript">
	$(function() {
		$('#pagerForm input:text').keypress(function(event){validate_number(event);});
	});
	
	function refundItem(id) {
		if (confirm("Возместить клиенту посылку №" + id + "?")){
			window.location.href = '<?=$selfurl?>refundPackage/' + id;
		}
	}
	
	function updateWeight(){
		if (confirm("Вы уверены, что хотите изменить вес посылок?")){
			$('#pagerForm').submit();
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
</script>