<script type="text/javascript">
$(function() {
	$("input.int").keypress(function(event){validate_number(event);});

	$('#detailsForm').ajaxForm({
		target: '<?= $selfurl ?>updateProductAjax/',
		type: 'POST',
		dataType: 'html',
		iframe: true,
		success: function(response)
		{
			$('#detailsForm').append($(response));
		}
	});
});

// BOF: сохранение статуса, веса, стоимости и местной доставки
function update_odetail_weight(order_id, odetail_id)
{
	var weight = $('input#odetail_weight' + odetail_id).val();
	var uri = '<?= $selfurl ?>update_odetail_weight/' +
			order_id + '/' +
			odetail_id + '/' +
			weight;
	var success_message = 'Вес товара №' + odetail_id + ' сохранен.';
	var error_message = 'Вес товара №' + odetail_id + ' не сохранен.';
	var progress = 'img#progress' + odetail_id;

	updateCustomProduct(uri, success_message, error_message, progress);
}

function update_odetail_price(order_id, odetail_id)
{
	var price = $('input#odetail_price' + odetail_id).val();
	var uri = '<?= $selfurl ?>update_odetail_price/' +
			order_id + '/' +
			odetail_id + '/' +
			price;
	var success_message = 'Стоимость товара №' + odetail_id + ' сохранена.';
	var error_message = 'Стоимость товара №' + odetail_id + ' не сохранена.';
	var progress = 'img#progress' + odetail_id;

	updateCustomProduct(uri, success_message, error_message, progress);
}

function update_odetail_pricedelivery(order_id, odetail_id)
{
	var pricedelivery = $('input#odetail_pricedelivery' + odetail_id).val();
	var uri = '<?= $selfurl ?>update_odetail_pricedelivery/' +
			order_id + '/' +
			odetail_id + '/' +
			pricedelivery;
	var success_message = 'Местная доставка товара №' + odetail_id + ' сохранена.';
	var error_message = 'Местная доставка товара №' + odetail_id + ' не сохранена.';
	var progress = 'img#progress' + odetail_id;

	updateCustomProduct(uri, success_message, error_message, progress);
}

function update_odetail_status(order_id, odetail_id)
{
	var status = $('select#odetail_status' + odetail_id).val();
	var uri = '<?= $selfurl ?>update_odetail_status/' +
			order_id + '/' +
			odetail_id + '/' +
			status;
	var success_message = 'Статус товара №' + odetail_id + ' сохранен.';
	var error_message = 'Статус товара №' + odetail_id + ' не сохранен.';
	var progress = 'img#progress' + odetail_id;

	updateCustomProduct(uri, success_message, error_message, progress);
}

function updateCustomProduct(uri, success_message, error_message, progress)
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

function refreshOrderTotals(order, success_message, error_message)
{
	if (order['is_error'])
	{
		error('top', error_message);
	}
	else
	{
		success('top', success_message);
	}

	$('.total_product_cost').html(order['products_cost']);
	$('.total_delivery_cost').html(order['delivery_cost']);
	$('.total_weight').html(order['weight']);
	$('.order_status').val(order['status']);
}
// EOF: сохранение статуса, веса, стоимости и местной доставки

// BOF: редактирование деталей заказа
function editItem(id)
{
	var odetail = eval('odetail' + id);

	if (odetail['is_editing'] == 0)
	{
		odetail['is_editing'] = 1;
		$tr = $('tr#product' + id);

		$tr.find('span.plaintext').hide();
		$tr.find('span.producteditor').show();
		$tr.find('a.edit').hide();
		$tr.find('a.cancel').show();
		$tr.find('a.save').show();

		$tr.find('textarea.link').val(odetail['link']);
		$tr.find('textarea.name').val(odetail['name']);
		$tr.find('textarea.amount').val(odetail['amount']);
		$tr.find('textarea.size').val(odetail['size']);
		$tr.find('textarea.color').val(odetail['color']);
		$tr.find('textarea.ocomment').val(odetail['comment']);
	}
}

