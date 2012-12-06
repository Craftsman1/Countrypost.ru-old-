<div class='table centered_td centered_th' style="<?= ($order AND count($order->details)) ? '' : 'display:none;'?>">
    <div class='angle angle-lt'></div>
    <div class='angle angle-rt'></div>
    <div class='angle angle-lb'></div>
    <div class='angle angle-rb'></div>
    <table id="new_products">
        <colgroup>
            <col style="width: 60px;">
            <col>
            <col>
            <col style="width: 85px;">
            <col style="width: 85px;">
            <col style="width: 85px;">
            <col style="width: 44px">
        </colgroup>
        <tr>
            <th nowrap>
                № <input type='checkbox' id='select_all'>
            </th>
            <th>Товар</th>
            <th>Скриншот</th>
            <th>
                Стоимость
            </th>
            <th>
                Местная<br>доставка
            </th>
            <th>
                Вес<br>товара
            </th>
            <th style="width:1px;"></th>
        </tr>
        <?
        $odetail_joint_id = 0;
        $odetail_joint_count = 0;

        if ($order)
        {
            $odetails = $order->details;
        }

        if ( ! empty($odetails)) : foreach($odetails as $odetail) :
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
        <tr id='product<?= $odetail->odetail_id ?>'>
            <td id='odetail_id<?= $odetail->odetail_id ?>'>
                <input type="checkbox" name="odetail_id" value="<?= $odetail->odetail_id ?>"/>
                <br>
                <?= $odetail->odetail_id ?>
                <br>
                <img id="progress<?= $odetail->odetail_id ?>"
                     class="float"
                     style="display:none;"
                     src="/static/images/lightbox-ico-loading.gif"/>

            </td>

            <form action='/client/updateNewProduct/<?= $order->order_id ?>/<?= $odetail->odetail_id ?>'
                  enctype="multipart/form-data"
                  method="POST">

            <td style="text-align: left; vertical-align: bottom;">
                <span class="plaintext">
                    <a target="_blank" href="<?= $odetail->odetail_link ?>"><?= $odetail->odetail_product_name ?></a>
                    <? if ($odetail->odetail_foto_requested) : ?>(требуется фото товара)<? endif; ?>
                    <br>
                    <b>Количество</b>: <?= $odetail->odetail_product_amount ?>
                    <b>Размер</b>: <?= $odetail->odetail_product_size ?>
                    <b>Цвет</b>: <?= $odetail->odetail_product_color ?>
                    <br>
                    <b>Комментарий</b>: <?= $odetail->odetail_comment ?>
                </span>

                <span class="producteditor" style="display: none;">
                    <br>
                    <b>Ссылка</b>:
                    <textarea class="link" name="link"></textarea>
                    <br>
                    <b>Наименование</b>:
                    <textarea class="name" name="name"></textarea>
                    <br>
                    <b>Количество</b>:
                    <textarea class="amount int" name="amount"></textarea>
                    <br>
                    <b>Размер</b>:
                    <textarea class="size" name="size"></textarea>
                    <br>
                    <b>Цвет</b>:
                    <textarea class="color" name="color"></textarea>
                    <br>
                    <b>Комментарий</b>:
                    <textarea class="ocomment" name="comment"></textarea>
                    <br>
                </span>
            </td>
            <td>
                <span class="plaintext">
                    <? if (isset($odetail->odetail_img)) : ?>
                    <a target="_blank" href="<?= $odetail->odetail_img ?>"><?=
                        (strlen($odetail->odetail_img) > 17 ?
                            substr($odetail->odetail_img, 0, 17) . '...' :
                            $odetail->odetail_img) ?></a>
                    <? else : ?>
                    <a href="javascript:void(0)" onclick="setRel(<?= $odetail->odetail_id ?>);">
                        <img src='/client/showScreen/<?= $odetail->odetail_id ?>' width="55px" height="55px">
                        <a rel="lightbox_<?= $odetail->odetail_id ?>" href="/client/showScreen/<?=
                            $odetail->odetail_id ?>" style="display:none;">Посмотреть</a>
                    </a>
                    <? endif; ?>
                </span>

                <span class="producteditor" style="display: none;">
                    <input type="radio" name="img_selector" class="img_selector" value="link">
                    <textarea class="image" name="img"></textarea>
                    <br>
                    <input type="radio" name="img_selector" class="img_selector" value="file">
                    <input type="file" class="img_file" name="userfile">
                </span>
            </td>

            </form>
            <td>
                <input type="text"
                   order-id="<?= $order->order_id ?>"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   id="odetail_price<?= $odetail->odetail_id ?>"
                   class="odetail_price int"
                   name="odetail_price<?= $odetail->odetail_id ?>"
                   value="<?= $odetail->odetail_price ?>"
                   style="width:60px"
                   maxlength="11">
            </td>
            <td>
                <input type="text"
                   order-id="<?= $order->order_id ?>"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   id="odetail_pricedelivery<?= $odetail->odetail_id ?>"
                   class="odetail_pricedelivery int"
                   name="odetail_pricedelivery<?= $odetail->odetail_id ?>"
                   value="<?= $odetail->odetail_pricedelivery ?>"
                   style="width:60px"
                   maxlength="11">
            </td>
            <td>

                <input type="text"
                   order-id="<?= $order->order_id ?>"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   id="odetail_weight<?= $odetail->odetail_id ?>"
                   class="odetail_weight int"
                   name="odetail_weight<?= $odetail->odetail_id ?>"
                   value="<?= $odetail->odetail_weight ?>"
                   style="width:60px"
                   maxlength="11">

            </td>
            <td>
                <a href="#"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="edit">
                    <img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
                <br>
                <a href="#"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="delete">
                    <img border="0" src="/static/images/delete.png" title="Удалить"></a>
                <br>
                <a href="#"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="cancel"
                   style="display: none;">
                    <img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>
                <br>
                <a href="#"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="save"
                   style="display: none;">
                    <img border="0" src="/static/images/done-filed.png" title="Сохранить"></a>
            </td>
        </tr>
        <? endforeach; endif; ?>

        <tr>
            <td colspan="3">&nbsp;</td>
            <td class="price_total product_total">
                <b class="total_product_cost"><?= ($order) ? $order->order_products_cost : '' ?></b>&nbsp;<?=
                ($order) ? $order->order_currency : '' ?>
            </td>
            <td class="delivery_total product_total">
                <b class="total_delivery_cost"><?= ($order) ? $order->order_delivery_cost : '' ?></b>&nbsp;<?= ($order) ? $order->order_currency : '' ?>
            </td>
            <td class="weight_total">
                <b class="total_weight"><?= ($order) ? $order->order_weight : '' ?></b> г
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr class='last-row'>
            <td colspan='2' style="border: none;">
                <div class='floatleft'>
                    <div class='submit'><div><input type='submit' value='Объединить доставку' /></div></div>
                </div>
                <img class="tooltip_join" src="/static/images/mini_help.gif" />
            </td>
            <td style="text-align: right; border: none;" colspan='5'>
                <br />
                <b>
                    Итого: <b class="order_totals"></b>
                    <br />
                    Доставка в <span class='countryTo' style="float:none; display:inline; margin:0;"></span><span class='cityTo' style="float:none; display:inline; margin:0;"></span>: <b class="weight_total"></b>
                </b>
            </td>
        </tr>

    </table>
