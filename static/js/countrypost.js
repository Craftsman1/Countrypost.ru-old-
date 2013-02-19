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
			'update' +
			page_status +
			'OrderStatus/0/ajax/' +
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
