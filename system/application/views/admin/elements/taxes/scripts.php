<script type="text/javascript">
// BOF: сохранение статуса, веса, стоимости и местной доставки
function update_tax_status(payment_id)
{
	var status = $('select#tax_status' + payment_id).val();
	var uri = '<?= $selfurl ?>update_tax_status/' +
			payment_id + '/' +
			status;

	var success_message = 'Статус платежа №' + payment_id + ' сохранен.';
	var error_message = 'Статус платежа №' + payment_id + ' не сохранен.';
	var progress = 'img#tax_progress' + payment_id;

	updateCustomPayment(uri, success_message, error_message, progress);
}

function updateCustomPayment(uri, success_message, error_message, progress)
{
	$.ajax({
		url: uri,
		dataType: 'json',
		type: 'POST',
		beforeSend: function(data) {
			$(progress).show();
		},
		success: function(data) {
			success('top', success_message);

			$('b.total_usd').html(data['total_usd']);
		},
		error: function(data) {
			error('top', error_message);
		},
		complete: function(data) {
			$(progress).hide();
		}
	});
}
// EOF: сохранение статуса, веса, стоимости и местной доставки
</script>