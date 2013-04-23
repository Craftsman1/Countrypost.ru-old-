<h3>Новости и объявления</h3>
<div class="table">
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<p>
		<? if (empty($blogs)) : echo('Нет новостей.'); else : foreach ($blogs as $blog) :  ?>
		<div>
			<span class="label">
				<?= isset($blog->created) ? date('d.m.Y H:i', strtotime($blog->created)) : '' ?>
			</span>
			<span class="label">
				<b id="blog_title<?= $blog->blog_id ?>"><?= $blog->title ?></b>
			</span>
		</div>
		 
		<div id="blog_message<?= $blog->blog_id ?>">
			<?= html_entity_decode($blog->message) ?>
		</div>
		 

		<hr>
		<? endforeach; endif; ?>
	</p>
</div>