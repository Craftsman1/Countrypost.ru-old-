$(function() {
	$('span.ratings_plugin').each(function()
	{
		$(this).find('div').each(function(index, star)
		{
			$(this).click(function() {
				processStarClick(index, star);
			});

			$(this).hover(function() {
				processStarHover(index, star);
			}, function() {
				processStarUnhover(index, star);
			});
		});
	});
});

function processStarClick(index, star)
{
	var star_on = $(star).hasClass('on');

	$(star).parent().find('div').removeClass('on').removeClass('half');

	if (star_on)
	{
		$(star).parent().find('input').val('');
		return;
	}

	$(star).parent().find('input').val(index);

	for (var i = 0; i <= index; i++)
	{
		$(star).parent().find('.star' + i).addClass('on');
	}
}

function processStarHover(index, star)
{
	var star_on = $(star).parent().find('div').hasClass('on');

	if (star_on) return;

	$(star).parent().find('div').removeClass('half');

	for (var i = 0; i <= index; i++)
	{
		$(star).parent().find('.star' + i).addClass('half');
	}
}

function processStarUnhover(index, star)
{
	$(star).parent().find('div').removeClass('half');
}

function validate_float(evt)
{
	validate_generic(evt, /[0-9]|\./);
}

function validate_number(evt) 
{
	validate_generic(evt, /[0-9]/);
}

function validate_generic(evt, regex)
{
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode(key);
	// проверяем на соответствие и пропускаем только delete, backspace, влево и вправо
	if ( ! regex.test(key) && theEvent.keyCode != 8  && theEvent.keyCode != 46 && theEvent.keyCode != 37  && theEvent.keyCode != 39)
	{
		theEvent.returnValue = false;
		theEvent.preventDefault();
	}
}

function noty_generic(layout, message, ntype)
{
	var n = noty({
		text: message,
		type: ntype,
		dismissQueue: true,
		layout: layout,
		theme: 'defaultTheme',
		timeout: 2000
	});
	//console.log('html: '+n.options.id);
}

function success(layout, message)
{
	noty_generic(layout, message, 'success');
}

function error(layout, message)
{
	noty_generic(layout, message, 'alert');
}

function updatePerPage(dropdown, handler)
{
	var id = $(dropdown).find('option:selected').val();
	window.location.href = '/' + handler + '/updatePerPage/' + id;
}

function getNowDate()
{
	var date = new Date();
	var day = date.getDate();
	var month = date.getMonth();
	var hours = date.getHours();
	var minutes = date.getMinutes();
	
	if (month < 10)
	{
		month = '0' + month;
	}

	if (day < 10)
	{
		day = '0' + day;
	}

	if (hours < 10)
	{
		hours = '0' + hours;
	}

	if (minutes < 10)
	{
		minutes = '0' + minutes;
	}
	
	return (day + "." + month + "." + date.getFullYear() + ' ' + hours + ':' + minutes);
}

function goto_page(page_url)
{
	window.location = '#pagerScroll';

	$.ajax({
		url: page_url,
		success: function (response){
			$('.pages').remove();
			$('#pagerForm,#packagesForm,#ordersForm,#partnersForm,#clientsForm,#unassignedOrders,#payments').before(response).remove();
		}});
}

function goto_page_message(page_url, form_name, success_message, error_message)
{
	window.location = '#pagerScroll';

	$.ajax({
		url: page_url,
		success: function (response){
			$('.pages').remove();
			$('#' + form_name).before(response).remove();
			success('top', success_message);
		},
		error: function () {
			success('top', error_message);
		}
	});
}

function update_order_status(url, order_id)
{
	goto_page_message(url,
		'ordersForm',
		'Статус заказа №' + order_id + ' успешно изменен.',
		'Не удалось изменить статус заказа №' + order_id + '. Попробуйте еще раз.');
}

function order_status_handler(uri, page_status)
{
	$('select.order_status').change(function() {
		$(this)
			.parent()
			.find('img.status_progress')
			.show();

		var $url = uri +
			'updateOrderStatus/0/ajax/' +
			$(this).attr('name') +
			'/' +
			$(this).val();

		var order_id = $(this).attr('name').substring(12);

		update_order_status($url, order_id);
	});
}

function update_payment_status(url, o2i_id)
{
	goto_page_message(url,
		'paymentForm',
		'Статус заявки №' + o2i_id + ' успешно изменен.',
		'Не удалось изменить статус заявки №' + o2i_id + '. Попробуйте еще раз.');
}

function payment_status_handler(uri)
{
	$('select.o2i_status').change(function() {
		$(this)
			.parent()
			.find('img.status_progress')
			.show();

		var $url = uri +
			'updatePayment/0/ajax/' +
			$(this).attr('name') +
			'/' +
			$(this).val();

		update_payment_status($url, o2i_id);
	});
}

function setRel(id)
{
	$("a[rel*='lightbox_"+id+"']").lightBox();
	var aa = $("a[rel*='lightbox_"+id+"']");
	$(aa[0]).click();
}

