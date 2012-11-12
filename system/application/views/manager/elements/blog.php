<div class="blog dealer_tab" style="display:none;">
	<form action="/manager/saveBlog" id="blogForm" method="POST">
		<div class="table" style="height: 303px;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div class="blog_box admin-inside">
				<input type="hidden" name="blog_id" />
				<div>
					<span class="label">Заголовок*:</span>
				</div>
				<br style="clear:both;" />
				<div>
					<input style="width:910px;" class="textbox" maxlength="255" type='text' id='title' name="title" />
				</div>
				<br style="clear:both;" />
				<div>
					<span class="label">Текст новости*:</span>
				</div>
				<br style="clear:both;" />
				<div>
					<textarea maxlength="65535" id='message' name="message"></textarea>
				</div>
			</div>
		</div>
		<br style="clear:both;" />
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Сохранить">
			</div>
		</div>
		<img class="float" id="blogProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>
	</form>
	<br style="clear:both;" />
	<h3 id="news_header">Все новости</h3>
	<? if ($blogs) : foreach ($blogs as $blog) : ?>
	<div class="table">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div>
			<span class="label">
				<?= isset($blog->created) ? date('d.m.Y H:i', strtotime($blog->created)) : '' ?>
			</span>
			<span class="label">
				<b><?= $blog->title ?></b>
			</span>
		</div>
		<div>
			<?= html_entity_decode($blog->message) ?>
		</div>
	</div>
	<br>
	<br>
	<? endforeach; endif; ?>
</div>
<script>
	$(function() {
		$('#blogForm').ajaxForm({
			target: '/manager/saveBlog',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#blogProgress").show();
			},
			success: function(response)
			{
				$("#blogProgress").hide();
				success('top', 'Новость успешно сохранена!');

				var oEditor = FCKeditorAPI.GetInstance('message');
				var message = oEditor.GetHTML(true);
				
				var news_snippet = '<div class="table"><div class="angle angle-lt"></div><div class="angle angle-rt"></div><div class="angle angle-lb"></div><div class="angle angle-rb"></div><div><span class="label">' +
				getNowDate() +
				'</span> <span class="label"><b>' +
				$('.blog_box input#title').val() +
				'</b></span></div><div>' +
				message +
				'</div></div><br><br>';
				
				$('#news_header').after(news_snippet);
				
				$('.blog_box input#title').val('');
				oEditor.SetHTML('');
			},
			error: function(response)
			{
				$("#blogProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('message', 200, 920, 'PackageComment') ?>
</script>