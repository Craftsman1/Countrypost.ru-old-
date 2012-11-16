<div class="ratings table">
	<div style="height: 41px;">
		<span style="margin-top: 12px;">
			РЕЙТИНГ:
		</span>
		<span>
			<?= $manager->statistics->rating ?>
			<? //print_r($manager);die();?>
		</span>
	</div>
	<div>
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => isset($manager->communication_rating) ?
							round(4 * $manager->communication_rating) :
							-1,
			'description' => 'Консультация (ответы в скайпе, почте)',
			'vote_count' => $manager->communication_rating_count
		)); ?>
	</div>
	<div>
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => isset($manager->buy_rating) ?
							round(4 * $manager->buy_rating) :
							-1,
			'description' => 'Выкуп товара',
			'vote_count' => $manager->buy_rating_count
		)); ?>
	</div>
	<div>
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => isset($manager->consolidation_rating) ?
							round(4 * $manager->consolidation_rating) :
							-1,
			'description' => 'Консолидация и отправка посылок',
			'vote_count' => $manager->consolidation_rating_count
		)); ?>
	</div>
	<div style="height:auto;">
		<? View::show('/main/elements/dealers/ratings', array(
			'rating' => isset($manager->pack_rating) ?
							round(4 * $manager->pack_rating) :
							-1,
			'description' => 'Упаковка посылок',
			'vote_count' => $manager->pack_rating_count
		)); ?>
	</div>
</div>