<div class='content'>
	<?View::show($viewpath.'elements/div_submenu');?>
	<h3>Закрытые заявки на вывод (партнеры)</h3>
	<div class='back'>
		<a class='back' href='javascript:history.back();'><span>Назад</span></a>
	</div>
	<form class='admin-sorting' id="filterForm" action="<?=$selfurl?>filterPayedManagerO2o" method="POST">
		<input type="hidden" name="order2out_status" value="payed">
		<div class='sorting'>
			<span class='first-title'>Поиск заявки:&nbsp;</span>
			<input style='float:left;' type='text' name='clientO2oSearchValue' value="<?=isset($filter->order2out_id) ? $filter->order2out_id : ''?><?=isset($filter->user_login) ? $filter->user_login : ''?><?=isset($filter->order2out_user) ? $filter->order2out_user : ''?>" />
			<span>&nbsp;по:&nbsp;</span> 
			<select class="select first-input" name='clientO2oSearchType'>
				<option value='order2out_id' <? if (isset($filter->order2out_id)) : ?>selected<? endif; ?>>Номеру заявки</option>
				<option value='user_login' <? if (isset($filter->user_login)) : ?>selected<? endif; ?>>Логину партнера</option>
				<option value='order2out_user' <? if (isset($filter->order2out_user)) : ?>selected<? endif; ?>>Номеру партнера</option>
			</select>
			<div class='submit' style="width:94px;"><div><input style="width:78px;" type='submit' value='Поиск' /></div></div>
		</div>
	</form>
	<?View::show($viewpath.'ajax/showManagerPayedOrdersToOut', array(
				'Orders'	=> $Orders,
				'statuses'	=> $this->Order2out->getStatuses(),
				'services'	=> $this->Services->getOutServices(),
				'pager' => $pager));?>
</div>