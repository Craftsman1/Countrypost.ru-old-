<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Добавление товара в
		<a href="<?= BASEURL . "client/order/$order->order_id" ?>">заказ №<?= $order->order_id ?></a>
	</h2>
	<? View::show("/client/elements/showAddProduct/$order->order_type"); ?>
</div>
<script type="text/javascript">
	$(function() {
		// ссылка на скриншот
		$('.screenshot_link_box img').click(function() {
			$('.screenshot_link_box,.screenshot_uploader_box').hide('slow');
			$('.screenshot_switch').show('slow');
		});

		$('form#onlineItemForm,form#offlineItemForm,form#serviceItemForm,form#deliveryItemForm,form#mail_forwardingItemForm').ajaxForm({
			dataType:'json',
			iframe:true,
			beforeSubmit:function (formData, jqForm, options) {
				$('img.progress').show();
			},
			success: function(response) {
				if (response)
				{
					// Ответ не является числовым значением
					if (isNaN(response.odetail_id) || isNaN(response.order_id)) {
						error('top', response);
					}
					// Все в порядке, добавляем товар
					else
					{
						success('top', 'Товар №' + response.odetail_id + ' успешно добавлен в заказ.');
					}

					window.location = '<?= BASEURL . "client/order/$order->order_id" ?>';
				}
				// Ответ не был получен
				else {
					removeItemProgress(item.id);
					error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
				}
			},
			error: function(response) {
				$('img.progress').hide();
				error('top', 'Товар не добавлен. Заполните все поля и попробуйте еще раз.');
			},
			complete: function() {
				$('img.progress').hide();
			}
		});

	});

	// скриншот
	function showScreenshotLink() {
		$('.screenshot_link_box').show('slow');
		if ($('.screenshot_link_box').val() == '') {
			$('.screenshot_link_box').val('ссылка на скриншот')
		}
		$('.screenshot_switch').hide('slow');
	}

	function showScreenshotUploader() {
		$('.screenshot_uploader_box').show('slow');
		if ($('.screenshot_link_box').val() == 'ссылка на скриншот') {
			$('.screenshot_link_box').val('')
		}
		$('.screenshot_switch').hide('slow');
	}
</script>