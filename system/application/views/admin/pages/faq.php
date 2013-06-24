<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<a name='editFaq' />
	<h3>Редактирование F.A.Q.</h3>
	<div class="h2_link">
		<a href="javascript:showAddSectionForm();" style="text-align:right;">Добавить группу</a>
		<a href="javascript:showAddForm();" style="text-align:right;margin-right:5px;">Добавить вопрос</a>
	</div>
	<div id="add" align="center" style="display: none;">
		<br />
		<form class='admin-inside' name="addForm" id="addForm" method="POST" action="<?= $this->config->item('base_url') ?>admin/saveFaq">
			<div class='table'>
				<div class='angle angle-lt'></div>
				<div class='angle angle-rt'></div>
				<div class='angle angle-lb'></div>
				<div class='angle angle-rb'></div>
				<table>
					<tr>
						<td><span>Вопрос:</span></td>
						<td>
							<textarea name="question" cols="155" rows="2"></textarea>
						</td>
					</tr>
					<tr>
						<td><span>Группа:</span></td>
						<td>
							<select name="section" id="section">
								<? foreach ($faq_sections as $section) : ?>
								<option value='<?= $section->faq_section_id ?>'><?= $section->faq_section_name ?></option>
								<? endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><span>Ответ:</span></td>
						<td>
							<textarea id="answer" name="answer" cols="155" rows="3"></textarea>
							<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
						</td>
					</tr>
					<tr class='last-row'>
						<td colspan='9'>
							<div class='float'>	
								<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
							</div>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
			<input type="hidden" name="id" />
		</form>
	</div>
	<div id="addSection" align="center" style="display: none;">
		<br />
		<form class='admin-inside' name="addSectionForm" id="addSectionForm" method="POST" action="<?= $this->config->item('base_url') ?>admin/addFaqSection">
			<div class='table'>
				<div class='angle angle-lt'></div>
				<div class='angle angle-rt'></div>
				<div class='angle angle-lb'></div>
				<div class='angle angle-rb'></div>
				<table>
					<tr>
						<td><span>Название группы:</span></td>
						<td>
							<input name="faq_section_name" maxlength='255'/>
						</td>
					</tr>
					<tr class='last-row'>
						<td colspan='9'>
							<div class='float'>	
								<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
							</div>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		</form>
	</div>
	<br />
	<form class='admin-inside' method="POST">
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='30' />
				<col width='auto' />
				<col width='30' />
				<tr>
					<th>№</th>
					<th>Вопрос / Ответ</th>
					<th>Изменить / Удалить</th>
				</tr>
				<?if (count($faq)):foreach($faq as $item): ?>
				<tr>
					<input id='s_<?= $item->faq_id ?>' value='<?= $item->faq_section_id ?>' type='hidden' />
					<td><?= $item->faq_id ?></td>
					<td>
						Вопрос: <span><b id="q_<?= $item->faq_id ?>" style="font-size:1.1em;"><?= $item->faq_question ?></b></span>
						<br />
						Ответ: <span id="a_<?= $item->faq_id ?>"><?= html_entity_decode($item->faq_answer) ?></span>
					</td>
					<td>
						<a href="#editFaq" onclick="editFaq(<?= $item->faq_id ?>);">Изменить</a>
						<a href="javascript:deleteItem(<?= $item->faq_id ?>);"><img border="0" src="/static/images/delete.png" title="Удалить"></a>
					</td>
				</tr>
				<?endforeach;endif; ?>
			</table>
		</div>
	</form>
</div>
<script type="text/javascript">
<? echo editor('answer', 200) ?>
	function showAddForm(){
		var f = document.forms['addForm'];
		
		if (f.id.value)
		{
			f.question.innerHTML	= '';
			f.answer.innerHTML		= '';
			f.id.value				= '';
			$('#addForm select#section').val('');

			var oEditor = FCKeditorAPI.GetInstance('answer') ;
			oEditor.SetData('');
		}
		else
		{
			$('#add').toggle();
		}
		
		$('#addSection').hide();
	}
	
	function showAddSectionForm()
	{
		$('#addSection').toggle();
		$('#add').hide();
	}
	
	function editFaq(id)
	{
		var f = document.forms['addForm'];
		var q = $('#q_'+id).html();
		var a = $('#a_'+id).html();
		var s = $('#s_'+id).val();
		f.question.innerHTML	= q;
		f.answer.innerHTML		= a;
		$('#addForm select#section').val(s);
		
		var oEditor = FCKeditorAPI.GetInstance('answer') ;
		oEditor.SetData(a);
		f.id.value = id;
		$('#add').show();
		$('#addSection').hide();
	}
	
	function deleteItem(id)
	{
		if (confirm("Вы уверены что хотите удалить запись?"))
		{
			window.location.href = '<?= $selfurl ?>deleteFaq/'+id;
		}
	}
</script>