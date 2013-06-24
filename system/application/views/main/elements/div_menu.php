<style>
.menu li {
	float:	left;
	border:	0.5px solid;
	float:	left;
	list-style: none outside none;
	padding: 23px;
}

.adminMenu a{
	margin: 100px;
	float: clear;
}
</style>
<div id="menu">
	<ul class="menu">
		<li><a href="<?=$this->config->item('base_url')?>main/showHowItWork">Как сделать заказ</a></li>
		<li><a href="<?=$user ? $this->config->item('base_url').$user->user_group : $this->config->item('base_url').'user/registration';?>">Личный кабинет</a></li>
		<li><a href="<?=$this->config->item('base_url')?>main/showPricelist">Тарифы на доставку</a></li>
		<li><a href="<?=$this->config->item('base_url')?>main/showCollaboration">Сотрудничество</a></li>
		<li><a href="<?=$this->config->item('base_url')?>main/showShopCatalog">Каталог интернет магазинов</a></li>
		<li><a href="<?=$this->config->item('base_url')?>main/showContacts">Контакты</a></li>
	</ul>
</div>
<div>&nbsp;<br /></div>
<div class="adminMenu">
	<br />
	<br />
</div>