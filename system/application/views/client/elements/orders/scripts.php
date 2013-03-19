<script type="text/javascript">
$(function() {
	$(".int").keypress(function(event){validate_number(event);});

	$('#select_all').change(function() {
		$('table.products td input[type=checkbox]').attr('checked', ($(this).attr('checked') == 'checked'));
	});
});

// BOF: сохранение статуса, веса, стоимости, местной доставки и трекинг номера
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
function editItem(id)
{
	var odetail = eval('odetail' + id);

	if (odetail['is_editing'] == 0)
	{
		odetail['is_editing'] = 1;
		$tr = $('tr#product' + id);

		$tr.find('span.plaintext').hide();
		$tr.find('span.producteditor').show();
		$tr.find('.edit').hide();
		$tr.find('.cancel').show();
		$tr.find('.save').show();

		$tr.find('textarea.link').val(odetail['link']);
		$tr.find('textarea.name').val(odetail['name']);
		$tr.find('textarea.amount').val(odetail['amount']);
		$tr.find('textarea.size').val(odetail['size']);
		$tr.find('textarea.color').val(odetail['color']);
		$tr.find('textarea.volume').val(odetail['volume']);
		$tr.find('textarea.tnved').val(odetail['tnved']);
		$tr.find('textarea.ocomment').val(odetail['comment']);
		$tr.find('textarea.image').val(odetail['img']);

		if (odetail['foto_requested'] == 1)
		{
			$tr.find('input.foto_requested').attr('checked', 'checked');
		}
		else
		{
			$tr.find('input.foto_requested').removeAttr('checked');
		}

		if (odetail['insurance'] == 1)
		{
			$tr.find('input.insurance').attr('checked', 'checked');
		}
		else
		{
			$tr.find('input.insurance').removeAttr('checked');
		}

		$tr.find('input.img_file').val(odetail['img_file']);
		$tr.find('input.img_selector[value="' + odetail['img_selector'] + '"]').attr('checked', 'checked');
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
		$tr.find('.edit').show();
		$tr.find('.cancel').hide();
		$tr.find('.save').hide();
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
		odetail['img_selector'] = $tr.find('input.img_selector:checked').val();
		odetail['foto_requested'] = $tr.find('input.foto_requested:checked').length;

		if (odetail['img_selector'] == 'link')
		{
			odetail['img'] = $tr.find('textarea.image').val();
			odetail['img_file'] = '';
		}
		else if (odetail['img_selector'] == 'file')
		{
			odetail['img'] = '';
			odetail['img_file'] = $tr.find('input.img_file').val();
		}

		$tr.find('form').submit();
	}
}

function submitItem(id, data)
{
	var $tr = $('tr#product' + id);
	var odetail = eval('odetail' + id);

	if (data['is_error'])
	{
		error('top', data['message']);
	}
	else
	{
		success('top', data['message']);

		var snippet_first =
				'<a target="_blank" href="' + odetail['link'] + '">' +
						odetail['name'] +'</a>' +
						'<br><b>Количество</b>: ' +
						odetail['amount'] +
						' <b>Размер</b>: ' +
						odetail['size'] +
						' <b>Цвет</b>: ' +
						odetail['color'] +
						(odetail['foto_requested'] == 1 ? '<br><b>Фото полученного товара:</b> сделать фото' : '') +
						'<br><b>Комментарий</b>: ' +
						odetail['comment'];

		$tr.find('span.plaintext:first').html(snippet_first);

		var snippet_last = '';

		if (odetail['img'] != '')
		{
			var short_link = odetail['img'];

			if (short_link.length > 17)
			{
				short_link = short_link.substring(0, 17) + '...';
			}

			snippet_last =
					"<a target='_blank' href='" +
							odetail['img'] +
							"'>" +
							short_link +
							"</a>";
		}
		else
		{
			snippet_last =
					'<a href="javascript:void(0);" onclick="setRel(' +
							id +
							');"><img src="/client/showScreen/' +
							id +
							'" width="55px" height="55px"><a rel="lightbox_' +
							id +
							'" href="/client/showScreen/' +
							id +
							'" style="display:none;">Посмотреть</a></a>';
		}

		$tr.find('span.plaintext:last').html(snippet_last);

		cancelItem(id);
	}
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
// EOF: редактирование деталей заказа

function deleteItem(id) {
	if (confirm("Вы уверены, что хотите удалить заказ №" + id + "?")){
		window.location.href = '<?= $selfurl ?>deleteOrder/' + id;
	}
}

function payItem(id) {
	if (confirm("Оплатить заказ №" + id + "?")){
		window.location.href = '<?= $selfurl ?>payOrder/' + id;
	}
}

function repayItem(id) {
	if (confirm("Доплатить за заказ №" + id + "?")){
		window.location.href = '<?= $selfurl ?>repayOrder/' + id;
	}
}
</script>