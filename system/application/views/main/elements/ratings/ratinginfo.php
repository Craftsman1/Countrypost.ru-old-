<div class='ratinginfo'>	<img src='<?= IMG_PATH ?>avatar.png'>	<a href="<?= $rating->statistics->login ?>"><?= $rating->statistics->fullname ?></a>	(№ <?= $rating->client_id ?>)<? if ($rating->rating_type) : ?>	<img src="<?= IMG_PATH ?><?= $rating->rating_type > 0 ? 'positive' : 'negative' ?>.png" style="margin: 2px 0 0 0;	 float: right;" title="Это <?= $rating->rating_type > 0 ? 'положительный' : 'отрицательный' ?> отзыв">	<? endif; ?>	<br>	<div class="reviews">		<span class='label'><?= isset($rating->created) ? date('d.m.Y H:i', strtotime($rating->created)) : date('d.m.Y H:i')?></span>		<? View::show('/main/elements/ratings/reviews', array(			'positive' =>  $rating->statistics->positive_reviews,			'neutral' =>  $rating->statistics->neutral_reviews,			'negative' =>  $rating->statistics->negative_reviews,		)); ?>	</div>	<? View::show('/main/elements/ratings/rating_block', array(		'rating' => $rating	)); ?></div>