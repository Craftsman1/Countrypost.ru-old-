<span class="plaintext">
	<? if (empty($odetail->odetail_link)) : ?>
	<b><?= $odetail->odetail_product_name ?></b>
	<? else : ?>
	<a target="_blank" href="<?= $odetail->odetail_link ?>"><?= $odetail->odetail_product_name ?></a>
	<? endif; ?>
	<br>
	<b>Количество</b>: <?= $odetail->odetail_product_amount ?>
	<b>Объём</b>: <?= $odetail->odetail_volume ?>
	<b>ТН ВЭД</b>: <?= $odetail->odetail_tnved ?>
	<? if ($odetail->odetail_insurance) : ?>
	<br><b>Страховка:</b> сделать страховку
	<? endif; ?>
	<br>
	<b>Комментарий</b>: <?= $odetail->odetail_comment ?>
</span>
<? if ($is_editable) : ?>
<script>
	var odetail<?= $odetail->odetail_id ?> = {
		"link":"<?= $odetail->odetail_link ?>",
		"name":"<?= $odetail->odetail_product_name ?>",
		"volume":"<?= $odetail->odetail_volume ?>",
		"tnved":"<?= $odetail->odetail_tnved ?>",
		"amount":"<?= $odetail->odetail_product_amount ?>",
		"comment":"<?= $odetail->odetail_comment ?>",
		"img":"<?= $odetail->odetail_img ?>",
		"img_file":"",
		"img_selector":"<?= isset($odetail->odetail_img) ? 'link' : 'file' ?>",
		"insurance":"<?= $odetail->odetail_insurance ?>",
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
	<b>Ссылка</b>:
	<textarea class="link" name="link"></textarea>
	<br>
	<b>Наименование</b>:
	<textarea class="name" name="name"></textarea>
	<br>
	<b>Количество</b>:
	<textarea class="amount int" name="amount"></textarea>
	<br>
	<b>Объём</b>:
	<textarea class="volume" name="volume"></textarea>
	<br>
	<b>ТН ВЭД</b>:
	<textarea class="tnved" name="tnved"></textarea>
	<br>
	<b>Комментарий</b>:
	<textarea class="ocomment" name="comment"></textarea>
	<br>
	<b>Требуется страховка</b>:
	<input type="checkbox" class="insurance" name="insurance">
	<br>
</span>
<? endif; ?>