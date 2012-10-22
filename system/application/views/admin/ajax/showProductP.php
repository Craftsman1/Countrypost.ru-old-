<b class='bulk' />
<script><!--

	<? if (isset($pdetail_id)) : ?>
	<? if (isset($error)) : ?>
	$('#pdetail_action<?=$pdetail_id?>')			
		.html('<a href="javascript:cancelItem(<?=$pdetail_id?>)" id="pdetail_cancel<?=$pdetail_id?>"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a><br /><a href="javascript:saveItem(<?=$pdetail_id?>)" id="pdetail_save<?=$pdetail_id?>"><img border="0" src="/static/images/done-filed.png" title="Сохранить"></a><br /><a href="javascript:deleteItem(<?=$pdetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a><br /><em class="red-color" id="productMessage<?=$pdetail_id?>"><?=$error?></em>');
	<? else : ?>
	<?					
					if (stripos($pdetail->pdetail_link, 'http://') !== 0)
					{
						$pdetail->pdetail_link = 'http://'.$pdetail->pdetail_link;
					}
					
					if (isset($pdetail->pdetail_img) && 
						stripos($pdetail->pdetail_img, 'http://') !== 0)
					{
						$pdetail->pdetail_img = 'http://'.$pdetail->pdetail_img;
					}
?>
	var snippet = "<td id='pdetail_product_name<?=$pdetail->pdetail_id?>'><?=shortenText($pdetail->pdetail_product_name, $pdetail->pdetail_id)?></td><td id='pdetail_product_color<?=$pdetail->pdetail_id?>'><?=shortenText($pdetail->pdetail_product_color.' / '.$pdetail->pdetail_product_size.' / '.$pdetail->pdetail_product_amount, $pdetail->pdetail_id)?></td><td id='pdetail_img<?=$pdetail->pdetail_id?>'><? if (isset($pdetail->pdetail_img)) : ?><a href='#' onclick='window.open(<?=$pdetail->pdetail_img?>);return false;'><?=(strlen($pdetail->pdetail_img)>17?substr($pdetail->pdetail_img,0,17).'...':$pdetail->pdetail_img)?></a><? else : ?><a href='javascript:void(0)' onclick='setRel(\"<?=$pdetail->pdetail_id?>_screenshot\");'>Просмотреть скриншот <a rel='lightbox_<?=$pdetail->pdetail_id?>_screenshot' href='/admin/showPdetailScreenshot/<?=$pdetail->pdetail_id?>' style='display:none;'>Посмотреть</a></a><? endif; ?></td><td id='pdetail_link<?=$pdetail->pdetail_id?>'><a href='#' onclick='window.open(<?=$pdetail->pdetail_link?>);return false;'><?=(strlen($pdetail->pdetail_link)>17?substr($pdetail->pdetail_link,0,17).'...':$pdetail->pdetail_link)?></a></td><? if ( ! $package->order_id) : ?><td id='pdetail_status<?=$pdetail->pdetail_id?>'><select class='select' name='pdetail_status<?=$pdetail->pdetail_id?>'><?
		foreach ($pdetails_statuses as $key => $val)
		{
			?><option value='<?= $key ?>' <? if ($pdetail->pdetail_status == $key) : ?>selected <? endif; ?>><?= $val ?></option><?
		}
		?></select></td><? endif; ?>";
	
	var pdetail<?=$pdetail->pdetail_id?> = {"pdetail_id":"<?=$pdetail->pdetail_id?>","pdetail_client":"<?=$pdetail->pdetail_client?>","pdetail_manager":"<?=$pdetail->pdetail_manager?>","pdetail_package":"<?=$pdetail->pdetail_package?>","pdetail_link":"<?=$pdetail->pdetail_link?>","pdetail_shop_name":"<?=$pdetail->pdetail_shop_name?>","pdetail_product_name":"<?=$pdetail->pdetail_product_name?>","pdetail_product_color":"<?=$pdetail->pdetail_product_color?>","pdetail_product_size":"<?=$pdetail->pdetail_product_size?>","pdetail_product_amount":"<?=$pdetail->pdetail_product_amount?>","pdetail_img":"<?=$pdetail->pdetail_img?>"};
	
	$('#pdetail_product_name<?=$pdetail->pdetail_id?>,#pdetail_product_color<?=$pdetail->pdetail_id?>,#pdetail_img<?=$pdetail->pdetail_id?>,#pdetail_link<?=$pdetail->pdetail_id?>,#pdetail_status<?=$pdetail->pdetail_id?>').remove();
	$('#product<?=$pdetail->pdetail_id?> #pdetail_id<?=$pdetail->pdetail_id?>').after(snippet); 

	$('#pdetail_action<?=$pdetail_id?>').find('img,br').remove();
				
	$('#pdetail_action<?=$pdetail_id?>')			
		.prepend('<a href="javascript:editItem(<?=$pdetail_id?>)" id="pdetail_edit<?=$pdetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Изменить"></a><br /><a href="javascript:deleteItem(<?=$pdetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>');
		
	<? if (isset($new_package_status)) : ?>
	$('.package_status').text('<?= $new_package_status?>');
	<? endif; ?>
	<? endif; ?>
	$('#product<?=$pdetail_id?>').find('input,textarea').attr('readonly', false);
	<? endif; ?>
	$('.bulk').remove();
--></script>
</b>