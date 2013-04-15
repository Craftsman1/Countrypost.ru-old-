<?
$is_editable = in_array($order->order_status, $editable_statuses);
$is_joinable = ($is_editable AND in_array($order->order_type, $joinable_types));
?>
<div class='table centered_td centered_th'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<table id="new_products" class="products">
		<colgroup>
			<col style="width: 60px;">
			<col>
			<col>
			<col style="width: 85px;">
			<col style="width: 85px;">
			<col style="width: 85px;">
			<? if ($order->order_status != 'pending') : ?>
			<col style="width: 169px;">
			<? endif; ?>
			<col style="width: 44px">
		</colgroup>
		<tr>
			<th nowrap>
				№
				<? if ($is_joinable) : ?>
				<input type='checkbox' id='select_all'>
				<? endif; ?>
			</th>
			<th>Товар</th>
			<th>Скриншот</th>
			<? if ($order->order_type == 'mail_forwarding') : ?>
			<th>Tracking №</th>
			<? else : ?>
			<th>
				Стоимость
			</th>
			<th>
				Местная<br>доставка
			</th>
			<? if ($order->order_type != 'service') : ?>
			<th>
				Вес<br>товара
			</th>
			<? endif; ?>
			<? endif; ?>
			<? if ($order->order_status != 'pending') : ?>
			<th>Статус</th>
			<? endif; ?>
			<? if ($is_editable) : ?>
			<th style="width:1px;"></th>
			<? endif; ?>
		</tr>
		<?
		$odetail_joint_id = 0;
		$odetail_joint_count = 0;

		if ( ! empty($odetails)) : foreach($odetails as $odetail) : ?>
		<tr id='product<?= $odetail->odetail_id ?>'>
			<td id='odetail_id<?= $odetail->odetail_id ?>'>
				<?= $odetail->odetail_id ?>
				<? if ($is_joinable) : ?>
				<br>
				<input type='checkbox' name='join<?= $odetail->odetail_id ?>'>
				<? endif; ?>
				<? if ($is_editable) : ?>
				<br>
				<img id="progress<?= $odetail->odetail_id ?>"
					 class="float <? if ($odetail->odetail_joint_id) : ?>progressJoint<?= $odetail->odetail_joint_id
					?><? endif; ?>"
					 style="display:none;"
					 src="/static/images/lightbox-ico-loading.gif"/>
				<? endif; ?>
			</td>
			<? if ($is_editable) : ?>
			<form action='<?= $selfurl ?>updateProduct/<?= $order->order_id ?>/<?= $odetail->odetail_id ?>'
				  enctype="multipart/form-data"
				  method="POST">
			<? endif; ?>
			<td style="text-align: left; vertical-align: middle;">
				<? View::show("main/elements/details/{$order->order_type}", array(
					'odetail' => $odetail,
					'is_editable' => $is_editable)); ?>
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
				<? if ($is_editable) : ?>
				<span class="producteditor" style="display: none;">
					<input type="radio" name="img_selector" class="img_selector" value="link">
					<textarea class="image" name="img"></textarea>
					<br>
					<input type="radio" name="img_selector" class="img_selector" value="file">
					<input type="file" class="img_file" name="userfile">
				</span>
				<? endif; ?>
			</td>
			<? if ($is_editable) : ?>
			</form>
			<? endif; ?>
			<? if ($order->order_type == 'mail_forwarding') : ?>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_tracking<?= $odetail->odetail_id ?>"
					   name="odetail_tracking<?= $odetail->odetail_id ?>"
					   value="<?= $odetail->odetail_tracking ?>"
					   style="width:60px"
					   maxlength="80"
					   onchange="update_odetail_tracking('<?= $order->order_id ?>',
							   '<?= $odetail->odetail_id ?>');">
				<? else : ?>
				<?= $odetail->odetail_tracking ?>
				<? endif; ?>
			</td>
			<? else : ?>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_price<?= $odetail->odetail_id ?>"
					   name="odetail_price<?= $odetail->odetail_id ?>"
					   class="int"
					   value="<?= $odetail->odetail_price ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_odetail_price('<?= $order->order_id ?>',
							   '<?= $odetail->odetail_id ?>');">
				<? else : ?>
				<?= $odetail->odetail_price ?> <?= $order->order_currency ?>
				<? endif; ?>
			</td>
			<? if ( ! $odetail->odetail_joint_id) : ?>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_pricedelivery<?= $odetail->odetail_id ?>"
					   name="odetail_price<?= $odetail->odetail_id ?>"
					   class="int"
					   value="<?= $odetail->odetail_pricedelivery ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_odetail_pricedelivery('<?= $order->order_id ?>',
							   '<?= $odetail->odetail_id ?>');">
				<? else : ?>
				<?= $odetail->odetail_pricedelivery ?> <?= $order->order_currency ?>
				<? endif; ?>
			</td>
			<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) :
				$odetail_joint_id = $odetail->odetail_joint_id; ?>
			<td rowspan="<?= $joints[$odetail->odetail_joint_id]->count ?>">
				<? if ($is_editable) : ?>
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
				<? else : ?>
				<?= $joints[$odetail->odetail_joint_id]->cost ?> <?= $order->order_currency ?>
				<? endif; ?>
			</td>
			<? endif; ?>
			<? if ($order->order_type != 'service') : ?>
			<td>
				<? if ($is_editable) : ?>
				<input type="text"
					   id="odetail_weight<?= $odetail->odetail_id ?>"
					   name="odetail_weight<?= $odetail->odetail_id ?>"
					   class="int"
					   value="<?= $odetail->odetail_weight ?>"
					   style="width:60px"
					   maxlength="11"
					   onchange="update_odetail_weight('<?= $order->order_id ?>', '<?= $odetail->odetail_id ?>')
							   ;">
				<? else : ?>
				<?= $odetail->odetail_weight ?> г
				<? endif; ?>
			</td>
			<? endif; ?>
			<? endif; ?>
			<? if ($order->order_status != 'pending') : ?>
			<td>
				<?= $odetail_statuses[$order->order_type][$odetail->odetail_status] ?>
			</td>
			<? endif; ?>
			<? if ($is_editable) : ?>
			<td>
				<a href="javascript:editItem(<?= $odetail->odetail_id ?>)"
				   class="edit">
					<img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
				<br class="cancel" style="display: none;">
				<a href="javascript:cancelItem(<?= $odetail->odetail_id ?>)"
				   class="cancel"
				   style="display: none;">
					<img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>
				<br class="save" style="display: none;">
				<a href="javascript:saveItem(<?= $odetail->odetail_id ?>)"
				   class="save"
				   style="display: none;">
					<img border="0" src="/static/images/done-filed.png" title="Сохранить"></a>
			</td>
			<? endif; ?>
		</tr>
		<? endforeach; endif; ?>
		<? if ($order->order_type != 'mail_forwarding') : ?>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td class="price_total product_total">
				<b class="total_product_cost"><?= $order->order_products_cost ?></b>&nbsp;<?=
				$order->order_currency ?>
			</td>
			<td class="delivery_total product_total">
				<b class="total_delivery_cost"><?= $order->order_delivery_cost ?></b>&nbsp;<?= $order->order_currency ?>
			</td>
			<? if ($order->order_type != 'service') : ?>
			<td class="weight_total">
				<b class="total_weight"><?= $order->order_weight ?></b> г
			</td>
			<? endif; ?>
			<td <? if ($is_editable AND $order->order_type != 'service') : ?>colspan="2"<? endif; ?>>&nbsp;</td>
		</tr>
		<? endif; ?>
		<? if ($is_editable) : ?>
		<tr class="last-row">
			<td colspan="6">
					<div class="admin-inside float-left">
						<div class="submit">
							<div>
								<input type="button" value="Добавить товар" onclick="addItem(<?= $order->order_id ?>);">
							</div>
						</div>
					</div>

			</td>
		</tr>
		<? endif ?>
	</table>
</div>
<? if ($is_joinable) : ?>
<div style="height:50px;">
	<div class="admin-inside float-left">
		<div class="submit">
			<div>
				<input type="button" value="Объединить доставку" onclick="joinProducts();">
			</div>
		</div>
	</div>
	<img class="float"
		 id="joinProgress"
		 style="display:none;margin:0px;margin-top:5px;"
		 src="/static/images/lightbox-ico-loading.gif">
</div>
<? endif ?>