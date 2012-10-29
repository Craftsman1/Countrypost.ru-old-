<div class="ratings table">
	<div style="height: 41px;">
		<span style="margin-top: 12px;">
			РЕЙТИНГ:
		</span>
		<span>
			<?= $manager->statistics->rating ?>
		</span>
	</div>
	<div>
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => 5,
			'description' => 'Консультация (ответы в скайпе, почте)',
			'vote_count' => 123
		)); ?>
	</div>
	<div>
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => 4,
			'description' => 'Выкуп товара',
			'vote_count' => 456
		)); ?>
	</div>
	<div>
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => 3,
			'description' => 'Консолидация и отправка посылок',
			'vote_count' => 789
		)); ?>
	</div>
	<div style="height:auto;">
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => 2,
			'description' => 'Упаковка посылок',
			'vote_count' => 321
		)); ?>
	</div>
</div>