<div class="client_ratings dealer_tab" style="display:none;">
	<form action="/manager/saveRating" id="ratingForm" method="POST">
		<div class="table" style="height: 303px;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div class="rating_box admin-inside">
				<input type="hidden" name="rating_id" />
				<div>
					<span class="label">
						<input type='radio' id='positive' name="rating_type" />
						<label for="positive" style="color: green;">Положительный</label>
					</span>
					<span class="label">
						<input type='radio' id='neutral' name="rating_type" checked />
						<label for="neutral">Нейтральный</label>
					</span>
					<span class="label">
						<input type='radio' id='negative' name="rating_type" />
						<label for="negative" style="color: red;">Отрицательный</label>
					</span>
				</div>
				<br style="clear:both;" />
				<div>
					<textarea maxlength="65535" id='rating_message' name="rating_message"></textarea>
				</div>
			</div>
			<div class="rating_group table">
				<div>
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 5,
					'description' => 'Консультация (ответы в скайпе, почте)'
				)); ?>
				</div>
				<div>
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 4,
					'description' => 'Выкуп товара'
				)); ?>
				</div>
				<div>
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 3,
					'description' => 'Консолидация и отправка посылок'
				)); ?>
				</div>
				<div style="height:auto;">
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 2,
					'description' => 'Упаковка посылок'
				)); ?>
				</div>
			</div>
		</div>
		<br style="clear:both;" />
		<div class="submit floatleft">
			<div>
				<input type="submit" value="Сохранить">
			</div>
		</div>
		<img class="float" id="ratingProgress" style="display:none;margin:0px;margin-top:4px;" src="/static/images/lightbox-ico-loading.gif"/>
	</form>
	<br style="clear:both;" />
	<h3 id="ratings_header">Все отзывы</h3>
	<? if (isset($ratings)) : foreach ($ratings as $rating) : ?>
	<div class="table">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div>
			<span class="label">
				<?= isset($rating->created) ? date('d.m.Y H:i', strtotime($rating->created)) : '' ?>
			</span>
			<span class="label">
				<b><?= $rating->title ?></b>
			</span>
		</div>
		<div>
			<?= html_entity_decode($rating->message) ?>
		</div>
	</div>
	<br>
	<br>
	<? endforeach; endif; ?>
</div>
<script>
	$(function() {
		$('#ratingForm').ajaxForm({
			target: '/manager/saveRating',
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#ratingProgress").show();
			},
			success: function(response)
			{
				$("#ratingProgress").hide();
				success('top', 'Новость успешно сохранена!');

				var oEditor = FCKeditorAPI.GetInstance('message');
				var message = oEditor.GetHTML(true);
				
				var news_snippet = '<div class="table"><div class="angle angle-lt"></div><div class="angle angle-rt"></div><div class="angle angle-lb"></div><div class="angle angle-rb"></div><div><span class="label">' +
				getNowDate() +
				'</span> <span class="label"><b>' +
				$('.rating_box input#title').val() +
				'</b></span></div><div>' +
				message +
				'</div></div><br><br>';
				
				$('#news_header').after(news_snippet);
				
				$('.rating_box input#title').val('');
				oEditor.SetHTML('');
			},
			error: function(response)
			{
				$("#ratingProgress").hide();
				error('top', 'Заполните все поля и сохраните еще раз.');
			}
		});
	});

	<?= editor('rating_message', 200, 920, 'PackageComment') ?>
</script>