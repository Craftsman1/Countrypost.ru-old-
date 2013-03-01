<div id='payments'>
	<? View::show('main/elements/payments/tabs', array('selected_submenu' => 'open_payments')); ?>
	<? View::show($viewpath . 'elements/payments/showPayments'); ?>
</div>
<script type="text/javascript">
	$(function() {
		$("div#payments .int").keypress(function(event){validate_number(event);});
	});
</script>

