<div class="personal table">
	<div>
		<span>
			Страна:
		</span>
		<span>
			<img src="/static/images/flags/<?= $countries_en[$manager->manager_country] ?>.png" />
			<b><?= $countries[$manager->manager_country] ?> (<?= $manager->city ?>)</b>
		</span>
	</div>
	<div>
		<span>
			Зарегистрирован:
		</span>
		<span>
			<?= isset($manager->created) ? date('d.m.Y H:i', strtotime($manager->created)) : date('d.m.Y H:i')?>
		</span>
	</div>
	<div>
		<span>
			Заказов в работе:
		</span>
		<span>
			12345678
		</span>
	</div>
	<div>
		<span>
			Выполненных заказов:
		</span>
		<span>
			<?= $manager->statistics->completed_orders ?>
		</span>
	</div>
	<div>
		<span>
			Отзывы:
		</span>
		<span>
			<? View::show('main/elements/ratings/reviews', array(
				'positive' =>  $manager->statistics->positive_reviews,
				'neutral' =>  $manager->statistics->neutral_reviews,
				'negative' =>  $manager->statistics->negative_reviews)); ?>
		</span>
	</div>
	<div>
		<span>
			Сайт:
		</span>
		<span>
			<a target="_blank" href="<?= empty($manager->website) ? $this->config->item('base_url').$manager->statistics->login : $manager->website ?>"><?= empty($manager->website) ? $this->config->item('base_url').$manager->statistics->login : $manager->website ?></a>
		</span>
	</div>
	<div>
		<span>
			Skype:
		</span>
		<span>
			<?= $manager->statistics->skype ?>
		</span>
	</div>
	<div>
		<span>
			Email:
		</span>
		<span>
			<a href="mailto:<?= $manager->statistics->email ?>"><?= $manager->statistics->email ?></a>
		</span>
	</div>
</div>