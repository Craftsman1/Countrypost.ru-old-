<div class="client_ratings dealer_tab" style="display:none;">
	<form action="/client/saveRating" id="ratingForm" method="POST">
		<div class="table">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<div class="rating_box admin-inside">
				<input type="hidden" name="manager_id" value='<?= $manager->manager_user ?>' />
				<div>
					<span class="label">
						<input type='radio' value='positive' name="rating_type" />
						<label for="positive" style="color: green;">Положительный</label>
					</span>
					<span class="label">
						<input type='radio' value='neutral' name="rating_type" checked />
						<label for="neutral">Нейтральный</label>
					</span>
					<span class="label">
						<input type='radio' value='negative' name="rating_type" />
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
					'description' => 'Консультация (ответы в скайпе, почте)',
					'rating_type' => 'communication_rating'
				)); ?>
				</div>
				<div>
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 4,
					'description' => 'Выкуп товара',
					'rating_type' => 'buy_rating'
				)); ?>
				</div>
				<div>
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 3,
					'description' => 'Консолидация и отправка посылок',
					'rating_type' => 'consolidation_rating'
				)); ?>
				</div>
				<div style="height:auto;">
					<? View::show('/main/elements/dealers/ratings_plugin', array(
					'rating' => 2,
					'description' => 'Упаковка посылок',
					'rating_type' => 'pack_rating'
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
	<h3 id="ratings_header" <? if (empty($ratings)) : ?>style="display: none;"<? endif; ?>>Все отзывы</h3>
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
			target: '/client/saveRating',
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
				success('top', 'Отзыв успешно сохранен!');

				var oEditor = FCKeditorAPI.GetInstance('rating_message');
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
				$('#ratingForm .ratings_plugin div').removeClass('on').removeClass('half');
				$('#ratingForm .ratings_plugin input').val('');
				$('#ratingForm .rating_box input[name=rating_type][value=neutral]').attr('checked', true);
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