<script type="text/javascript" src="/static/js/easyTooltip.js"></script>
<script type="text/javascript" src="/static/js/jquery.numeric.js"></script> 
<script type="text/javascript">
	$(document).ready(function(){
		$("img.tooltip").easyTooltip();
		$("img.tooltip_rbk").easyTooltip({
			tooltipId: "tooltip_id",
			content: '\
				<div class="box">\
					<p>Если в течении 7 дней этот товар не был доставлен и не появился в разделе "Посылки ожидающие отправки" поставьте тут галочку и нажмите сохранить.</p>\
				</div>\
			'
		});
	});
	
	function setstatusundelivered(o)
	{
		if(o.checked)
		{
			$.get("/client/setStatusUndelivered/" + o.value, { odetail_id: o.value}, function(data){
			});
		}
	}

</script>
<div class='content'>
	<h2>Заказ № <?=$order->order_id?></h2>
    <? View::show($viewpath.'elements/div_float_manual', array(
		'updatePriceNotification' => ($order->order_status == 'sended' || 
			$order->order_status == 'payed' ||
			$order->order_status == 'not_delivered'))); ?>	
    <? View::show($viewpath.'elements/div_order_item_details'); ?>	
	<form class='admin-inside'>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>Общая цена заказа с учетом местной доставки</th>
					<th>Статус</th>
				</tr>
				<tr>
					<td><?=$order->order_cost?>$<br />
						<hr />
						Общая стоимость указанных товаров: <?=$order->order_products_cost?>$<br />
						Цена местной доставки: <?=$order->order_delivery_cost?>$<!--<br />
						Примерный вес посылки: <?=$order->order_weight?>кг-->
					</td>
					<td>
						<span class='order_status'><?=$order->order_status_desc?></span>
						<? if ($order->order_status == 'payed' || $order->order_status == 'sended') : ?>
						:<br />
						$<?=$order->order_cost_payed?>
						<? endif; ?>						
					</td>
				</tr>
			</table>
		</div>
	</form>
	
	<br /><hr />

	<h3>Товары для покупки в заказе:</h3>
	<? View::show($viewpath.'elements/div_order_item_preview'); ?>	
	<div class='back'>
		<a class='back' href='javascript:history.back();'><span>Назад</span></a>
	</div><br />	
    <? if (isset($result->join_status)) : if ($result->e<0):?>
		<em style="color:red;"><?=$result->m?></em>
		<br />
		<br />
	<?elseif ($result->e>0):?>
		<em style="color:green;"><?=$result->m?></em>
		<br />
		<br />
	<?endif;endif;?>

	<? if ($order->order_status != 'sended' && $order->order_status != 'not_delivered') : ?>
    <div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Добавить товар" name="add" onclick="lay2()">
			</div>
		</div>
	</div>
	<? endif; ?>
    
	<form class='admin-inside' id='detailsForm' action='<?=$selfurl?>updateProductAjax' enctype="multipart/form-data" method="POST">
		<input name="order_id" type="hidden" value="<?=$order->order_id?>"/>
		<input id="odetail_id" name="odetail_id" type="hidden" value=""/>
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<tr>
					<th>№</th>
					<th>Название магазина</th>
					<th>Наименование</th>
					<th>Цвет / Размер / Кол-во</th>
					<th>Скриншот</th>
					<th>Ссылка на товар</th>
					<th>Статус</th>
                    <th>Цена</th>
                    <th>Местная доставка</th>
					<? if ($order->order_status != 'not_delivered' && $order->order_status != 'sended') : ?>
					<th>Изменить* / Удалить</th>
					<? endif; ?>
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
				<tr id='product<?=$odetail->odetail_id?>'>
					<script>
						var odetail<?=$odetail->odetail_id?> = {"odetail_id":"<?=$odetail->odetail_id?>","odetail_client":"<?=$odetail->odetail_client?>","odetail_manager":"<?=$odetail->odetail_manager?>","odetail_order":"<?=$odetail->odetail_order?>","odetail_link":"<?=$odetail->odetail_link?>","odetail_shop_name":"<?=$odetail->odetail_shop_name?>","odetail_product_name":"<?=$odetail->odetail_product_name?>","odetail_product_color":"<?=$odetail->odetail_product_color?>","odetail_product_size":"<?=$odetail->odetail_product_size?>","odetail_product_amount":"<?=$odetail->odetail_product_amount?>","odetail_img":"<?=$odetail->odetail_img?>"};
	                </script>
					<td id='odetail_id<?=$odetail->odetail_id?>'><?=$odetail->odetail_id?></td>
					<td id='odetail_shop_name<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_shop_name, $odetail->odetail_id)?></td>
					<td id='odetail_product_name<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_name, $odetail->odetail_id)?></td>
					<td id='odetail_product_color<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_color.' / '.$odetail->odetail_product_size.' / '.$odetail->odetail_product_amount, $odetail->odetail_id)?></td>
					<td id='odetail_img<?=$odetail->odetail_id?>'>
						<? if (isset($odetail->odetail_img)) : ?>
						<a href="#" onclick="window.open('<?=$odetail->odetail_img?>');return false;"><?=(strlen($odetail->odetail_img)>17?substr($odetail->odetail_img,0,17).'...':$odetail->odetail_img)?></a>
						<? else : ?>
						<a href="javascript:void(0)" onclick="setRel(<?=$odetail->odetail_id?>);">
	                        Просмотреть скриншот <a rel="lightbox_<?=$odetail->odetail_id?>" href="/client/showScreen/<?=$odetail->odetail_id?>" style="display:none;">Посмотреть</a>
						</a>
						<? endif; ?>
					</td>
					<td id='odetail_link<?=$odetail->odetail_id?>'><a href="#" onclick="window.open('<?=$odetail->odetail_link?>');return false;"><?=(strlen($odetail->odetail_link)>17?substr($odetail->odetail_link,0,17).'...':$odetail->odetail_link)?></a></td>
					<td id='odetail_status<?=$odetail->odetail_id?>'><?=$odetail->odetail_status_desc?>
						<? if (($order->order_status == 'sended' || $order->order_status == 'not_delivered') &&
								($odetail->odetail_status == 'available' || $odetail->odetail_status == 'sent')) : ?>
                        <br />
                        <input type="checkbox" value="<?=$odetail->odetail_id?>" name="odetail_status<?=$odetail->odetail_id?>"/>Не доставлен <img class="tooltip tooltip_rbk" src="/static/images/mini_help.gif">
						<? endif; ?>
					</td>
                    <td><?=$odetail->odetail_price?></td>
                    <? if (!$odetail->odetail_joint_id) : 
						$order_delivery_cost += $odetail->odetail_pricedelivery;
					?>
					<td>
						<?=$odetail->odetail_pricedelivery?>
					</td>
					<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
							$odetail_joint_id = $odetail->odetail_joint_id;
							$odetail_joint_count = $odetail->odetail_joint_count;
							$order_delivery_cost += $odetail->odetail_joint_cost;
					?>
					<td rowspan="<?=$odetail_joint_count?>">
						<?=$odetail->odetail_joint_cost?>
					</td>
					<? endif; ?>
					<? if ($order->order_status != 'sended' && $order->order_status != 'not_delivered') : ?>
					<td align="center" id="odetail_action<?=$odetail->odetail_id?>">
						<a href="javascript:editItem(<?=$odetail->odetail_id?>)" id="odetail_edit<?=$odetail->odetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
						<br />
						<a href="javascript:deleteItem(<?=$odetail->odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>						
					</td>
					<? endif; ?>
				</tr>
				<?endforeach; endif;?>
                <tr>
					<td colspan="7">&nbsp;</td>
                    <td><?=$order_products_cost?></td>
                    <td><?=$order_delivery_cost?></td>
					<? if ($order->order_status != 'sended' && $order->order_status != 'not_delivered') : ?>
					<td align="center">&nbsp;</td>
					<? endif; ?>
				</tr>
				<tr class='last-row'>
					<td colspan='10'>
						<br />
						<? if ($order->order_status != 'not_delivered' && $order->order_status != 'sended') : ?>
						<div id="tableComments" style="text-align:left;float:left;">
							* для редактирования товара кликните по иконке <img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a> или дважды кликните по товару в списке
						</div>
						<? endif; ?>
						<? if ($order->order_status == 'sended' || $order->order_status == 'not_delivered') : ?>
						<div class='float'>	
							<div class='submit'><div><input type='button' value='Сохранить' onclick='updateStatuses();'/></div></div>
						</div>
						<? endif; ?>
					</td>
				</tr>
			</table>
		</div>
	</form>
    <? if ($order->order_status != 'payed' && $order->order_status != 'sended' && $order->order_status != 'not_delivered') : ?>
    <div style="height: 50px;" class="admin-inside">
		<div class="submit">
			<div>
				<input type="button" value="Добавить товар" name="add" onclick="lay2()">
			</div>
		</div>
	</div>
	<? endif; ?>
	<a name="comments"></a>
	<h3>Комментарии к заказу</h3>
	<form class='comments' action='<?=$selfurl?>addOrderComment/<?=$order->order_id?>' method='POST'>
		<?if (!$comments):?>
			<div class='comment'>
				Пока нет комментариев<br/>
			</div>
		<?else:?>
			<? foreach ($comments as $comment):?>
				<div class='comment'>
					<div class='question'>
						<span class="name">№<?= $comment->ocomment_id ?>
						<?if ($comment->ocomment_user == $order->order_client):?>
							Вы:
						<?elseif ($comment->ocomment_user == $order->order_manager):?>
							Менеджер:
						<?else:?>
							Администрация:
						<?endif;?>
							<br /><?=formatCommentDate($comment->ocomment_time)?>
						</span>
						<p><?=html_entity_decode($comment->ocomment_comment)?></p>
					</div>
				</div>
			<? endforeach; ?>
		<?endif;?>
		<h3>Оставьте комментарий:</h3>
		<div class='add-comment'>
			<div><textarea id='comment' name='comment'></textarea></div>
			<div class='submit comment-submit'><div><input type='submit' name="add" value="Добавить" /></div></div>
			<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
		</div>
	</form>
