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
		<li><a href="<?=BASEURL?>main/showHowItWork">Как сделать заказ</a></li>
		<li><a href="<?=$user ? BASEURL.$user->user_group : BASEURL.'user/registration';?>">Личный кабинет</a></li>
		<li><a href="<?=BASEURL?>main/showPricelist">Тарифы на доставку</a></li>
		<li><a href="<?=BASEURL?>main/showCollaboration">Сотрудничество</a></li>
		<li><a href="<?=BASEURL?>main/showShopCatalog">Каталог интернет магазинов</a></li>
		<li><a href="<?=BASEURL?>main/showContacts">Контакты</a></li>
	</ul>
</div>
<div>&nbsp;<br /></div>
<div class="adminMenu">
	<br />
	<br />
</div>