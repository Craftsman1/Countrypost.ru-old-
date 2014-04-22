<script type="text/javascript">
$(function() {
	$(".int").keypress(function(event){validate_number(event);});

	$('#select_all').change(function() {
		$('table.products td input[type=checkbox]').attr('checked', ($(this).attr('checked') == 'checked'));
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

function update_odetail_tracking(order_id, odetail_id)
{
	var tracking = $('input#odetail_tracking' + odetail_id).val();
	var uri = '<?= $selfurl ?>update_odetail_tracking/' +
			order_id + '/' +
			odetail_id + '/' +
			tracking;
	var success_message = 'Tracking номер товара №' + odetail_id + ' сохранен.';
	var error_message = 'Tracking номер товара №' + odetail_id + ' не сохранен.';
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

function update_joint_pricedelivery(order_id, joint_id)
{
	var cost = $('input#joint_pricedelivery' + joint_id).val();
	var uri = '<?= $selfurl ?>update_joint_pricedelivery/' +
			order_id + '/' +
			joint_id + '/' +
			cost;
	var success_message = 'Местная доставка товаров сохранена.';
	var error_message = 'Местная доставка товаров не сохранена.';
	var progress = 'img.progressJoint' + joint_id;

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

	if ($.isArray(order['bids']))
	{
		refreshBidTotals(order);
	}

	if (order['order_details'])
	{
		$('form#orderForm').replaceWith(order['order_details']);
	}
}

function refreshBidTotals(order)
{
	for (id in order['bids'])
	{
		var bid = order['bids'][id];

		$('div#bid' + bid['id'])
			.find('span.order_total_cost')
			.html(bid['total'])
			.end()
			.find('span.order_products_cost')
			.html(order['products_cost'] + order['delivery_cost'])
			.end()
			.find('span.manager_tax')
			.html(bid['tax'])
			.end()
			.find('span.manager_foto_tax')
			.html(bid['foto'])
			.end()
			.find('span.order_delivery_cost')
			.html(bid['delivery'])
			.end()
			.find('span.order_delivery_cost')
			.html(bid['delivery'])
			.end()
			.find('span.extra_tax')
			.html(bid['extra']);
	}
}
// EOF: сохранение статуса, веса, стоимости и местной доставки

// BOF: редактирование деталей заказа
function submitItem(id, data)
{
	submitItemByType(id, data, '<?= $order->order_type ?>');
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
	var selectedProds = $('table.products input[type="checkbox"]:checked');

	if (selectedProds.length < 2)
	{
		alert("Выберите хотя бы 2 товара для объединения местной доставки.");
		return false;
	}

	if (confirm("Объединить местную доставку для выбранных товаров?"))
	{
		$('img#joinProgress').show();
		var queryString = '';

		selectedProds.each(function(index, item) {
			queryString += (queryString.length ? '&' : '') +
					$(item).attr('name') +
					'=on';
		});

		$.post('<?= $selfurl ?>joinProducts/<?= $order->order_id ?>',
				queryString,
				function()
				{
					self.location.reload();
				}
		);
	}
}

function removeJoint(id)
{
	if (confirm("Отменить объединение общей доставки для выбранных товаров?"))
	{
		$('img#joinProgress').show();
		window.location.href = '<?= $selfurl ?>removeJoint/<?= $order->order_id ?>/' + id;
	}
}
</script>