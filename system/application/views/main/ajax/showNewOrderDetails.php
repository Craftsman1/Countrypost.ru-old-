<h3 class="cart_header"
	style="<?= ($order AND count($order->details)) ? '' : 'display:none;'?>">Товары в корзине:</h3>
<div class='table centered_td centered_th' style="<?= ($order AND count($order->details)) ? '' : 'display:none;'?>">
    <div class='angle angle-lt'></div>
    <div class='angle angle-rt'></div>
    <div class='angle angle-lb'></div>
    <div class='angle angle-rb'></div>
    <table id="new_products">
        <colgroup>
            <col style="width: 60px;">
            <col>
            <? if ($order_type != 'delivery') : ?>
			<col style="width: 206px;">
            <? endif; ?>
			<? if ($order_type == 'mail_forwarding') : ?>
            <col style="width: 185px;">
            <? else : ?>
			<col style="width: 85px;">
			<col style="width: 85px;">
			<? if ($order_type != 'service') : ?>
			<col style="width: 85px;">
			<? endif; ?>
            <? endif; ?>
            <col style="width: 44px">
        </colgroup>
        <tr>
            <th nowrap>
                № <input type='checkbox' id='select_all'>
            </th>
            <th>Товар</th>
            <? if ($order_type != 'delivery') : ?>
            <th style="width: 206px;">Скриншот</th>
            <? endif; ?>
            <? if ($order_type == 'mail_forwarding') : ?>
			<th>
				Tracking номер
			</th>
            <? else : ?>
			<th>
				Стоимость
			</th>
			<th>
				Местная<br>доставка
			</th>

			<? if ($order_type != 'service') : ?>
			<th>
				Вес<br>товара
			</th>
			<? endif; ?>
            <? endif; ?>
            <th style="width:1px;"></th>
        </tr>
        <?
        $odetail_joint_id = 0;
        $odetail_joint_count = 0;

        if ($order)
        {
            $odetails = $order->details;
        }

        if ( ! empty($odetails)) :
			foreach($odetails as $odetail) :
				if (stripos($odetail->odetail_link, 'http://') !== 0 AND
					! empty($odetail->odetail_link))
				{
					$odetail->odetail_link = 'http://'.$odetail->odetail_link;
				}

				// генерируем выдачу изображения: если 0 - не указано ничего, NULL - загружен файл, VALUE - ссылка на принтскрин
				if (isset($odetail->odetail_img) && $odetail->odetail_img=='0')
				{
					$oimg = '';
				}
				elseif (!isset($odetail->odetail_img) || $odetail->odetail_img===NULL)
				{
					$oimg = '<a href="javascript:void(0)" onclick="setRel('.$odetail->odetail_id.');">
								<img src="/client/showScreen/'.$odetail->odetail_id.'" width="55px" height="55px">
								<a rel="lightbox_'.$odetail->odetail_id.'" href="/client/showScreen/'.$odetail->odetail_id.'" style="display:none;">Посмотреть</a>
							</a>';
				}
				elseif (isset($odetail->odetail_img) && $odetail->odetail_img !='0')
				{
					$img_src = $odetail->odetail_img;
					if (stripos($img_src, 'http://') !== 0)
					{
						$img_src = 'http://'.$img_src;
					}

					$oimg = '<a target="_blank" href="'.$img_src.'">'.
								(strlen($img_src) > 17 ?
									substr($img_src, 0, 17).'...' :
									$img_src).'</a>';
				}
        ?>
        <tr id='product<?= $odetail->odetail_id ?>'>
            <td id='odetail_id<?= $odetail->odetail_id ?>'>
                <form style="display: none;"
					  id="odetail<?= $order_type ?><?= $odetail->odetail_id ?>"
					  action="<?= $selfurl ?>updateNewProduct/<?= $order->order_id ?>/<?= $odetail->odetail_id ?>"
                      enctype="multipart/form-data"
                      method="POST">
                </form>
                <input type="checkbox" name="odetail_id" value="<?= $odetail->odetail_id ?>"/>
                <br>
                <?= $odetail->odetail_id ?>
                <br>
                <img id="progress<?= $odetail->odetail_id ?>"
                     class="float"
                     style="display:none;"
                     src="/static/images/lightbox-ico-loading.gif"/>
            </td>
			<td style="text-align: left; vertical-align: middle;">
				<? View::show("main/elements/details/{$order->order_type}", array(
				'odetail' => $odetail,
				'is_editable' => TRUE)); ?>
			</td>
			<? if ($order_type != 'delivery') : ?>
			<td style="width: 206px;">
				<span class="plaintext">
					<?= $oimg ?>
				</span>
				<span class="producteditor" style="display: none; width: 206px;">
					<input value="link" class="img_selector" name="img_selector" type="radio">
					<textarea name="img" class="image"></textarea>
					<br>
					<input value="file" class="img_selector" name="img_selector" type="radio">
					<input name="userfile" class="img_file" type="file">
				</span>
			</td>
			<? endif; ?>
			<? if ($order_type == 'mail_forwarding') : ?>
			<td>
				<span class="plaintext">
					<?= $odetail->odetail_tracking ?>
				</span>
				<span class="producteditor" style="display: none;">
					<input type="text"
						   order-id="<?= $order->order_id ?>"
						   odetail-id="<?= $odetail->odetail_id ?>"
						   id="odetail_tracking<?= $odetail->odetail_id ?>"
						   class="odetail_tracking int"
						   name="odetail_tracking"
						   value="<?= $odetail->odetail_tracking ?>"
						   style="width:180px"
						   maxlength="80">
				</span>
			</td>
			<? else : ?>
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
			<? if ($odetail_joint_id != $odetail->odetail_joint_id AND isset($joints[$odetail->odetail_joint_id])) :
				$odetail_joint_id = $odetail->odetail_joint_id; ?>
			<td rowspan="<?= $joints[$odetail->odetail_joint_id]->count ?>">
				<input type="text"
					   id="joint_pricedelivery<?= $odetail->odetail_joint_id ?>"
					   name="joint_price<?= $odetail->odetail_joint_id ?>"
					   class="int"
					   value="<?= $joints[$odetail->odetail_joint_id]->cost ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_joint_pricedelivery('<?= $order->order_id ?>',
							   '<?= $odetail->odetail_joint_id ?>');">
				<br>
				<a href="javascript:removeJoint(<?= $odetail->odetail_joint_id ?>);">Отменить<br>объединение</a>
			</td>
			<? elseif ( ! $odetail->odetail_joint_id) : ?>
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
			<? endif; ?>
			<? if ($order_type != 'service') : ?>
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
			<? endif; ?>
            <? endif; ?>
            <td>
                <a href="javascript:void(0)"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="edit">
                    <img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
                <br>
                <a href="javascript:void(0)"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="delete">
                    <img border="0" src="/static/images/delete.png" title="Удалить"></a>
                <br>
                <a href="javascript:void(0)"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="cancel"
                   style="display: none;">
                    <img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>
                <br>
                <a href="javascript:void(0)"
                   odetail-id="<?= $odetail->odetail_id ?>"
                   class="save"
                   style="display: none;">
                    <img border="0" src="/static/images/done-filed.png" title="Сохранить"></a>
            </td>
        </tr>
        <? endforeach; endif; ?>
        <? if ($order_type != 'mail_forwarding') : ?>
		<tr>
			<? if ($order_type == 'delivery') : ?>
			<td colspan="2">&nbsp;</td>
			<? else : ?>
			<td colspan="3">&nbsp;</td>
			<? endif; ?>
			<td class="price_total product_total">
				<b class="total_product_cost"><?= ($order) ? $order->order_products_cost : '' ?></b>&nbsp;<?=
				($order) ? $order->order_currency : '' ?>
			</td>
			<td class="delivery_total product_total">
				<b class="total_delivery_cost"><?= ($order) ? $order->order_delivery_cost : '' ?></b>&nbsp;<?= ($order) ? $order->order_currency : '' ?>
			</td>
			<? if ($order_type != 'service') : ?>
			<td class="weight_total">
				<b class="total_weight"><?= ($order) ? $order->order_weight : '' ?></b> г
			</td>
			<? endif; ?>
			<td>&nbsp;</td>
		</tr>
        <? endif; ?>
        <tr class='last-row'>
			<td colspan='2'
				style="border: none;padding-left: 0;padding-bottom: 0;padding-top: 13px;">
				<div class="submit admin-inside checkOutOrderBlock float-left"
					 style="height: 50px;">
					<div>
						<input type="button"
							   value="Сформировать заказ"
							   id="<?= $order_type ?>checkoutOrder"
							   name="checkout">
					</div>
				</div>
				<? if ($order_type != 'mail_forwarding') : ?>
				<div class='floatleft'
					 style="margin-left: 10px; margin-top: 0;">
					<div class='submit'>
						<div>
							<input type='submit' class="joint_delivery_submit" value='Объединить доставку' />
						</div>
					</div>
					<img src="/static/images/lightbox-ico-loading.gif" style="display:none;" class="float" id="joint_progress">
				</div>
				<img class="tooltip_join"
					 style="float:left;margin-left: 10px;margin-top: 8px;"
					 src="/static/images/mini_help.gif" />
				<? endif; ?>
			</td>
			<td style="text-align: right; border: none;padding: 0;" colspan='5'>
				<br />
				<b>
					<? if ($order_type != 'mail_forwarding') : ?>
					Итого: <b class="order_totals"></b>
					<br />
					<? endif; ?>
					Доставка в <span class='countryTo' style="float:none; display:inline; margin:0;"></span><span class='cityTo' style="float:none; display:inline; margin:0;"></span><?
					if ($order_type != 'mail_forwarding' AND $order_type != 'service') : ?>: <b
							class="weight_total"></b><?endif; ?>
				</b>
			</td>
		</tr>
		<tr class='last-row' style="display: none">
			<td colspan='5' style="border: none;">&nbsp;</td>
		</tr>
    </table>
</div>
<script>
function getSelectedCurrency()
{
	return selectedCurrency;
}

function authValidation()
{
	if (window.user_group == undefined)
	{
		window.location = '#';
		success('top', 'Пожалуйста, войдите или зарегистрируйтесь для добавления нового заказа.');
		return false;
	}
	else
	{
		return true;
	}
}
</script>