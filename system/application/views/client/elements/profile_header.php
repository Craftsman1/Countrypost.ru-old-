<div class='managerinfo admin-inside'>
	<h2>Мой профиль</h2>
	<ul class='tabs'>
		<style>
			.top-block {
				min-height: auto;
			}
		</style>
		<li class='active profile'><div><a class='profile' href="javascript:void();">Персональные данные</a></div></li>
		<li class='reviews'><div><a class='reviews' href="javascript:void();">Отзывы</a></div></li>
	</ul>
</div>
<script>
	$(function() {
		$('ul.tabs a')
			.click(function(e) {
				e.preventDefault();
				$('ul.tabs li').removeClass('active');
				$('div.client_tab').hide();
				
				$('div.' + $(e.target).attr('class')).show();
				
				$(this).parent().parent().addClass('active');
			});
	});
</script>