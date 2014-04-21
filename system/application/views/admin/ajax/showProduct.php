<b class='bulk' />
<script>
	<? if (isset($odetail_id)) : ?>
	<? if (isset($error)) : ?>
	$('#odetail_action<?=$odetail_id?>')			
		.html('<a href="javascript:cancelItem(<?=$odetail_id?>)" id="odetail_cancel<?=$odetail_id?>"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a><br /><a href="javascript:saveItem(<?=$odetail_id?>)" id="odetail_save<?=$odetail_id?>"><img border="0" src="/static/images/done-filed.png" title="Сохранить"></a><br /><a href="javascript:deleteItem(<?=$odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a><br /><em class="red-color" id="productMessage<?=$odetail_id?>"><?=$error?></em>');
	<? else : 		
		if (stripos($odetail->odetail_link, 'http://') !== 0)
		{
			$odetail->odetail_link = 'http://'.$odetail->odetail_link;
		}
		
		if (isset($odetail->odetail_img) && 
			stripos($odetail->odetail_img, 'http://') !== 0)
		{
			$odetail->odetail_img = 'http://'.$odetail->odetail_img;
		} ?>
	var snippet = "<td id='odetail_shop_name<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_shop_name, $odetail->odetail_id)?></td><td id='odetail_product_name<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_name, $odetail->odetail_id)?></td><td id='odetail_product_color<?=$odetail->odetail_id?>'><?=shortenText($odetail->odetail_product_color.' / '.$odetail->odetail_product_size.' / '.$odetail->odetail_product_amount, $odetail->odetail_id)?></td><td id='odetail_img<?=$odetail->odetail_id?>'><? if (isset($odetail->odetail_img)) : ?><a href='#' onclick='window.open(\"<?=$odetail->odetail_img?>\");return false;'><?=(strlen($odetail->odetail_img)>17?substr($odetail->odetail_img,0,17).'...':$odetail->odetail_img)?></a><? else : ?><a href='javascript:void(0)' onclick='setRel(<?=$odetail->odetail_id?>);'>Просмотреть скриншот <a rel='lightbox_<?=$odetail->odetail_id?>' href='/admin/showScreen/<?=$odetail->odetail_id?>' style='display:none;'>Посмотреть</a></a><? endif; ?></td><td id='odetail_link<?=$odetail->odetail_id?>'><a href='#' onclick='window.open(\"<?=$odetail->odetail_link?>\");return false;'><?=(strlen($odetail->odetail_link)>17?substr($odetail->odetail_link,0,17).'...':$odetail->odetail_link)?></a></td>";
	
	var odetail<?=$odetail->odetail_id?> = {"odetail_id":"<?=$odetail->odetail_id?>","odetail_client":"<?=$odetail->odetail_client?>","odetail_manager":"<?=$odetail->odetail_manager?>","odetail_order":"<?=$odetail->odetail_order?>","odetail_link":"<?=$odetail->odetail_link?>","odetail_shop_name":"<?=$odetail->odetail_shop_name?>","odetail_product_name":"<?=$odetail->odetail_product_name?>","odetail_product_color":"<?=$odetail->odetail_product_color?>","odetail_product_size":"<?=$odetail->odetail_product_size?>","odetail_product_amount":"<?=$odetail->odetail_product_amount?>","odetail_img":"<?=$odetail->odetail_img?>"};
	
	$('#odetail_shop_name<?=$odetail->odetail_id?>,#odetail_product_name<?=$odetail->odetail_id?>,#odetail_product_color<?=$odetail->odetail_id?>,#odetail_img<?=$odetail->odetail_id?>,#odetail_link<?=$odetail->odetail_id?>').remove();
	$('#product<?=$odetail->odetail_id?> #odetail_id<?=$odetail->odetail_id?>').after(snippet); 

	$('#odetail_action<?=$odetail_id?>').find('img,br').remove();
				
	$('#odetail_action<?=$odetail_id?>')			
		.prepend('<a href="javascript:editItem(<?=$odetail_id?>)" id="odetail_edit<?=$odetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Изменить"></a><br /><a href="javascript:deleteItem(<?=$odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>');
	
	$('#odetail_pricedelivery<?= $odetail->odetail_id?>').val('<?= $odetail->odetail_pricedelivery?>');
	$('#odetail_price<?= $odetail->odetail_id?>').val('<?= $odetail->odetail_price?>');
	$('#odetail_status<?= $odetail->odetail_id?>').val('<?=$odetail->odetail_status?>');
	<? endif; ?>
	$('#product<?=$odetail_id?>').find('input,textarea').attr('readonly', false);
	<? if ($odetail->updated_by_client == 1) : ?>
	$('td#odetail_id<?= $odetail->odetail_id?>').addClass('red-color');
	<? else : ?>
	$('td#odetail_id<?= $odetail->odetail_id?>').removeClass('red-color');
	<? endif; ?>
	<? endif; ?>
	$('.bulk').remove();
</script>
</b>