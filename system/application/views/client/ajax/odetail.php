<tr id='product<?= $odetail->odetail_id ?>'>
	<td id='odetail_id<?= $odetail->odetail_id ?>'>
		<?= $odetail->odetail_id ?>
		<? if ($is_editable AND
			! $order->is_creating) : ?>
			<br>
		<input type='checkbox'
			   class='item_check'
				   name='join<?= $odetail->odetail_id ?>'>
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
		<? if (isset($odetail->odetail_img) AND
			$odetail->odetail_img) : ?>
			<a target="_blank" href="<?= $odetail->odetail_img ?>"><?=
				(strlen($odetail->odetail_img) > 17 ?
					substr($odetail->odetail_img, 0, 17) . '...' :
					$odetail->odetail_img) ?></a>
		<? elseif ( ! isset($odetail->odetail_img)) : ?>
			<a href="javascript:void(0)" onclick="setRel(<?= $odetail->odetail_id ?>);">
				<img src='/client/showScreen/<?= $odetail->odetail_id ?>' height="55px">
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
	<? elseif ($odetail_joint_id != $odetail->odetail_joint_id) : ?>
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
		<?= $odetail->odetail_weight ?> кг
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
	<td class="edit_box">
		<a href="javascript:editItem(<?= $odetail->odetail_id ?>)"
		   class="edit">
			<img border="0" src="/static/images/comment-edit.png" title="Редактировать"></a>
		<a href="javascript:deleteProduct(<?= $odetail->odetail_id ?>)"
		   class="delete"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
		<a href="javascript:cancelItem(<?= $odetail->odetail_id ?>)"
		   class="cancel"
		   style="display: none;">
			<img border="0" src="/static/images/comment-delete.png" title="Отменить"></a>
		<a href="javascript:saveItem(<?= $odetail->odetail_id ?>)"
		   class="save"
		   style="display: none;">
			<img border="0" src="/static/images/done-filed.png" title="Сохранить"></a>
	</td>
	<? endif; ?>
</tr>