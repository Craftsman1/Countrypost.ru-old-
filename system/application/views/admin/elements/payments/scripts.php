<script type="text/javascript">
$(function() {
	$(".int").keypress(function(event){validate_number(event);});
});

// BOF: сохранение статуса, веса, стоимости и местной доставки
function update_payment_amount(order_id, payment_id)
{
	var amount = $('input#payment_amount' + payment_id).val();
	var uri = '<?= $selfurl ?>update_payment_amount/' +
			order_id + '/' +
			payment_id + '/' +
			amount;

	var success_message = 'Сумма оплаты в заявке №' + payment_id + ' сохранена.';
	var error_message = 'Сумма оплаты в заявке №' + payment_id + ' не сохранена.';
	var progress = 'img#payment_progress' + payment_id;

	updateCustomPayment(uri, success_message, error_message, progress);
}

function update_payment_amount_local(order_id, payment_id)
{
	var amount = $('input#payment_amount_local' + payment_id).val();
	var uri = '<?= $selfurl ?>update_payment_amount_local/' +
			order_id + '/' +
			payment_id + '/' +
			amount;

	var success_message = 'Сумма перевода в заявке №' + payment_id + ' сохранена.';
	var error_message = 'Сумма перевода в заявке №' + payment_id + ' не сохранена.';
	var progress = 'img#payment_progress' + payment_id;

	updateCustomPayment(uri, success_message, error_message, progress);
}

function update_payment_status(order_id, payment_id)
{
	var status = $('select#payment_status' + payment_id).val();
	var uri = '<?= $selfurl ?>update_payment_status/' +
			order_id + '/' +
			payment_id + '/' +
			status;

	var success_message = 'Статус заявки №' + payment_id + ' сохранен.';
	var error_message = 'Статус заявки №' + payment_id + ' не сохранен.';
	var progress = 'img#payment_progress' + payment_id;

	updateCustomPayment(uri, success_message, error_message, progress);
}

function update_all_payment_status(payment_id)
{
	var status = $('select#payment_status' + payment_id).val();
	var uri = '<?= $selfurl ?>update_all_payment_status/' +
			payment_id + '/' +
			status;

	var success_message = 'Статус заявки №' + payment_id + ' сохранен.';
	var error_message = 'Статус заявки №' + payment_id + ' не сохранен.';
	var progress = 'img#payment_progress' + payment_id;

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
			refreshOrderTotals(data, success_message, error_message);
		},
		error: function(data) {
			error('top', error_message);
		},
		complete: function(data) {
			$(progress).hide();
		}
	});
}
</script>