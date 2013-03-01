<?
$payable_amount =
	($order->order_cost > ($order->order_cost_payed + $order->excess_amount)) ?
	($order->order_cost - $order->order_cost_payed - $order->excess_amount) :
	'';
?>
<div class="manager_payment_box">
	<form action="/client/payOrderDirect/<?= $order->order_id ?>" id="paymentForm" method="POST">
		<div class="table">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div class="admin-inside">
				<div>
					<span class="label" style="float:left">Сумма к оплате* :</span>
					<input style="width:180px;"
						   class="textbox"
						   maxlength="11"
						   id="amount"
						   name="amount"
						   value="<?= $payable_amount ?>"
						   type="text">
					<b class="currency">
						<?= $order->order_currency ?>
					</b>
					<? if ($order->excess_amount) : ?>
					<b class="excess_amount">
						+
						<?= $order->excess_amount ?>
						<?= $order->order_currency ?>
						<img class="tooltip"
							 src="/static/images/mini_help.gif"
							 title="Доступный остаток от предыдущих заказов">
						=
						<b class="total_local_amount">
							<?= $payable_amount + $order->excess_amount ?>
						</b>
						<?= $order->order_currency ?>
					</b>
					<? endif; ?>
				</div>
				<br style="clear:both;">
				<div>
					<span class="label" style="float:left">Способ оплаты* :</span>
					<input style="width:180px;"
						   class="textbox"
						   maxlength="255"
						   id="service"
						   name="service"
						   value=""
						   type="text">
				</div>
				<br style="clear:both;">
				<div>
					<span class="label" style="float:left">Комментарий :</span>
					<textarea
							style="width:180px;
							height: 60px;"
							class="textbox"
							maxlength="4096"
							id="comment"
							name="comment"></textarea>
				</div>
				<br style="clear:both;">
				<div class="submit">
					<div>
						<input type="submit" value="Оплатить">
					</div>
				</div>
				<br style="clear:both;">
			</div>
		</div>
	</form>
</div>
<br style="clear:both;">
<script>
function updateExcessAmountDirect()
{
	var excess_amount = <?= $order->excess_amount ?>;
	var amount = $('div.manager_payment_box input#amount').val();
	amount = parseFloat(amount);
	amount = (isNaN(amount) ? 0 : amount);

	$('div.manager_payment_box b.total_local_amount').html(excess_amount + amount);
}

$(function()
{
	$('div.manager_payment_box input#amount')
		.keypress(function(event) {
			validate_number(event);
		})
		.bind('keypress keydown mouseup keyup blur', function() {
			updateExcessAmountDirect();
		});


	var addPaymentItemProgress = function(obj)
	{
		var progress_snipet = '<img class="float" id="paymentProgress" style="margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>';
		$(obj).find('.edit_icon, .delete_icon').hide();
		$(obj).append(progress_snipet);
	},
	removePaymentItemProgress = function(obj)
	{
		$(obj).find('#paymentProgress').remove();
		$(obj).find('.edit_icon, .delete_icon').show();
	},
	validatePaymentForm = function()
	{
		field = null,
		errorCount = 0;

		field = $('div.manager_payment_box input#amount');

		if (isNaN(field.val()) || field.val() == '0')
		{
			$.fn.addProfileFieldError(field, 'Введите сумму платежа');
			errorCount++;
		}
		else
		{
			$.fn.removeProfileFieldError(field);
		}

		field = $('div.manager_payment_box input#service');

		if (field.val() == '')
		{
			$.fn.addProfileFieldError(field, 'Добавьте название платежной системы');
			errorCount++;
		}
		else
		{
			$.fn.removeProfileFieldError(field);
		}

		field = $('div.manager_payment_box input#comment');
		if (field.val().length > 4096)
		{
			$.fn.addProfileFieldError(field, 'Комментарий слишком длинный');
			errorCount++;
		}
		else
		{
			$.fn.removeProfileFieldError(field);
		}

		return (errorCount > 0) ? false : true;
	}

	// Валидация при заполнении
	$('#amount').validate({
		expression: "if (isNaN(VAL.val()) || VAL.val() == '0') return false; else return true;",
		message: "Введите сумму платежа"
	});

	$('#service').validate({
		expression: "if (VAL != '' || VAL.val().length <= 255) return true; else return false;",
		message: "Добавьте название платежной системы"
	});

	$('#comment').validate({
		expression: "if (VAL.val().length <= 4096) return true; else return false;",
		message: "Комментарий слишком длинный"
	});

	$('#paymentForm').ajaxForm({
		clearForm: false,
		type: 'POST',
		dataType: 'html',
		iframe: true,
		beforeSubmit: function(formData, jqForm, options)
		{
			return validatePaymentForm();
		},
		success: function(response)
		{
			var response_ = $.parseJSON(response);

			if (response_ === null)
			{
				error('top', 'Заполните все поля и сохраните еще раз.');
				return;
			}

			success('top', 'Заявка на оплату успешно сохранена!');
		},
		error: function(response)
		{
			error('top', 'Заполните все поля и сохраните еще раз.');
		}
	});
});

</script>