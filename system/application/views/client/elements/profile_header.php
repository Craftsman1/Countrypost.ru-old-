<div class='managerinfo admin-inside'>
	<h2>Мой профиль
		<a target="_blank"
		   href="<?= $this->config->item('base_url') . $this->session->userdata['user_login'] ?>"
		   style="font-size: 11px; text-transform: none; font-weight: normal;">Посмотреть мою страницу в новом
			окне</a></h2>
	<ul class='tabs'>
		<li class='active profile'>
			<div>
				<a class='profile' href="javascript:void(0);">Персональные данные</a>
			</div>
		</li>
		<li class='reviews'>
			<div>
				<a class='delivery_address' href="javascript:void(0);">Адреса&nbsp;доставки</a>
			</div>
		</li>
	</ul>
</div>
<script>
	$(function() {
		
		init_profile();
		choose_tab();
	});
	function choose_tab()
	{
		var tabname = document.location.hash;
		if((''+tabname).length>0 && tabname=='#delivery_address')
		{
			$('ul.tabs li').removeClass('active');
			$('div.client_tab,div.dealer_tab').hide();

			$('div.delivery_address').show();
			$('ul.tabs li.reviews').addClass('active');
		}
	}
</script>