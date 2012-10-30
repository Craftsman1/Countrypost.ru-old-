<h3>Новости и объявления</h3>
<div class="table">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<p>
		<? if (empty($blogs)) : echo('Нет новостей.'); else : foreach ($blogs as $blog) :  ?>
		<? endforeach; endif; ?>
	</p>
</div>