<div class="personal table">
	<div>
		<span>
			Страна:
		</span>
		<span>
			<img src="/static/images/flags/<?= $countries_en[$manager->manager_country] ?>.png" />
			<b><?=$countries[$manager->manager_country]?></b>
		</span>
	</div>
	<div>
		<span>
			Зарегистрирован:
		</span>
		<span>
			<?= isset($manager->created) ? date('d.m.Y h:i', strtotime($manager->created)) : date('d.m.Y h:i')?>
		</span>
	</div>
	<div>
		<span>
			Заказов в работе:
		</span>
		<span>
			<?= $manager->statistics->completed_orders ?>
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
			<? View::show('main/elements/dealers/rating', array('manager' => $manager)); ?>
		</span>
	</div>
	<div>
		<span>
			Сайт:
		</span>
		<span>
			<a target="_blank" href="<?= empty($manager->website) ? BASEURL.$manager->statistics->login : $manager->website ?>"><?= empty($manager->website) ? BASEURL.$manager->statistics->login : $manager->website ?></a>
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