<h3 class="bids_header" <?= $ratings ? '' : 'style="display:none;"' ?> >Все	отзывы</h3><span id="insert_rating"></span><? if ($ratings){	foreach ($ratings as $rating)	{		View::show('main/elements/dealers/manager_rating', array(				'rating' => $rating));	}} ?><script>	$(function() {		$('#bidCommentForm').ajaxForm({			target: $('#bidCommentForm').attr('action'),			type: 'POST',			dataType: 'html',			iframe: true,			beforeSubmit: function(formData, jqForm, options)			{								beforeSubmitComment();			},			success: function(response)			{				successSubmitComment(response);			},			error: function(response)			{				errorSubmitComment(response);							}		});	});		function beforeSubmitComment()	{		$('img.comment_progress').show('slow');		$('.cancel_comment').hide('slow');	}		function successSubmitComment(response)	{		$('img.comment_progress').hide('slow');				if (response)		{			success('top', 'Комментарий добавлен!');			$('.add_comment,.delete_bid').show('slow');						var bid_id = $(response).find('input').val();						$('.save_comment,.collapse_comments,.expand_comments,.cancel_comment,.add-comment').hide('slow');						$('#comments' + bid_id).find('tr.comment:last').after('<tr class="comment"><td>' + response + '</td></tr>');			$('#comments' + bid_id).find('tr.comment').show('slow');						$('#comments' + bid_id).find('.collapse_comments').show('slow');			$('#comments' + bid_id).find('.expand_comments').hide('slow');			eval('window.comment' + bid_id + 'expanded = true;');		}		else		{			error('top', 'Комментарий не добавлен. Напишите что-нибудь и сохраните его еще раз.');			$('.cancel_comment').show('slow');		}	}	function errorSubmitComment(response)	{		$('img.comment_progress').hide('slow');		$('.cancel_comment').show('slow');		error('top', 'Комментарий не добавлен. Напишите что-нибудь и сохраните его еще раз.');	}		function beforeSubmitChoose(bid_id)	{		$('#chooseBidForm' + bid_id)			.parent()			.find('img.comment_progress')			.show('slow');		$('#chooseBidForm' + bid_id)			.find('.choose_bid')			.hide('slow');	}		function successSubmitChoose(response, bid_id)	{		$('#chooseBidForm' + bid_id)			.parent()			.find('img.comment_progress')			.hide('slow');				if (response)		{			success('top', 'Посредник выбран!');			$('h3.bids_header').html('Выбран посредник');			$('#chooseBidForm' + bid_id)				.find('.choose_bid')				.hide('slow');			$('div.clientOrderInfo')				.after(response);			$('div.clientOrderInfo:first')				.remove();			$('h3.clientOrderInfo')				.show('slow');		}		else		{			error('top', response);			$('#chooseBidForm' + bid_id)				.parent()				.find('img.comment_progress')				.hide('slow');			$('#chooseBidForm' + bid_id)				.find('.choose_bid')				.show('slow');					}	}	function errorSubmitChoose(response, bid_id)	{		error('top', 'Посредник не выбран. Попробуйте еще раз.');		$('#chooseBidForm' + bid_id)			.parent()			.find('img.comment_progress')			.hide('slow');		$('#chooseBidForm' + bid_id)			.find('.choose_bid')			.show('slow');	}</script>