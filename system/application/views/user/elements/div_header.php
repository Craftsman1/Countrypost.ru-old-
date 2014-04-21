
		<div class='header'>
			<h1 class='logo'><a href='/'>CountryPost - Лучший сервис покупок за рубежом</a></h1>
			<ul class='menu'>
				<li><a href='<?=$this->config->item('base_url')?>main/showHowItWork'>Как это работает</a></li>
				<li><a href='<?=$user ? $this->config->item('base_url').$user->user_group : $this->config->item('base_url').'user/showRegistration';?>'>Личный кабинет</a></li>
<!--				<li><a href='< ?=$this->config->item('base_url')?>main/showPays'>Способы оплаты</a></li>-->
				<li><a href='<?=$this->config->item('base_url')?>main/showPricelist'>Тарифы на доставку</a></li>
				<li><a href='<?=$this->config->item('base_url')?>main/showCollaboration'>Сотрудничество</a></li>
				<li><a href='<?=$this->config->item('base_url')?>main/showShopCatalog'>Каталог магазинов</a></li>
				<li><a href='<?=$this->config->item('base_url')?>main/showContacts'>Контакты</a></li>
			</ul>
		</div>