function cancelItem(id)
{
	var odetail = eval('odetail' + id);

	if (odetail['is_editing'] == 1)
	{
		odetail['is_editing'] = 0;
		$tr = $('tr#product' + id);

		$tr.find('span.plaintext').show();
		$tr.find('span.producteditor').hide();
		$tr.find('a.edit').show();
		$tr.find('a.cancel').hide();
		$tr.find('a.save').hide();
	}
}

function saveItem(id)
{
	var odetail = eval('odetail' + id);

	if (odetail['is_editing'] == 1)
	{
		$tr = $('tr#product' + id);

		odetail['link'] = $tr.find('textarea.link').val();
		odetail['name'] = $tr.find('textarea.name').val();
		odetail['amount'] = $tr.find('textarea.amount').val();
		odetail['size'] = $tr.find('textarea.size').val();
		odetail['color'] = $tr.find('textarea.color').val();
		odetail['comment'] = $tr.find('textarea.ocomment').val();

		var uri = '<?= $selfurl ?>updateProduct/<?= $order->order_id ?>/' + id;
		var progress = 'img#progress' + id;

		$.ajax({
			url: uri,
			dataType: 'json',
			type: 'POST',
			data: odetail,
			beforeSend: function(data) {
				$(progress).show();
			},
			success: function(data) {
				if (data['is_error'])
				{
					error('top', data['message']);
				}
				else
				{
					success('top', data['message']);

					var snippet =
						'<a target="_blank" href="' + odetail['link'] + '">' +
						odetail['name'] +'</a>' +
						(odetail['foto_requested'] == 1 ? ' (требуется фото товара)' : '') +
						'<br><b>Количество</b>: ' +
						odetail['amount'] +
						' <b>Размер</b>: ' +
						odetail['size'] +
						' <b>Цвет</b>: ' +
						odetail['color'] +
						'<br><b>Комментарий</b>: ' +
						odetail['comment'];

					$tr.find('span.plaintext:first').html(snippet);

					cancelItem(id);
				}
			},
			error: function(data) {
				error('top', 'Описание товара №' + id + ' не сохранено.');
			},
			complete: function(data) {
				$(progress).hide();
			}
		});
	}
}
// EOF: редактирование деталей заказа

function updateTotals()
{
	updateTotalGeneric('oprice', 'price_total', getSelectedCurrency());
	updateTotalGeneric('odeliveryprice', 'delivery_total', getSelectedCurrency());
	updateTotalGeneric('product_total', 'order_totals', getSelectedCurrency());
	updateTotalGeneric('oweight', 'weight_total', 'г');

	$('span.countryTo').html(countryTo);
	cityTo = $.trim($("input#city_to").val());

	if (cityTo)
	{
		$('span.cityTo').html(" (город: " + cityTo + ")");
	}
	else
	{
		$('span.cityTo').html("");
	}
}

function updateTotalGeneric(column, result, measure)
{
	var total = 0;

	$('.' + column).each(function() {
		total += parseFloat($(this).html());
	});

	$('.' + result).html(total + ' ' + measure);
}

function getSelectedCurrency()
{
	return selectedCurrency;
}

function joinProducts()
{
	var selectedProds = $('#detailsForm input[type="checkbox"]:checked');

	if (selectedProds.length < 2)
	{
		alert("Выберите хотя бы 2 товара для объединения.");
		return false;
	}

	if (confirm("Вы уверены, что хотите объединить выбранные товары?"))
	{
		var queryString = $('#detailsForm').formSerialize();
		$.post('<?=$selfurl?>joinProducts/<?=$order->order_id?>', queryString,
				function()
				{
					self.location.reload();
				}
		);
	}
}

function removeJoint(id)
{
	if (confirm("Отменить объединение общей доставки?"))
	{
		window.location.href = '<?=$selfurl?>removeOdetailJoint/<?=$order->order_id?>/'+id;
	}
}
</script>