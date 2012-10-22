<form class='admin-inside' id="filterForm" action='<?=$selfurl?>filterPaymentHistory' method='POST'>
	<input type='hidden' name='resetFilter' id='resetFilter' value='' />
	<b>Поиск платежа:</b> <input type='text' name='svalue' value="<?= isset($filter->svalue)? $filter->svalue : '' ?>"></input> по 
	<select name='sfield'>
		<option value='id' <?= isset($filter->sfield) && $filter->sfield=='id' ? 'selected' : '' ?>>Номеру</option>
		<option value='login' <?= isset($filter->sfield) && $filter->sfield=='login' ? 'selected' : '' ?>>Логину</option>	
	</select>
	<select name='stype'>
		<option value='from' <?= isset($filter->stype) && $filter->stype == 'from' ? 'selected' : '' ?>>Отправителя</option>
		<option value='to' <?= isset($filter->stype) && $filter->stype == 'to' ? 'selected' : '' ?>>Получателя</option>	
		<option value='package' <?= isset($filter->stype) && $filter->stype == 'package' ? 'selected' : '' ?>>Посылки</option>	
		<option value='order' <?= isset($filter->stype) && $filter->stype == 'order' ? 'selected' : '' ?>>Заказа</option>	
	</select>
	за
	<select name='sdate'>
		<option value='all' <?= isset($filter->sdate) && $filter->sdate == 'all' ? 'selected' : '' ?>>Весь период</option>
		<option value='day' <?= isset($filter->sdate) && $filter->sdate == 'day' ? 'selected' : '' ?>>День</option>	
		<option value='week' <?= isset($filter->sdate) && $filter->sdate == 'week' ? 'selected' : '' ?>>Неделю</option>
		<option value='month' <?= isset($filter->sdate) && $filter->sdate == 'month' ? 'selected' : '' ?>>Месяц</option>
	</select>
	<select name='sservice'>
		<option value='all'>Все платежи</option>
		<option value='package' <?= isset($filter->sservice) && $filter->sservice == 'package' ? 'selected' : '' ?>>Оплата посылки</option>
		<option value='order' <?= isset($filter->sservice) && $filter->sservice == 'order' ? 'selected' : '' ?>>Оплата заказа</option>	
		<option value='in' <?= isset($filter->sservice) && $filter->sservice == 'in' ? 'selected' : '' ?>>Пополнение счета</option>
		<option value='out' <?= isset($filter->sservice) && $filter->sservice == 'out' ? 'selected' : '' ?>>Заявки на вывод (клиенты)</option>
		<option value='salary' <?= isset($filter->sservice) && $filter->sservice == 'salary' ? 'selected' : '' ?>>Заявки на вывод (партнеры)</option>
	</select>
	<? if ($result->e < 0) : ?>
		<em style="color:red;"><?= $result->m ?></em>
	<? endif; ?>
	<div class='submit historySearch'>
		<div>
			<input type='submit' value='Искать' />
		</div>
	</div>
	<a href='#' id='reset_filter'>Все платежи</a>
</form>
<script>
	$(function() {
		$('a#reset_filter').click(function(e) {
			e.preventDefault();
			
			$("#resetFilter").val("1");
			$("#filterForm").submit();
		});
	});
</script>