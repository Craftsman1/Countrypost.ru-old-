//Для конвертации JQuery('form_id') обьекта формы в js обьект
function to_obj(array)
{
    var o = {};
    var a = array.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

$(function() {

    temphash = '';

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

	$('.pricelist_main .textbox').blur(function(){
		if(($(this).val()+'').length==0)
		{
			$(this).val('0');
		}			
	})

    $(".delCommentRating").live('click',function(){
        var id_message = $(this).parent().attr('id');
        var re = /comm_/; id_message = id_message.replace(re,'');

        if (confirm("Вы хотите удалить Ваш комментарий?"))
        {
            $.ajax({
                url: 'profile/delCommentRating/'+id_message,
                type: 'POST',
                beforeSend: function(data) {
                },
                success: function(data) {
                    $("#comm_"+id_message).parent().parent().remove();
                    checkButtons();
                },
                error: function(data) {
                    error('top', 'Не могу удалить');
                },
                complete: function(data) {
                }
            });
        }else{
            return;
        }

    });
	$('#editBidForm0 input.manager_tax').change(function() {
			manager_tax = parseGenericTax(this);
			refreshEditTotals();
		});

		$('#editBidForm0 input.manager_tax_percentage').change(function() {
			manager_tax_percentage = parseGenericTax(this);
			manager_tax = Math.ceil(manager_tax_percentage * order_products_cost * 0.01);
			refreshEditTotals();
		});

		$('#editBidForm0 input.manager_foto_tax').change(function() {
			
			manager_foto_tax = parseGenericTax(this);
			refreshEditTotals();
		});
    jQuery('.comment-area').keypress(function(e){
        var _this = jQuery(e.target);
        switch(e.which)
        {
            case 13:

                break;
        }
    });
});
	
function editRating(rating_id)
{
    $.ajax({
        url: 'profile/editRating/'+rating_id,
        type: 'POST',
        beforeSend: function(data) {
        },
        success: function(data) {
            //window.location.replace("#tab2");
            //window.location.reload();
        },
        error: function(data) {
            error('top', 'Не могу вызвать на редактирование.');
        },
        complete: function(data) {
        }
    });
}

function delRating(rating_id,manager_id)
{
    if (confirm("Вы хотите удалить Ваш отзыв?"))
    {

        $.ajax({
            url: 'profile/delRating/'+rating_id+'/'+manager_id,
            type: 'POST',
            beforeSend: function(data) {
            },
            success: function(data) {
                $("#manager_rating"+rating_id).animate( { opacity: 'hide' },'slow',function(){ $(this).remove(); });
            },
            error: function(data) {
                error('top', 'Не могу удалить');
            },
            complete: function(data) {
            }
        });
    }else{
        return;
    }
}

$(document).ready(function () {
    $(window).on('popstate', function (e) {

        if (!location.hash){
            //eval($("#new").attr('href'));
        }
        else{
            //eval($(location.hash).attr('href'));
        }
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
		timeout: 3000
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
	//window.location = '#pagerScroll';

	$.ajax({
		url: page_url,
		success: function (response){
			$('.pages').remove();
			$('#pagerForm,#packagesForm,#ordersForm,#partnersForm,#clientsForm,#unassignedOrders,#payments').before(response).remove();
            window.location.hash = '#'+$('.active a', '#ordersForm .tabs').attr('name');
            temphash = window.location.hash;
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
		snippet = '<b>Описание</b>: ' +
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
		odetail['shop'] = $tr.find('textarea.shop').val();
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
		$tr.find('textarea.shop').val(odetail['shop']);
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

function showProgress()
{
	$('img.progress').show();
}

function validateAndShowProgress_online()
{
	$('img.progress').show();
	//валидации формы добавления заказа
	var flag=0;
	if($('#country_from').length>0 && $('#country_from').val()==0)
	{
		flag--;
		$('#country_from_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_from_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#country_to').length>0 && $('#country_to').val()==0)
	{
		flag--;
		$('#country_to_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_to_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#city_to').length>0 && $('#city_to').val()=='')
	{
		flag--;
		$('#city_to')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#city_to')
			.css('border','1px solid #AFAFAF');
	}
	if($('#olink').length>0 && $('#olink').val()=='')
	{
		flag--;
		$('#olink')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#olink')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oname').length>0 && $('#oname').val()=='')
	{
		flag--;
		$('#oname')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oname')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oprice').length>0 && ($('#oprice').val()=='' || isNaN($('#oprice').val())))
	{
		flag--;
		$('#oprice')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oprice')
			.css('border','1px solid #AFAFAF');
	}

	if(flag<0)
	{
		error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
		hideProgress();
		return false;
	}
	else
	{
		hideProgress();
		return true;
	}
}

function validateAndShowProgress_offline()
{
	$('img.progress').show();
	//валидации формы добавления заказа
	var flag=0;
	if($('#country_from').length>0 && $('#country_from').val()==0)
	{
		flag--;
		$('#country_from_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_from_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#country_to').length>0 && $('#country_to').val()==0)
	{
		flag--;
		$('#country_to_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_to_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#city_to').length>0 && $('#city_to').val()=='')
	{
		flag--;
		$('#city_to')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#city_to')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oname').length>0 && $('#oname').val()=='')
	{
		flag--;
		$('#oname')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oname')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oprice').length>0 && ($('#oprice').val()=='' || isNaN($('#oprice').val())))
	{
		flag--;
		$('#oprice')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oprice')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oshop').length>0 && $('#oshop').val()==0)
	{
		flag--;
		$('#oshop')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oshop')
			.css('border','1px solid #AFAFAF');
	}
	if($('#ocolor').length>0 && $('#ocolor').val()==0)
	{
		flag--;
		$('#ocolor')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#ocolor')
			.css('border','1px solid #AFAFAF');
	}
	if($('#osize').length>0 && $('#osize').val()==0)
	{
		flag--;
		$('#osize')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#osize')
			.css('border','1px solid #AFAFAF');
	}

	if(flag<0)
	{
		error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
		hideProgress();
		return false;
	}
	else
	{
		hideProgress();
		return true;
	}
}

function validateAndShowProgress_service()
{
	$('img.progress').show();
	//валидации формы добавления заказа
	var flag=0;
	if($('#country_from').length>0 && $('#country_from').val()==0)
	{
		flag--;
		$('#country_from_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_from_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#ocomment').length>0 && $('#ocomment').val()==0)
	{
		flag--;
		$('#ocomment')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#ocomment')
			.css('border','1px solid #AFAFAF');
	}

	if(flag<0)
	{
		error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
		hideProgress();
		return false;
	}
	else
	{
		hideProgress();
		return true;
	}
}

function validateAndShowProgress_delivery()
{
	$('img.progress').show();
	//валидации формы добавления заказа
	var flag=0;
	if($('#country_from').length>0 && $('#country_from').val()==0)
	{
		flag--;
		$('#country_from_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_from_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#country_to').length>0 && $('#country_to').val()==0)
	{
		flag--;
		$('#country_to_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_to_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#city_to').length>0 && $('#city_to').val()=='')
	{
		flag--;
		$('#city_to')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#city_to')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oweight').length>0 && $('#oweight').val()=='')
	{
		flag--;
		$('#oweight')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oweight')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oname').length>0 && $('#oname').val()=='')
	{
		flag--;
		$('#oname')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oname')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oamount').length>0 && ($('#oamount').val()=='' || isNaN($('#oamount').val())))
	{
		flag--;
		$('#oamount')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oamount')
			.css('border','1px solid #AFAFAF');
	}
	
	if(flag<0)
	{
		error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
		hideProgress();
		return false;
	}
	else
	{
		hideProgress();
		return true;
	}
}

function validateAndShowProgress_mail_forwarding()
{
	$('img.progress').show();
	//валидации формы добавления заказа
	var flag=0;
	if($('#dealer_id').length>0 && $('#dealer_id').val()==0)
	{
		flag--;
		$('#dealer_id_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#dealer_id_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#country_to').length>0 && $('#country_to').val()==0)
	{
		flag--;
		$('#country_to_msdd')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#country_to_msdd')
			.css('border','1px solid #AFAFAF');
	}
	if($('#otracking').length>0 && $('#otracking').val()=='')
	{
		flag--;
		$('#otracking')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#otracking')
			.css('border','1px solid #AFAFAF');
	}
	if($('#oname').length>0 && $('#oname').val()=='')
	{
		flag--;
		$('#oname')
			.css('border','1px solid #DD0000');
	}
	else
	{
		$('#oname')
			.css('border','1px solid #AFAFAF');
	}

	if(flag<0)
	{
		error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
		hideProgress();
		return false;
	}
	else
	{
		hideProgress();
		return true;
	}
}

function hideProgress()
{
	$('img.progress').hide();
}

function errorAddProduct()
{
	hideProgress();
	error('top', 'Товар не добавлен. Заполните все обязательные поля и попробуйте еще раз.');
}

function successAddProduct(response)
{
	if (response)
	{
		if (response.error == 0)
		{
			refreshOrderTotals(response, 'Товар №' + response.odetail_id + ' успешно добавлен в заказ.', response.error);

			$('table.products tr:first').after(response.product);

			$('div.checkout,tr.totals').show('slow');
			$('tr.missing_products').hide();
			$('form.orderForm').resetForm();
		}
		else {
			error('top', response.error);
		}
	}
	else
	{
		errorAddProduct();
	}
}

function errorAddOrder()
{
	hideProgress();
	error('top', 'Заказ не сформирован. Заполните все обязательные поля и попробуйте еще раз.');
}

function updateCountryFrom()
{
	var country_id = $('select#country_from').val();
	$('input.country_from').val(country_id);

	for (var index in currencies)
	{
		var currency = currencies[index];

		if (country_id == currency['country_id'])
		{
			$('.currency')
				.html(currency['country_currency']);

			$('.order_currency')
				.val(currency['country_currency']);
			break;
		}
	}
}

function updateCountryTo()
{
	$('input.country_to').val($('select#country_to').val());
}

function updateDealer()
{
	$('input.dealer_id').val($('select#dealer_id').val());
}

function updateCityTo()
{
	$('input.city_to').val($('input#city_to').val());
}

function updateDelivery()
{
	$('input.preferred_delivery').val($('input#preferred_delivery').val());
}

function checkout()
{
	$('form#orderForm').submit();
}

// скриншот
function showScreenshotLink()
{
	$('.screenshot_switch').hide();
	$('.screenshot_link_box').show();

	if ($('.screenshot_link_box').val() == '')
	{
		$('.screenshot_link_box').val('ссылка на скриншот')
	}
}

function showScreenshotUploader()
{
	$('.screenshot_switch').hide();
	$('.screenshot_uploader_box').show();

	if ($('.screenshot_link_box').val() == 'ссылка на скриншот')
	{
		$('.screenshot_link_box').val('')
	}
}

function screenshotUnDefault(element)
{
	if ($(element).val() == 'ссылка на скриншот')
	{
		$(element)
			.val('')
			.removeClass('screenshot_default');
	}
}

function screenshotDefault(element)
{
	if ($(element).val() == '')
	{
		$(element)
			.val('ссылка на скриншот')
			.addClass('screenshot_default');
	}
}

function showScreenshotSwitch()
{
	$('.screenshot_uploader_box').hide();
	$('.screenshot_link_box').hide();
	$('.screenshot_switch').show();
}


function initBidForm()
{
	bidFormInitialized = true;
	refreshTotals();
}

function showNewBidForm()
{
	if (window.user_group == undefined)
	{
		window.location = '#';
		success('top', 'Пожалуйста, войдите или зарегистрируйтесь для добавления нового предложения.');
	}
	else if (window.user_group == 'client')
	{
		$('div#newBidButton').hide('slow');
		window.location = '#';
		error('top', 'Извините, добавление нового предложения доступно только посредникам.');
	}
	else
	{
		$('div#newBidButton').hide('slow');

		$('div#newBidForm').show(0, function() {
			$('div#bid0').each(function() {
				if ( ! bidFormInitialized)
				{
					initBidForm();
				}

				$('div#bid0').show('slow', function() {
					window.location = '#new_bid';
				});
			});
		});
	}
}

function cancelBid()
{
	$('div#bid0').hide('slow');
	$('div#newBidButton').show('slow');
}

function refreshTotals()
{
	recalculateBid(edit_bid_id);
}

function refreshEditTotals()
{
	recalculateBid(edit_bid_id);
}

function recalculateBid(bid_id)
{
	order_total_cost =
		order_products_cost +
		countrypost_tax +
		manager_tax +
		manager_foto_tax +
		extra_tax +
		parseFloat(order_delivery_cost);

	$bid = $('#bid' + bid_id);

	$bid
		.find('.manager_tax')
		.val(manager_tax)
		.html(manager_tax);
	$bid
		.find('.manager_tax_percentage')
		.val(manager_tax_percentage)
		.html(manager_tax_percentage);
	$bid
		.find('.countrypost_tax')
		.val(countrypost_tax)
		.html(countrypost_tax);
	$bid
		.find('.manager_foto_tax')
		.val(manager_foto_tax)
		.html(manager_foto_tax);
	$bid
		.find('.requested_foto_count')
		.val(requested_foto_count)
		.html(requested_foto_count);
	$bid
		.find('.manager_foto_tax')
		.val(manager_foto_tax)
		.html(manager_foto_tax);
	$bid
		.find('.extra_tax')
		.val(extra_tax)
		.html(extra_tax);
	$bid
		.find('.order_total_cost')
		.val(order_total_cost)
		.html(order_total_cost);
	$bid
		.find('.order_delivery_cost')
		.val(order_delivery_cost)
		.html(order_delivery_cost);
	$bid
		.find('.order_products_cost')
		.val(order_products_cost)
		.html(order_products_cost);
	$bid
		.find('.order_weight')
		.val(order_weight)
		.html(order_weight);
	$bid
		.find('.extra_tax_counter')
		.val(extra_tax_counter)
		.html(extra_tax_counter);
		if(!isNaN(exchangeRate)){
	$bid
		.find('.converted_order_totals span')
		.val(parseFloat(order_total_cost*exchangeRate).toFixed(2))
		.html(parseFloat(order_total_cost*exchangeRate).toFixed(2));
		}
}

function addBid()
{
	$('#editBidForm'+edit_bid_id).submit();
}

function editFotoTax()
{
	$('#editBidForm'+edit_bid_id+' .foto_tax_plaintext').hide('fast');
	$('#editBidForm'+edit_bid_id+' .foto_tax_editor').show('fast');
}

function editManagerTax()
{
	$('#editBidForm'+edit_bid_id+' .manager_tax_plaintext').hide('fast');
	$('#editBidForm'+edit_bid_id+' .manager_tax_editor').show('fast');
}

function addExtraTax()
{
	var $template = $('#editBidForm'+edit_bid_id+' .template').clone();
	
	$template
		.removeClass('template')
		.find('input.extra_tax_value')
		.attr('name', 'extra_tax_value' + extra_tax_counter)
		.keypress(function(event) {
			validate_float(event);
		})
		.change(function() {
			updateExtraTax();
		});

	$template
		.find('input.extra_tax_name')
		.attr('name', 'extra_tax_name' + extra_tax_counter);
	$('#editBidForm'+edit_bid_id+' div.extra_tax_box:last').after($template);

	$template.show('fast');

	extra_tax_counter++;
	refreshTotals();
}

function parseGenericTax(input)
{
	var newTax = $(input).val();

	if (newTax == '')
	{
		newTax = 0;
	}

	return (isNaN(newTax) ? 0 : parseInt(newTax));
}
function showTypeSelector()
{
	$('#editBidForm'+edit_bid_id+' .manager_tax_type')
		.val('products_delivery')
		.show();
	$('#editBidForm'+edit_bid_id+' .manager_tax_editor').hide();

	manager_tax = products_delivery_tax;
	recalculateBid(edit_bid_id);
}

function showTaxEditor()
{
	if ($('#editBidForm'+edit_bid_id+' select.manager_tax_type').val() == 'custom')
	{
		$('#editBidForm'+edit_bid_id+' .manager_tax_type').hide();
		$('#editBidForm'+edit_bid_id+' .manager_tax_editor').show();

		manager_tax = parseFloat($('#editBidForm'+edit_bid_id+' input.manager_tax').val());
	}
	else if ($('#editBidForm'+edit_bid_id+' select.manager_tax_type').val() == 'products_delivery')
	{
		manager_tax = products_delivery_tax;
		$('#editBidForm'+edit_bid_id+' input.manager_tax').val(manager_tax);
	}
	else if ($('#editBidForm'+edit_bid_id+' select.manager_tax_type').val() == 'products')
	{
		manager_tax = products_tax;
		$('#editBidForm'+edit_bid_id+' input.manager_tax').val(manager_tax);
	}

	recalculateBid(edit_bid_id);
}

// доп. комиссии
function updateExtraTax()
{
	extra_tax = 0;

	$('#editBidForm'+edit_bid_id+' input.extra_tax_value').each(function(index, item) {
		extra_tax += parseGenericTax(item);
	});

	refreshTotals();
}

function removeExtraTax(image)
{
	$(image)
		.parent()
		.parent()
		.remove();
	extra_tax_counter--;
	updateExtraTax();
	refreshTotals();
}

function editBid(bid_id)
{
	edit_bid_id = bid_id;
	initEditBidForm();
	$('#editBidForm'+bid_id).show('slow');


	var bid = $('div#bid' + bid_id);

	bid
		.find('a.edit_button')
		.attr('href', 'javascript:cancelEditBid(' + bid_id + ');')
		.html('отмена');

	bid
		.find('div.bid_buttons')
		.hide('slow');

	bid
		.find('div#comments' + bid_id)
		.hide('slow');
}

function cancelEditBid(bid_id)
{
	$('div#editBidForm'+bid_id).hide('slow');

	var bid = $('div#bid' + bid_id);

	bid
		.find('a.edit_button')
		.attr('href', 'javascript:editBid(' + bid_id + ');')
		.html('изменить');

	bid
		.find('div.bid_buttons')
		.show('slow');

	bid
		.find('div#comments' + bid_id)
		.show('slow');
}

function initEditBidForm()
{
	editBidFormInitialized = true;
	refreshEditTotals();
}

function showEditBidForm()
{
	$('div#editBidForm'+edit_bid_id).show();

	if ( ! editBidFormInitialized)
	{
		initEditBidForm();
	}

	window.location = '#edit_bid'+edit_bid_id;
}


// BOF: комментарии
function addComment(id)
{
	$('#comments' + id)
		.find('.add_comment,.expand_comments,.collapse_comments,.delete_bid')
		.hide('slow');
	$('#comments' + id)
		.find('.save_comment,.cancel_comment,.add-comment')
		.show('slow');
}

function cancelComment(id)
{
	$('#comments' + id).find('.add_comment,.delete_bid').show('slow');

	if (true == eval('window.comment' + id + 'expanded'))
	{
		$('#comments' + id).find('.collapse_comments').show('slow');
	}
	else
	{
		$('#comments' + id).find('.expand_comments').show('slow');
	}

	$('#comments' + id).find('.save_comment,.cancel_comment,.add-comment').hide('slow');
}

function checkButtons()
{
    var comm  = jQuery('.comment');
    var count = comm.length - 2;
    var btns  = jQuery('#bid_buttons');
    if(count > 2)
    {
        btns.show('slow');
    }
    else
    {
        btns.hide('slow');
        comm.show();
    }
    expandComments(btns.attr('data-id'));
}

function saveComment(id)
{
	$('#bidCommentForm' + id).submit();
	$('#ratingCommentForm' + id).submit();
}

function saveCommentRating(id, _this)
{
    _this    = _this || undefined;
    var form = $('#ratingCommentForm' + id);
    var tr   = form.closest('tr');
    var post = to_obj(form);
    post['count'] = jQuery('.comment').length - 2;
    post['comment'] = post.comment.replace(/\n/g,'<br/>');
    jQuery.post(form.attr('action'),post,
                function(data){
                    success('top', 'Комментарий добавлен!');
                    if(data.comment)
                    {
                        tr.before(data.comment);
                        if(undefined != _this)
                        {
                            jQuery(_this).closest('form').find('textarea').val('').html('');
                        }
                    }
                    checkButtons();
                },'json');
}

function expandComments(bid_id)
{
	var $comments = $('div#comments' + bid_id);

	$comments
		.find('tr.comment')
		.show('slow');
		
	$comments
		.find('.bid_buttons div.submit')

	$comments
		.find('div.expand_comments')
		.hide('slow');

	$comments
		.find('div.collapse_comments')
		.show('slow');

    // новые сообщения
    $comments
        .find('div#new_comments')
        .hide('slow');

    $('.angle').removeClass('new');
    $('.table.bid').removeClass('new');
    $('.table.admin-inside').removeClass('new');

    $.post("/"+window.user_group+"/clearNewComments/" + bid_id).success(function(response) {
    });

	eval('window.comment' + bid_id + 'expanded = true;');
}

function collapseComments(bid_id)
{

    var $comments = $('div#comments' + bid_id);

	$comments
		.find('tr.comment')
		.hide('slow');
		
	$comments
		.find('.bid_buttons div.submit')
		.css('margin-top','-10px');

	$comments
		.find('div.expand_comments')
		.show('slow');

	$comments
		.find('div.collapse_comments')
		.hide('slow');

	eval('window.comment' + bid_id + 'expanded = false;');
}

// EOF: комментарии