</div>


<div style="height: 50px; <?= ((empty($this->user->user_group) OR !($order AND count($order->details))) ? 'display:none;' : '')?>" class="admin-inside checkOutOrderBlock">
    <div class="submit">
        <div>
            <input type="button" value="Готово" id="checkoutOrder" name="checkout" onclick="/*checkout();*/">
        </div>
    </div>
</div>

<? if (empty($this->user->user_group)) : ?>
<br><br>
<? View::show('main/elements/auth/new_order'); ?>
<? endif; ?>
    
<script>


	function cancelItem(id) {
		if ($('#odetail_product_name' + id + ' textarea').length)
		{
			var odetail = eval('odetail' + id);
			
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
		if ($('#odetail_product_name' + id + ' textarea').length)
		{
			$('#odetail_product_name' + id).parent().find('input,textarea').attr('readonly', true);
			$('#odetail_action' + id).html('<img border="0" src="/static/images/lightbox-ico-loading.gif" title="Товар сохраняется..."><br><a href="javascript:cancelItem(' + id + ')" id="odetail_cancel' + id + '"><img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>');
			$('#odetail_id').val(id);
			$('#detailsForm').submit();						
		}
	}
	
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}

    function getSelectedCurrency()
    {
        return selectedCurrency;
    }
</script>



