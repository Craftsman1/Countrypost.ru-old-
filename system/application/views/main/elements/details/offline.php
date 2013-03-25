<span class="plaintext">
	<b><?= $odetail->odetail_shop ?></b>
	<br>
	<b><?= $odetail->odetail_product_name ?></b>
	<br>
	<b>Количество</b>: <?= $odetail->odetail_product_amount ?>
	<b>Размер</b>: <?= $odetail->odetail_product_size ?>
	<b>Цвет</b>: <?= $odetail->odetail_product_color ?>
	<? if ($odetail->odetail_foto_requested) : ?>
	<br><b>Фото полученного товара:</b> сделать фото
	<? endif; ?>
	<? if ($odetail->odetail_search_requested) : ?>
	<br><b>Поиск товара:</b> требуется поиск
	<? endif; ?>
	<br>
	<b>Комментарий</b>: <?= $odetail->odetail_comment ?>
</span>
<? if ($is_editable) : ?>
<script>
	var odetail<?= $odetail->odetail_id ?> = {
		"link":"<?= $odetail->odetail_link ?>",
		"shop":"<?= $odetail->odetail_shop ?>",
		"name":"<?= $odetail->odetail_product_name ?>",
		"color":"<?= $odetail->odetail_product_color ?>",
		"size":"<?= $odetail->odetail_product_size ?>",
		"amount":"<?= $odetail->odetail_product_amount ?>",
		"comment":"<?= $odetail->odetail_comment ?>",
		"img":"<?= $odetail->odetail_img ?>",
		"img_file":"",
		"img_selector":"<?= isset($odetail->odetail_img) ? 'link' : 'file' ?>",
		"foto_requested":"<?= $odetail->odetail_foto_requested ?>",
		"search_requested":"<?= $odetail->odetail_search_requested ?>",
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
	<input type="hidden" name="link" class="link" value="<?= BASEURL ?>" />
	<br>
	<b>Магазин:</b>
	<textarea class="shop" name="shop"></textarea>
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
	<b>Требуется поиск товара</b>
	<input type="checkbox" class="search_requested" name="search_requested">
	<br>
</span>
<? endif; ?>