<div class="rating_group table">	<div class="rating_item">		<? View::show('/main/elements/ratings/ratings_viewer', array(		'rating' => isset($rating->communication_rating) ? $rating->communication_rating * 4 : -1,		'description' => 'Консультация (ответы в скайпе, почте)',		'rating_type' => 'communication_rating'	)); ?>		<br>		<? View::show('/main/elements/ratings/ratings_viewer', array(		'rating' => isset($rating->consolidation_rating) ? $rating->consolidation_rating * 4 : -1,		'description' => 'Консолидация и отправка посылок',		'rating_type' => 'consolidation_rating'	)); ?>	</div>	<div class="rating_item">		<? View::show('/main/elements/ratings/ratings_viewer', array(		'rating' => isset($rating->buy_rating) ? $rating->buy_rating * 4 : -1,		'description' => 'Выкуп товара',		'rating_type' => 'buy_rating'	)); ?>		<br>		<? View::show('/main/elements/ratings/ratings_viewer', array(		'rating' => isset($rating->pack_rating) ? $rating->pack_rating * 4 : -1,		'description' => 'Упаковка посылок',		'rating_type' => 'pack_rating'	)); ?>	</div></div>