<!--
<form class='admin-inside' id='detailsForm' action='<?=$selfurl?>updateProductAjax' enctype="multipart/form-data" method="POST" style="display:none;">
	<input name="order_id" type="hidden" value=""/>
	<input id="odetail_id" name="odetail_id" type="hidden" value=""/>
	<h3>Ваш заказ:</h3>
	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<table id="new_products">
			<tr>
				<th nowrap style="width:1px;">
					№ <input type='checkbox' id='select_all' />
				</th>
				<th>Товар</th>
				<th>Скриншот</th>
				<th>Стоимость</th>
				<th>Местная доставка</th>
				<th>Примерный вес</th>
				<th style="width:1px;"></th>
			</tr>
			<? $order_products_cost = 0;
$order_delivery_cost = 0;
$order_product_weight = 0;
$odetail_joint_id = 0;
$odetail_joint_count = 0;

if ( ! empty($odetails)) : foreach($odetails as $odetail) :
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
				<td id="odetail_price<?=$odetail->odetail_id?>"><?=$odetail->odetail_price?></td>
				<? if (!$odetail->odetail_joint_id) :
    $order_delivery_cost += $odetail->odetail_pricedelivery;
    ?>
				<td id="odetail_pricedelivery<?=$odetail->odetail_id?>">
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
				<td align="center" id="odetail_action<?=$odetail->odetail_id?>">
					<a href="javascript:editItem(<?=$odetail->odetail_id?>)" id="odetail_edit<?=$odetail->odetail_id?>"><img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
					<br />
					<a href="javascript:deleteItem(<?=$odetail->odetail_id?>)"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
				</td>
			</tr>
			<? endforeach; endif; ?>
			<tr>
				<td colspan="3">&nbsp;</td>
				<td class="price_total product_total"><?= $order_products_cost ?></td>
				<td class="delivery_total product_total"><?= $order_delivery_cost ?></td>
				<td class="weight_total"><?= $order_product_weight ?></td>
				<td align="center">&nbsp;</td>
			</tr>
			<tr class='last-row'>
				<td colspan='4'>
					<div class='floatleft'>
						<div class='submit'><div><input type='submit' value='Объединить доставку' /></div></div>
					</div>
					<img class="tooltip_join" src="/static/images/mini_help.gif" />
				</td>
				<td style="text-align: right;" colspan='3'>
					<br />
					<b>
						Итого: <b class="order_totals"></b>
						<br />
						Доставка в <span class='countryTo' style="float:none; display:inline; margin:0;"></span><span class='cityTo' style="float:none; display:inline; margin:0;"></span>: <b class="weight_total"></b>
					</b>
				</td>
			</tr>
		</table>
	</div>
	<div style="height: 50px; <?= (empty($this->user->user_group) ? 'display:none;' : '')?>" class="admin-inside checkOutOrderBlock">
		<div class="submit">
			<div>
				<input type="button" value="Готово" id="checkoutOrder" name="checkout" onclick="/*checkout();*/">
			</div>
		</div>
	</div>
</form>
-->