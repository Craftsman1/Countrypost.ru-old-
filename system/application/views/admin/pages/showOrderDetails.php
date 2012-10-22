<div class='content admin-inside'>
	<h3>Заказ № <?= $order->order_id ?></h3>
    <? View::show($viewpath.'elements/div_float_manual'); ?>	
	<div class='back'>
		<a class='back' href='<?= $selfurl ?><?= $back_handler ?>'><span>Назад к списку</span></a>
	</div><br />	
	<form class='admin-inside' id="orderForm" action="<?= $selfurl ?>updateOrderDetails" method="POST">
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th>№ клиента</th>
					<th>№ партнера</th>
					<th>Общая цена заказа с<br />учетом местной доставки</th>
					<th>Статус</th>
				</tr>
				<tr>
					<td><?= $order->order_client?></td>
					<td><?= $order->order_manager?></td>
					<td align="right"><?= $order->order_cost?>$<br />
						<hr />
						<span>Общая стоимость указанных товаров ($): </span>
						<input name="order_products_cost" type="text" value="<?= $order->order_products_cost?>"/><br /><br />
						<span>Цена местной доставки ($): <span>
						<input name="order_delivery_cost" type="text" value="<?= $order->order_delivery_cost?>"/><br />
						<input name="order_weight" type="hidden" value="<?= $order->order_weight?>"/>
						<input name="order_id" type="hidden" value="<?= $order->order_id ?>"/>
					</td>
					<td>
						<?= $order->order_status_desc?>
						<? if ($order->order_status == 'payed' || $order->order_status == 'sended') : ?>
						:<br />
						$<?= $order->order_cost_payed?>
						<? endif; ?>						
					</td>
				</tr>
				<tr class='last-row'>
					<td colspan='4'>
						<? if ( ! $order->confirmation_sent) : ?>
						<div>	
							<div class='submit'><div><input type='button' value='Заказ доставлен' onclick="javascript:sendConfirmation();" /></div></div>
						</div>
						<? endif; ?>
						<div class='float'>	
							<div class='submit'><div><input type='submit' value='Сохранить' <? if ($order->order_status == 'not_delivered' || $order->order_status == 'payed' || $order->order_status == 'sended') : ?>onclick="return confirmUpdateCost();"<? endif; ?> /></div></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<br />
	<hr />
	<h3>Товары для покупки в заказе:</h3>
	<? View::show($viewpath.'elements/div_order_item_preview'); ?>	
	<? if (isset($result->join_status)) : if ($result->e<0) : ?>
		<em style="color:red;"><?= $result->m?></em>
		<br />
		<br />
	<? elseif ($result->e>0) : ?>
		<em style="color:green;"><?= $result->m?></em>
		<br />
		<br />
	<? endif;endif; ?>
	<div style="height: 50px;" class="admin-inside">
		<div class='submit'>
			<div>
				<input type="button" value="Добавить товар" name="add" onclick="lay2()">
			</div>
		</div>
		<div class="submit">
			<div>
				<a href="<?= $selfurl ?>exportOrder/<?= $order->order_id ?>" style="text-decoration:none;">
					<input type="button" value="Экспорт">
				</a>
			</div>
		</div>
		<form id="importForm" action="<?= $selfurl ?>importOrder/<?= $order->order_id ?>" enctype="multipart/form-data" method="POST" style="vertical-align:baseline;">
			<div class="submit">
				<div>
					<input type="button" value="Импорт" onclick="openFileBrowser(this);" />
					<input type="submit" value="Отправить" id="send_import_button" style="display:none;" />
				</div>
			</div>
			<input type="file" id="file_browser" value="Импорт" name="importOrder" style="display:none;margin-top:12px;" />
		</form>
	</div>
	<form class='admin-inside' id="detailsForm" action="<?= $selfurl ?>updateProductAjax" enctype="multipart/form-data" method="POST">
		<input name="order_id" type="hidden" value="<?= $order->order_id ?>"/>
		<input id="odetail_id" name="odetail_id" type="hidden" value=""/>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th nowrap>
						№ <input type='checkbox' id='select_all' />
					</th>
					<th>Название магазина</th>
					<th>Наименование</th>
					<th>Цвет / Размер / Кол-во</th>
					<th>Скриншот</th>
					<th>Ссылка на товар</th>
                    <th>Цена</th>
                    <th>Местная доставка</th>
					<th>Статус</th>
					<th></th>
				</tr>
				<? $order_products_cost = 0; 
					$order_delivery_cost = 0; 
					$odetail_joint_id = 0;
					$odetail_joint_count = 0;
					
				if ($odetails) : foreach($odetails as $odetail) : 
					$order_products_cost += $odetail->odetail_price;
					
					if (stripos($odetail->odetail_link, 'http://') !== 0)
					{
						$odetail->odetail_link = 'http://'.$odetail->odetail_link;
					}
					
					if (isset($odetail->odetail_img) && 
						stripos($odetail->odetail_img, 'http://') !== 0)
					{
						$odetail->odetail_img = 'http://'.$odetail->odetail_img;
					}
				?>
				<tr id='product<?= $odetail->odetail_id?>'>
					<script>
						var odetail<?= $odetail->odetail_id?> = {"odetail_id":"<?= $odetail->odetail_id?>","odetail_client":"<?= $odetail->odetail_client?>","odetail_manager":"<?= $odetail->odetail_manager?>","odetail_order":"<?= $odetail->odetail_order?>","odetail_link":"<?= $odetail->odetail_link?>","odetail_shop_name":"<?= $odetail->odetail_shop_name?>","odetail_product_name":"<?= $odetail->odetail_product_name?>","odetail_product_color":"<?= $odetail->odetail_product_color?>","odetail_product_size":"<?= $odetail->odetail_product_size?>","odetail_product_amount":"<?= $odetail->odetail_product_amount?>","odetail_img":"<?= $odetail->odetail_img?>"};
	                </script>
	                <td id='odetail_id<?= $odetail->odetail_id?>' <? if ($odetail->updated_by_client == 1) : ?>class="red-color" title="Товар изменен клиентом"<? endif; ?>>
						<?= $odetail->odetail_id?>
						<input type="checkbox" name="join<?= $odetail->odetail_id?>" id="join<?= $odetail->odetail_id?>">
					</td>
					<td id='odetail_shop_name<?= $odetail->odetail_id?>'>
						<?=shortenText($odetail->odetail_shop_name, $odetail->odetail_id) ?>
					</td>
					<td id='odetail_product_name<?= $odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_name, $odetail->odetail_id) ?></td>
					<td id='odetail_product_color<?= $odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_color.' / '.$odetail->odetail_product_size.' / '.$odetail->odetail_product_amount, $odetail->odetail_id) ?></td>
					<td id='odetail_img<?= $odetail->odetail_id?>'>
						<? if (isset($odetail->odetail_img)) : ?>
						<a href="#" onclick="window.open('<?= $odetail->odetail_img?>');return false;"><?=(strlen($odetail->odetail_img)>17?substr($odetail->odetail_img,0,17).'...':$odetail->odetail_img) ?></a>
						<? else : ?>
						<a href="javascript:void(0)" onclick="setRel(<?= $odetail->odetail_id?>);">
	                        Просмотреть скриншот <a rel="lightbox_<?= $odetail->odetail_id?>" href="/admin/showScreen/<?= $odetail->odetail_id?>" style="display:none;">Посмотреть</a>
						</a>
						<? endif; ?>
					</td>
					<td id='odetail_link<?= $odetail->odetail_id?>'>
						<a href="#" onclick="window.open('<?= $odetail->odetail_link?>');return false;"><?=(strlen($odetail->odetail_link)>17?substr($odetail->odetail_link,0,17).'...':$odetail->odetail_link) ?></a>
					</td>
                    <td>
						<?	if ($odetail->odetail_status == 'purchased' || 
								$odetail->odetail_status == 'received' || 
								$odetail->odetail_status == 'not_delivered') : ?>
						<?= $odetail->odetail_price?>
						<? else : ?>
						<input size="3" type="text" id="odetail_price<?= $odetail->odetail_id?>" name="odetail_price<?= $odetail->odetail_id?>" value="<?= $odetail->odetail_price?>" >
						<? endif; ?>
					</td>
                    <? if ( ! $odetail->odetail_joint_id) : 
						$order_delivery_cost += $odetail->odetail_pricedelivery;
					?>
					<td>
						<?	if ($odetail->odetail_status == 'purchased' || 
								$odetail->odetail_status == 'received' || 
								$odetail->odetail_status == 'not_delivered') : ?>
						<?= $odetail->odetail_pricedelivery?>
						<? else : ?>
						<input size="3" type="text" id="odetail_pricedelivery<?= $odetail->odetail_id?>" name="odetail_pricedelivery<?= $odetail->odetail_id?>" value="<?= $odetail->odetail_pricedelivery?>">
						<? endif; ?>
						</td>
					<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
							$odetail_joint_id = $odetail->odetail_joint_id;
							$odetail_joint_count = $odetail->odetail_joint_count;
							$order_delivery_cost += $odetail->odetail_joint_cost;
					?>
					<td rowspan="<?= $odetail_joint_count?>">
						<? if ($odetail->odetail_joint_enabled) : ?>
						<input size="3" type="text" name="odetail_joint_cost<?= $odetail->odetail_joint_id?>" value="<?= $odetail->odetail_joint_cost?>">
						<? else : ?>
						<?= $odetail->odetail_joint_cost?>
						<? endif; ?>
						<br />
						<a href="#" onclick="removeJoint(<?= $odetail->odetail_joint_id?>);">Отменить</a>
					</td>
					<? endif; ?>
					<td>
						<select class="select" id="odetail_status<?= $odetail->odetail_id?>" name="odetail_status<?= $odetail->odetail_id?>">
                        <?
                        foreach ($odetails_statuses as $key => $val)
						{
								?><option value="<?= $key?>" <? if ($odetail->odetail_status == $key) : ?>selected="selected"<? endif; ?>><?= $val?></option><?
						}
						?>
						</select>
					</td>
					<td align="center" id="odetail_action<?= $odetail->odetail_id?>">
						<a href="javascript:editItem(<?= $odetail->odetail_id?>)" id="odetail_edit<?= $odetail->odetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
						<br />
						<a href="javascript:deleteProduct(<?= $odetail->odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
					</td>
				</tr>
				<? endforeach; endif; ?>
                <tr>
					<td colspan="6">&nbsp;</td>
                    <td><?= $order_products_cost?></td>
                    <td><?= $order_delivery_cost?></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr class='last-row'>
					<td colspan='10'>
						<div class='float'>	 
							<div class='submit'><div><input type='button' value='Сохранить' onclick='<? if ($order->order_status == 'not_delivered' || $order->order_status == 'payed' || $order->order_status == 'sended') : ?>if (confirmUpdateCost()) <? endif; ?>updateStatuses();'/></div></div>
						</div>
						<div class='floatleft'>	
							<div class='submit'><div><input type='button' value='Объединить' onclick="joinProducts();"/></div></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
    <div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Добавить товар" name="add" onclick="lay2()">
			</div>
		</div>
	</div>
	<a name="comments"></a>
	<h3>Комментарии к заказу</h3>
	<br />
	<form class='comments' id="commentForm" action='<?= $selfurl ?>addOrderComment/<?= $order->order_id ?>' method='POST'>
		<? if ( ! $comments) : ?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<? else : ?>
			<? foreach ($comments as $comment) : ?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->ocomment_id ?>
						<? if ($comment->ocomment_user == $order->order_client) : ?>
							Клиент:
						<? elseif ($comment->ocomment_user == $order->order_manager) : ?>
							Менеджер:
						<? else : ?>
							Вы:
						<? endif; ?>
							<br /><?=formatCommentDate($comment->ocomment_time) ?>
						</span>
						<div id="comment_<?= $comment->ocomment_id?>">
							<?=html_entity_decode($comment->ocomment_comment) ?>
						</div>
						
							<a href="javascript:updateItem(<?= $comment->ocomment_id?>, '№<?= $comment->ocomment_id ?> <? if ($comment->ocomment_user == $order->order_manager) : ?>Менеджер<? elseif ($comment->ocomment_user == $order->order_client) : ?>Клиент<? else : ?>Вы<? endif; ?>:', <?= $comment->ocomment_id?>);">Изменить</a>
							<a href="javascript:deleteItem(<?= $comment->ocomment_id?>);"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
							<br /><br />
					</div>
				</div>
			<? endforeach; ?>
		<? endif; ?>
		<a name="edit_comment_area" />
		<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
		<h3 class="update" style="display:none;">Редактирование комментария</h3>
		<div class='comment update' style="border:0;">			
			<div class='question update' style="display:none;">
				<span id="comment_user" class="name">
				</span>
			</div>
		</div>
		<br class="update" style="display:none;"/>
		<div class='add-comment update' style="display:none;">
			<input type='hidden' id='comment_id' name='comment_id' />
			<div><textarea id='comment_update' name='comment_update'></textarea></div>
			<div class='submit comment-submit'><div><input type='submit' id="update" name="update" value="Сохранить" onclick="editComment();"/></div></div>
			<br />
		</div>
		<div class="back update" style="display:none;">
			<a href="#" onclick="cancel();" class="back"><span>Отмена</span></a>
		</div>

		<h3 class='save'>Оставьте комментарий:</h3>
		<div class='add-comment save'>
			<div><textarea id='comment' name='comment'></textarea></div>
			<div class='submit comment-submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$(function() {
		$('#orderForm input:text').keypress(function(event){validate_number(event);});
		
		$('#detailsForm').ajaxForm({
			type: 'POST',
			dataType: 'html',
			iframe: true,
			before: function(formData, jqForm, options)
			{
			},
			success: function(response)
			{
				$('#detailsForm').append($(response));
			},
			error: function(response)
			{
			}
		});
	});
	
	function openFileBrowser(button)
	{
		$(button).hide();
		$('input#send_import_button').show();
		$('input#file_browser').show().click();
	}
	
	function editItem(id) 
	{
		if ( ! $('#odetail_shop_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
		
			$('#odetail_shop_name' + id)
				.html('<textarea name="shop' + id + '" style="width:50px;resize:auto;">' + odetail['odetail_shop_name'] + '</textarea>');

			$('#odetail_product_name' + id)
				.html('<textarea name="oname' + id + '" style="width:75px;resize:auto;">' + odetail['odetail_product_name'] + '</textarea>');

			$('#odetail_product_color' + id)
				.html('')
				.append('<textarea name="ocolor' + id + '" style="width:52px;height:14px;resize:auto;">' + odetail['odetail_product_color'] + '</textarea><br />')
				.append('<textarea name="osize' + id + '" style="width:52px;height:14px;resize:auto;">' + odetail['odetail_product_size'] + '</textarea><br />')
				.append('<textarea name="oamount' + id + '" style="width:52px;height:14px;resize:auto;">' + odetail['odetail_product_amount'] + '</textarea>');

			$('#odetail_link' + id)
				.children().hide().parent()
				.append('<textarea name="olink' + id + '" style="width:75px;resize:auto;">' + odetail['odetail_link'] + '</textarea>');

			$('#odetail_img' + id)
				.children().hide().parent()
				.append('<div style="width:120px;"><input type="radio" value="1" id="img' + id + '" name="img' + id + '" ><label for="img' + id + '"><textarea id="userfileimg' + id + '" type="text" name="userfileimg' + id + '" maxlength="4096" style="top:0;height:14px;width:92px;resize:auto;">' + odetail['odetail_img'] + '</textarea></label><br><input type="radio" value="2" id="img2' + id + '" name="img' + id + '" ><label for="img2' + id + '"><input id="userfile' + id + '" type="file" name="userfile" style="width:95px;"></label></div>');
				
			$('#odetail_edit' + id).remove();
			$('#odetail_action' + id + ' br').remove();
				
			$('#odetail_action' + id)
				.prepend('<a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a><br /><a href="javascript:saveItem(' + id + ')" id="odetail_save' + id + '"><img border="0" src="/static/images/done-filed.png" title="Сохранить"></a><br />');
		}
		else
		{
			cancelItem(id);
		}
	}

	function cancelItem(id) 
	{
		if ($('#odetail_shop_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
			
			$('#odetail_shop_name' + id).html(odetail['odetail_shop_name']);
			$('#odetail_product_name' + id).html(odetail['odetail_product_name']);
			$('#odetail_product_color' + id).html(odetail['odetail_product_color'] + ' / ' + odetail['odetail_product_size'] + ' / ' + odetail['odetail_product_amount']);

			$('#odetail_link' + id + ',#odetail_img' + id).find('label,input,textarea,br').remove();
			$('#odetail_link' + id + ',#odetail_img' + id).children().show();
			$('#odetail_img' + id + ' a[rel]').hide();
						
			$('#odetail_action' + id)			
				.html('<a href="javascript:editItem(' + id + ')" id="odetail_edit' + id + '"><img border="0" src="/static/images/comment-edit.png" title="Изменить"></a><br /><a href="javascript:deleteItem(' + id + ')"><img border="0" src="/static/images/delete.png" title="Удалить"></a>');
		}
	}

	function saveItem(id) 
	{
		if ($('#odetail_shop_name' + id + ' textarea').length)
		{
			$('#odetail_shop_name' + id).parent().find('input,textarea').attr('readonly', true);
			$('#odetail_action' + id).html('<img border="0" src="/static/images/lightbox-ico-loading.gif" title="Товар сохраняется..."><br><a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>');
			$('#odetail_id').val(id);
			$('#detailsForm').submit();						
		}
	}

	function setRel(id)
	{
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}

	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if ( ! regex.test(key)) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}

	function updateItem(comment_id, user_login)
	{
		$('.save').hide();
		$('.update').show();
		
		$('#comment_user').html(user_login);
		$('#comment_id').val(comment_id);
		var oEditor = FCKeditorAPI.GetInstance('comment_update');
		oEditor.SetHTML($('#comment_'+comment_id).html());

		window.location.href = '#edit_comment_area';
	}

	function editComment()
	{
		var $f = document.getElementById('commentForm');
		$f.action = '<?= $selfurl ?>addOrderComment/<?= $order->order_id ?>/'+$('#comment_id').val();
	}
	
	function cancel()
	{
		$('.save').show();
		$('.update').hide();

		$('#comment_user').html('');
		$('#comment_id').val('');
		$('#comment_update').text('');
		history.back();
	}

	function deleteItem(id)
	{
		if (confirm("Вы уверены, что хотите удалить комментарий №" + id + "?"))
		{
			window.location.href = '<?= $selfurl ?>delOrderComment/<?= $order->order_id ?>/'+id;
		}
	}
	
	function confirmUpdateCost()
	{
		return confirm("После нажатия кнопки Сохранить весь заказ будет обновлен и пересчитан по текущему курсу на главной странице. После этого сумма заказа может как увеличиться, так и уменьшиться.\n\nОб этом обязательно нужно предупредить клиента, написав в комментариях, либо любым другим способом.\n\nБез необходимости не нажимать на эту кнопку в оплаченном заказе.");
	}
	
	function deleteProduct(id) {
		if (confirm("Вы уверены, что хотите удалить товар № " + id + " ?")){
			window.location.href = '<?= $selfurl ?>deleteProduct/' + id;
		}
	}

	function joinProducts()
	{
		var selectedProds = $('#detailsForm input:checked');
		
		if (selectedProds.length < 2)
		{
			alert("Выберите хотя бы 2 товара для объединения.");
			return false;			
		}
		
		if (confirm("Вы уверены, что хотите объединить выбранные товары?"))
		{
			var queryString = $('#detailsForm').formSerialize(); 
			$.post('<?= $selfurl ?>joinProducts/<?= $order->order_id ?>', queryString,
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
			window.location.href = '<?= $selfurl ?>removeOdetailJoint/<?= $order->order_id ?>/'+id;
		}
	}

	function sendConfirmation()
	{
		if (confirm("Отправить клиенту уведомление о доставке заказа №<?= $order->order_id ?>?"))
		{
			window.location.href = '<?= $selfurl ?>sendOrderConfirmation/<?= $order->order_id ?>';
		}
	}

	function updateStatuses()
	{
		 // $('#detailsForm').attr('action', '<?= $selfurl ?>updateOdetailStatuses/').submit();
		 // return false;
		var queryString = $('#detailsForm').formSerialize(); 
		$.post('<?= $selfurl ?>updateOdetailStatuses/', queryString, function(result) {
			self.location.reload();
		});
	}

	var order_country = '<?= $order_country?>';
	<? echo editor('comment', 212, 650, 'PackageComment') ?>
	<? echo editor('comment_update', 212, 650, 'PackageComment') ?>
</script>