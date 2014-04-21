<span class="plaintext">
	<a target="_blank" href="<?= $odetail->odetail_link ?>"><?= $odetail->odetail_product_name ?></a>
	<br>
	<b>Количество</b>: <?= $odetail->odetail_product_amount ?>
	<b>Размер</b>: <?= $odetail->odetail_product_size ?>
	<b>Цвет</b>: <?= $odetail->odetail_product_color ?>
	<? if ($odetail->odetail_foto_requested) : ?>
	<br><b>Фото полученного товара:</b> сделать фото
	<? endif; ?>
	<br>
	<b>Комментарий</b>: <?= $odetail->odetail_comment ?>
</span>
<? if ($is_editable) : ?>
<script>
	var odetail<?= $odetail->odetail_id ?> = {
		"link":"<?= $odetail->odetail_link ?>",
		"name":"<?= $odetail->odetail_product_name ?>",
		"color":"<?= $odetail->odetail_product_color ?>",
		"size":"<?= $odetail->odetail_product_size ?>",
		"amount":"<?= $odetail->odetail_product_amount ?>",
		"comment":"<?= $odetail->odetail_comment ?>",
		"img":"<?= $odetail->odetail_img ?>",
		"img_file":"",
		"img_selector":"<?= isset($odetail->odetail_img) ? 'link' : 'file' ?>",
		"foto_requested":"<?= $odetail->odetail_foto_requested ?>",
		"is_editing":0
	};

	$(function() {
		$('tr#product<?= $odetail->odetail_id ?> form').ajaxForm({
			dataType: 'json',
			iframe: true,
			beforeSubmit: function()
			{
				$('img#progress<?= $odetail->odetail_id ?>').show();
			},
			error: function()
			{
				error('top', 'Описание товара №<?= $odetail->odetail_id ?> не сохранено.');
			},
			success: function(data) {
				$('img#progress<?= $odetail->odetail_id ?>').hide();

				submitItem(<?= $odetail->odetail_id ?>, data);
			}
		});
	});
</script>
<span class="producteditor" style="display: none;">
	<br>
	<b>Ссылка:</b>
	<textarea class="link" name="link"></textarea>
	<br>
	<b>Наименование:</b>
	<textarea class="name" name="name"></textarea>
	<br>
	<b>Количество:</b>
	<textarea class="amount int" name="amount"></textarea>
	<br>
	<b>Размер:</b>
	<textarea class="size" name="size"></textarea>
	<br>
	<b>Цвет:</b>
	<textarea class="color" name="color"></textarea>
	<br>
	<b>Комментарий:</b>
	<textarea class="ocomment" name="comment"></textarea>
	<br>
	<b>Требуется фото</b>
	<input type="checkbox" class="foto_requested" name="foto_requested">
	<br>
</span>
<? endif; ?>