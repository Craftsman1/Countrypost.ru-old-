<h3 class="bids_header" <?= $bids ? '' : 'style="display:none;"' ?> ><? if (empty($order->order_manager)) :	?>Предложения от посредников<? else : ?>Выбран исполнитель<? endif; ?></h3><? if ($bids) {	foreach ($bids as $bid)	{		View::show('main/elements/orders/bid', array('bid' => $bid));	}} ?><script>	// BOF: динамика	function expandBidDetails(bid_id)	{		$('div#bid' + bid_id)			.find('.biddetails div')			.css('display', 'block');		$('div#bid' + bid_id)			.find('.expander')			.css('display', 'block');		$('div#bid' + bid_id)			.find('.biddetails div.expand')			.hide();			}	function collapseBidDetails(bid_id)	{		$('div#bid' + bid_id)			.find('.biddetails div')			.hide();		$('div#bid' + bid_id)			.find('.expander')			.hide();		$('div#bid' + bid_id)			.find('.biddetails div.expand')			.css('display', 'block');		$('div#bid' + bid_id)			.find('.biddetails div.edit')			.css('display', 'block');	}	// EOF: динамика	// BOF: комментарии	function beforeSubmitComment(bid_id)	{		var $comments = $('div#comments' + bid_id);		$comments			.find('img.comment_progress')			.show('slow');		$comments			.find('.cancel_comment')			.hide('slow');	}		function successSubmitComment(response, bid_id)	{		var $comments = $('div#comments' + bid_id);		$comments			.find('img.comment_progress')			.hide('slow');				if (response)		{			success('top', 'Комментарий добавлен!');			$comments				.find('.delete_bid')				.show('slow');			$comments				.find('.expand_comments')				.hide('slow');			$comments				.find('tr.comment:last')				.before('<tr class="comment"><td>' + response + '</td></tr>');			$comments				.find('tr.comment')				.show('slow');			$comments				.find('.collapse_comments,.delete_bid')				.show('slow');			$comments				.find('.expand_comments')				.hide('slow');			eval('window.comment' + bid_id + 'expanded = true;');			$('.add-comment textarea').val('');		}		else		{			error('top', 'Комментарий не добавлен. Напишите что-нибудь и сохраните его еще раз.');			$comments				.find('.cancel_comment')				.show('slow');		}	}	function errorSubmitComment(response, bid_id)	{		var $comments = $('div#comments' + bid_id);		$comments			.find('img.comment_progress')			.hide('slow');		$comments			.find('.cancel_comment')			.show('slow');		error('top', 'Комментарий не добавлен. Напишите что-нибудь и сохраните его еще раз.');	}	// EOF: комментарии	// BOF: добавление предложения	function chooseBid(bid_id)	{		$('#chooseBidForm' + bid_id).submit();	}	function unchooseBid(bid_id)	{        if ( ! confirm("Вы уверены, что хотите отказаться от посредника?")) {            return true;        }        var progress = '#bid' + bid_id + ' img.comment_progress';		$(progress).show();		$.post("/client/unchooseBid/<?= $order->order_id ?>")			.success(function(response) {				$(progress).hide();				success('top', 'Вы успешно отказались от работы с прошлым посредником.');				$('.unchoose_bid').hide('slow');				$('.chooseBidForm,.choose_bid').show('slow');				$('h3.bids_header').html('Предложения от посредников');				$('div.bid,div.content>br.expander')					.show('slow');				if (response)				{					$('form#orderForm').replaceWith(response);				}			})			.error(function() {				$(progress).hide();				error('top', 'Попробуйте еще раз.');			});	}	function deleteBid(order_id, bid_id)	{		var progress = '#bid' + bid_id + ' img.comment_progress';		var success_message = 'Предложение успешно удалено!';		var error_message = 'Предложение не удалено.';		var uri = '/manager/removeBid/' + order_id + '/' + bid_id;        if ( ! confirm("Вы уверены, что хотите удалить предложение?")) 		{            return true;        }		$.ajax({			url: uri,			dataType: 'json',			type: 'POST',			beforeSend: function(data) {				$(progress).show();			},			success: function(data) {				if (data['is_error'])				{					error('top', data['message']);				}				else				{					success('top', success_message);					// прячем предложение					$('div#bid' + bid_id)						.remove()						.next()						.remove()						.next()						.remove();					// прячем заголовок					if ($('div.bid').length == 0)					{						$('h3.bids_header').hide('slow');					}					else					{						$('h3.bids_header').val('Предложения от посредников');					}					// прячем форму заказа					$('form#orderForm').hide('slow', function() {						$('form#orderForm').remove();					});					// прячем заголовок таблицы товаров					$('h3.managerOrderInfo').hide('slow');					// показываем кнопку добавить предложение					$('div#newBidButton, input.bid_button').show('slow');				}			},			error: function(data) {				error('top', error_message);			},			complete: function(data) {				$(progress).hide();			}		});	}	function beforeSubmitChoose(bid_id)	{		$('#chooseBidForm' + bid_id)			.parent()			.find('img.comment_progress')			.show('slow');		$('#chooseBidForm' + bid_id)			.find('.choose_bid')			.hide('slow');	}	function successSubmitChoose(response, bid_id)	{		$('#chooseBidForm' + bid_id)			.parent()			.find('img.comment_progress')			.hide('slow');		if (response)		{			success('top', 'Посредник выбран!');			$('h3.bids_header').html('Выбран исполнитель');			$('#chooseBidForm' + bid_id)				.find('.choose_bid')				.hide('slow');			$('form#orderForm')				.replaceWith(response);			$('div.bid,br.expander')				.not('#bid' + bid_id)				.hide('slow');								prepareOrderFormHandlers();			$('div#bid' + bid_id)				.find('.unchoose_bid')				.show('slow');		}		else		{			error('top', 'Исполнитель не выбран.');			$('#chooseBidForm' + bid_id)				.parent()				.find('img.comment_progress')				.hide('slow');			$('#chooseBidForm' + bid_id)				.find('.choose_bid')				.show('slow');		}	}		function errorSubmitChoose(response, bid_id)	{		error('top', 'Посредник не выбран. Попробуйте еще раз.');		$('#chooseBidForm' + bid_id)			.parent()			.find('img.comment_progress')			.hide('slow');		$('#chooseBidForm' + bid_id)			.find('.choose_bid')			.show('slow');	}	// EOF: добавление предложения</script>