function getSnippetByType(odetail, order_type)
{
	var snippet = '';

	if (order_type == 'online')
	{
		snippet = '<a target="_blank" href="' + odetail['link'] + '">' +
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
	}
	else if (order_type == 'offline')
	{
		snippet = '<br><b>' + odetail['shop'] +'</b>' +
			'<br><b>' + odetail['name'] +'</b>' +
			'<br><b>Количество</b>: ' +
			odetail['amount'] +
			' <b>Размер</b>: ' +
			odetail['size'] +
			' <b>Цвет</b>: ' +
			odetail['color'] +
			(odetail['foto_requested'] == 1 ? '<br><b>Фото полученного товара:</b> сделать фото' : '') +
			(odetail['search_requested'] == 1 ? '<br><b>Поиск товара:</b> требуется поиск' : '') +
			'<br><b>Комментарий</b>: ' +
			odetail['comment'];
	}
	else if (order_type == 'service')
	{
		snippet = '<b>' + odetail['name'] + '</b>' +
			'<br><b>Описание</b>: ' +
			odetail['comment'];
	}
	else if (order_type == 'delivery')
	{
		snippet = (odetail['link'] ? ('<a target="_blank" href="' + odetail['link'] + '">' +
			odetail['name'] +'</a>') :
			('<b>' + odetail['name'] + '</b>')) +
			'<br><b>Количество</b>: ' +
			odetail['amount'] +
			' <b>Объём</b>: ' +
			odetail['volume'] +
			' <b>ТН ВЭД</b>: ' +
			odetail['tnved'] +
			(odetail['insurance'] == 1 ? '<br><b>Страховка:</b> сделать страховку' : '') +
			'<br><b>Комментарий</b>: ' +
			odetail['comment'];
	}
	else if (order_type == 'mail_forwarding')
	{
		snippet = (odetail['link'] ? ('<a target="_blank" href="' + odetail['link'] + '">' +
			odetail['name'] +'</a>') :
			('<b>' + odetail['name'] + '</b>')) +
			'<br><b>Количество</b>: ' +
			odetail['amount'] +
			' <b>Размер</b>: ' +
			odetail['size'] +
			' <b>Цвет</b>: ' +
			odetail['color'] +
			(odetail['foto_requested'] == 1 ? '<br><b>Фото полученного товара:</b> сделать фото' : '') +
			'<br><b>Комментарий</b>: ' +
			odetail['comment'];
	}

	return snippet;
}

function getImageSnippet(odetail, id)
{
	var snippet = '';

	if (odetail['img'] != '')
	{
		var short_link = odetail['img'];

		if (short_link.length > 17)
		{
			short_link = short_link.substring(0, 17) + '...';
		}

		snippet =
			"<a target='_blank' href='" +
				odetail['img'] +
				"'>" +
				short_link +
				"</a>";
	}
	else
	{
		snippet =
			'<a href="javascript:void(0);" onclick="setRel(' +
				id +
				');"><img src="/main/showScreen/' +
				id +
				'" width="55px" height="55px"><a rel="lightbox_' +
				id +
				'" href="/main/showScreen/' +
				id +
				'" style="display:none;">Посмотреть</a></a>';
	}

	return snippet;
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
		odetail['tnved'] = $tr.find('textarea.tnved').val();
		odetail['volume'] = $tr.find('textarea.volume').val();
		odetail['comment'] = $tr.find('textarea.ocomment').val();
		odetail['img_selector'] = $tr.find('input.img_selector:checked').val();
		odetail['foto_requested'] = $tr.find('input.foto_requested:checked').length;
		odetail['search_requested'] = $tr.find('input.search_requested:checked').length;
		odetail['insurance'] = $tr.find('input.insurance:checked').length;

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

function submitItemByType(id, data, order_type)
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

		var snippet_first = getSnippetByType(odetail, order_type);
		$tr.find('span.plaintext:first').html(snippet_first);

		var snippet_last = getImageSnippet(odetail, id)
		$tr.find('span.plaintext:last').html(snippet_last);

		cancelItem(id);
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
		$tr.find('.delete').show();
		$tr.find('.cancel').hide();
		$tr.find('.save').hide();
	}
}

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
		$tr.find('.delete').hide();
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

		if (odetail['search_requested'] == 1)
		{
			$tr.find('input.search_requested').attr('checked', 'checked');
		}
		else
		{
			$tr.find('input.search_requested').removeAttr('checked');
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

function editBlog(id)
{
	var blog = eval('blog' + id);
 
		 
		$tr = $('tr#blog' + id);

		$tr.find('span.plaintext').hide();
		$tr.find('span.producteditor').show();
		$tr.find('.edit').hide();
		$tr.find('.delete').hide();
		$tr.find('.cancel').show();
		$tr.find('.save').show();
		$('#title').val(blog['title']).focus();
		$('#blog_id').val(blog['blog_id']);
		var oEditor = FCKeditorAPI.GetInstance('message');
		oEditor.SetHTML(blog['message']);

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

		if (odetail['search_requested'] == 1)
		{
			$tr.find('input.search_requested').attr('checked', 'checked');
		}
		else
		{
			$tr.find('input.search_requested').removeAttr('checked');
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

function init_profile()
{
	$('ul.tabs a').click(function(e) {
			e.preventDefault();
			$('ul.tabs li').removeClass('active');
			$('div.client_tab,div.dealer_tab').hide();

			$('div.' + $(e.target).attr('class')).show();
			$(this).parent().parent().addClass('active');
		});
}