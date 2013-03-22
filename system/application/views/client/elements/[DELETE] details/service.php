<span class="plaintext">
	<b><?= $odetail->odetail_product_name ?></b>
	<br>
	<b>Описание</b>: <?= $odetail->odetail_comment ?>
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
	<textarea class="link" name="link" style="display: none;"></textarea>
	<br>
	<b>Наименование</b>:
	<textarea class="name" name="name"></textarea>
	<br>
	<b>Описание</b>:
	<textarea class="ocomment" name="comment"></textarea>
	<br>
</span>
<? endif; ?>