</div>
<script>
	function deleteItem(id) {
		if (confirm("Вы уверены, что хотите удалить товар № " + id + " ?")){
			window.location.href = '<?=$selfurl?>deleteProduct/' + id;
		}
	}

	<? if ($order->order_status != 'sended' && $order->order_status != 'not_delivered') : ?>					
	$(document).ready(function() {
		$('#detailsForm').ajaxForm({
			target: '<?=$selfurl?>updateProductAjax/',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
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

	function editItem(id) {
		if (!$('#odetail_shop_name' + id + ' textarea').length)
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

	function cancelItem(id) {
		if ($('#odetail_shop_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
			
			$('#odetail_shop_name' + id).html(odetail['odetail_shop_name']);
			$('#odetail_product_name' + id).html(odetail['odetail_product_name']);
			$('#odetail_product_color' + id).html(odetail['odetail_product_color'] + ' / ' + odetail['odetail_product_size'] + ' / ' + odetail['odetail_product_amount']);

			$('#odetail_link' + id + ',#odetail_img' + id).find('label,textarea,input,br').remove();
			$('#odetail_link' + id + ',#odetail_img' + id).children().show();
			$('#odetail_img' + id + ' a[rel]').hide();
						
			$('#odetail_action' + id)			
				.html('<a href="javascript:editItem(' + id + ')" id="odetail_edit' + id + '"><img border="0" src="/static/images/comment-edit.png" title="Изменить"></a><br /><a href="javascript:deleteItem(' + id + ')"><img border="0" src="/static/images/delete.png" title="Удалить"></a>');
		}
	}

	function saveItem(id) {
		if ($('#odetail_shop_name' + id + ' textarea').length)
		{
			$('#odetail_shop_name' + id).parent().find('input,textarea').attr('readonly', true);
			$('#odetail_action' + id).html('<img border="0" src="/static/images/lightbox-ico-loading.gif" title="Товар сохраняется..."><br><a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>');
			$('#odetail_id').val(id);
			$('#detailsForm').submit();						
		}
	}
	<? else : ?>
	function updateStatuses()
	{
		$('#detailsForm')
			.attr('action', '<?=$selfurl?>updateOdetailStatuses/')
			.submit();
	}
	<? endif; ?>
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
	
	var order_country = '<?=$order_country?>';
	<? echo editor('comment', 212, 650, 'PackageComment') ?>
</script>