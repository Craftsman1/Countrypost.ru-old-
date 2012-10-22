<a name="pagerScroll"></a>
<script>
	$(function() 
	{
		// конвертация
		$('form.admin-inside input:text')
			.keypress(function(event) {
				validate_number(event);
			});
			
		$('form.admin-inside input.amount')
			.bind('keypress keydown mouseup keyup blur', function() {
				convert_to_local($(this));
			});
			
		$('form.admin-inside input.amount_local')
			.bind('keypress keydown mouseup keyup blur', function() {
				convert_to_usd($(this));
			});
	});

	function convert_to_local($input)
	{
		var $input_local = $input.parent().parent().find('.amount_local');
		var service = $input_local.attr('rel');
		
		var tax = getTax(service);
		var extra = getExtra(service);
		var rate = getRate(service);
		var amount = parseInt($input.val());
		amount = (isNaN(amount) ? 0 : amount) ;
		
		var amount_local = Math.ceil(amount * rate + tax * amount * rate * 0.01) + extra;
		$input_local.val(amount_local);
	}

	function convert_to_usd($input_local)
	{
		var $input = $input_local.parent().parent().find('.amount');
		var service = $input_local.attr('rel');
		
		var tax = getTax(service);
		var extra = getExtra(service);
		var rate = getRate(service);
		var amount_local = parseInt($input_local.val());

		var amount = amount_local - extra;
		amount = Math.floor(amount / (rate + tax * rate * 0.01));
		
		$input.val(amount);
	}

	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]/;
		if (!regex.test(key))
		{
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}

	function getRate(service)
	{
		var rate = 0;
	
		switch (service) 
		{
			case "bm": rate = <?= $usd ?>; break;
			case "cc": rate = <?= $usd ?>; break;
			case "so": rate = <?= $usd ?>; break;
			case "op": rate = <?= $usd ?>; break;
			case "rbk": rate = <?= $usd ?>; break;
			case "qw": rate = <?= $usd ?>; break;
            case "bta": rate = <?= $kzt ?>; break;
            case "ccr": rate = <?= $kzt ?>; break;
            case "kkb": rate = <?= $kzt ?>; break;
            case "nb": rate = <?= $kzt ?>; break;
            case "tb": rate = <?= $kzt ?>; break;
            case "atf": rate = <?= $kzt ?>; break;
            case "ab": rate = <?= $kzt ?>; break;
			case "pb": rate = <?= $uah ?>; break;
			case "sv": rate = <?= $usd ?>; break;
			case "vtb": rate = <?= $usd ?>; break;
		}
		
		return rate;
	}
	
	function getTax(service)
	{
		var tax = 0;
	
		switch (service) 
		{
			case "wmr": tax = <?= WM_IN_TAX ?>; break;
			case "wmz": tax = <?= WMZ_IN_TAX ?>; break;
			case "rk": tax = <?= RK_IN_TAX ?>; break;
			case "w1": tax = <?= W1_IN_TAX ?>; break;
			case "lp": tax = <?= LP_IN_TAX ?>; break;
			case "bm": tax = <?= BM_IN_TAX ?>; break;
			case "rbk": tax = <?= RBK_IN_TAX ?>; break;
			case "qw": tax = <?= QW_IN_TAX ?>; break;
			case "qw1": tax = <?= QIWI_IN_TAX ?>; break;
            case "pp": tax = <?= PP_IN_TAX ?>; break;
            case "bta": tax = <?= BTA_IN_TAX ?>; break;
            case "ccr": tax = <?= CCR_IN_TAX ?>; break;
            case "kkb": tax = <?= KKB_IN_TAX ?>; break;
            case "nb": tax = <?= NB_IN_TAX ?>; break;
            case "tb": tax = <?= TB_IN_TAX ?>; break;
            case "atf": tax = <?= ATF_IN_TAX ?>; break;
            case "ab": tax = <?= AB_IN_TAX ?>; break;
            case "pb": tax = <?= PB_IN_TAX ?>; break;
            case "sv": tax = <?= SV_IN_TAX ?>; break;
            case "vtb": tax = <?= VTB_IN_TAX ?>; break;
		}
		
		return tax;
	}
	
	function getExtra(service)
	{
		var extra = 0;
	
		switch (service) 
		{
			case "wmz": extra = <?= WMZ_IN_EXTRA ?>; break;
            case "pp": extra = <?= PP_IN_EXTRA ?>; break;
		}
		
		return extra;
	}
</script>
<form method="POST" class='admin-inside' action="/admin/saveOrders2in/showClientOpenOrders2In" id="pagerForm">
	<? View::show('/admin/elements/div_float_credentials'); ?>
	<ul class='tabs'>
		<li class='active'><div><a href='<?= $selfurl ?>showClientOpenOrders2In'>Новые</a></div></li>
		<li><div><a href='<?= $selfurl ?>showClientPayedOrders2In'>Выполненные</a></div></li>
	</ul>
	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<? if (isset($Orders2In) && count($Orders2In)) : ?>
		<table>
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<tr>
				<th>№ заявки</th>
				<th>Клиент</th>
				<th>Способ оплаты</th>
				<th>Статус заявки</th>
				<th>Комментарии</th>
				<th>Сумма пополнения</th>
				<th>Сумма перевода</th>
				<th width="1">Удалить</th>
			</tr>
			<? foreach ($Orders2In as $order) : ?>
			<tr>
				<td>
					<b>№ <?= $order->order2in_id ?></b>
					<br />
					<?= date("d.m.Y H:i", strtotime($order->order2in_createtime)) ?>
				</td>
				<td>
					<a href="/admin/editClient/<?= $order->order2in_user; ?>"><?= $order->order2in_user; ?></a>
					<br />
					<?= $order->client_surname ?>
					<?= $order->client_name ?>
					<?= $order->client_otc ?>
					<br />
					(<?= $order->user_login ?>)
				</td>
				<td>
					<? foreach ($services as $service) : 
					if ($service->payment_service_id == $order->order2in_payment_service) : ?>
					<u><?= $service->payment_service_name ?></u>
					<br />
					<? break; endif; endforeach; ?>
					<? if ($order->order2in_payment_service == 'bm' OR
						$order->order2in_payment_service == 'pb' OR
						$order->order2in_payment_service == 'bta' OR
						$order->order2in_payment_service == 'ccr' OR
						$order->order2in_payment_service == 'kkb' OR
						$order->order2in_payment_service == 'nb' OR
						$order->order2in_payment_service == 'tb' OR
						$order->order2in_payment_service == 'atf' OR
						$order->order2in_payment_service == 'ab' OR
						$order->order2in_payment_service == 'sv' OR
						$order->order2in_payment_service == 'vtb') : ?>
					<b>Номер карты:</b>
					<?= $order->order2in_details ?>
					<br />
					<? elseif ($order->order2in_payment_service == 'rbk' OR
						$order->order2in_payment_service == 'qw') : ?>
					<b>Номер кошелька:</b>
					<?= $order->order2in_details ?>
					<br />
					<? elseif ($order->order2in_payment_service == 'mb') : ?>
					<b>Email отправителя:</b>
					<?= $order->order2in_details ?>
					<br />
					<? else : ?>
					<?= $order->order2in_details ?>
					<br />
					<? endif; ?>
					
					<? if (($order->order2in_payment_service == 'bm' OR
							$order->order2in_payment_service == 'cc' OR
							$order->order2in_payment_service == 'so' OR
							$order->order2in_payment_service == 'op' OR
							$order->order2in_payment_service == 'pb' OR
							$order->order2in_payment_service == 'bta' OR
							$order->order2in_payment_service == 'ccr' OR
							$order->order2in_payment_service == 'kkb' OR
							$order->order2in_payment_service == 'nb' OR
							$order->order2in_payment_service == 'tb' OR
							$order->order2in_payment_service == 'atf' OR
							$order->order2in_payment_service == 'ab' OR
							$order->order2in_payment_service == 'sv' OR
							$order->order2in_payment_service == 'vtb') &&
							isset($Orders2InFoto[$order->order2in_id])) : ?>
					<b>Скриншот:</b>
					<a href="javascript:void(0)" onclick="setRel(<?= $order->order2in_id ?>)">
						Посмотреть&nbsp;(<?= count($Orders2InFoto[$order->order2in_id]); ?>)<? 
						foreach ($Orders2InFoto[$order->order2in_id] as $o2iFoto) : ?><a rel="lightbox_<?= $order->order2in_id ?>" href="/admin/showOrder2InFoto/<?= $order->order2in_id ?>/<?= $o2iFoto ?>" style="display:none;">Посмотреть</a><? endforeach; ?></a>
					<? endif; ?>
				</td>
				<td>
					<select name="status_<?= $order->order2in_id ?>">
						<? foreach ($Orders2InStatuses as $key=>$val) : ?>
						<? if ($key != 'not_confirmed' OR 
							$order->order2in_payment_service == 'bm' OR
							$order->order2in_payment_service == 'cc' OR
							$order->order2in_payment_service == 'so' OR
							$order->order2in_payment_service == 'op' OR
							$order->order2in_payment_service == 'pb' OR
							$order->order2in_payment_service == 'bta' OR
							$order->order2in_payment_service == 'ccr' OR
							$order->order2in_payment_service == 'kkb' OR
							$order->order2in_payment_service == 'nb' OR
							$order->order2in_payment_service == 'tb' OR
							$order->order2in_payment_service == 'atf' OR
							$order->order2in_payment_service == 'ab' OR
							$order->order2in_payment_service == 'sv' OR
							$order->order2in_payment_service == 'vtb') : ?>
						<option value='<?= $key ?>' <? if ($key==$order->order2in_status) : ?>selected="selected"<? endif; ?>><?= $val ?></option>
						<? endif; ?>
						<? endforeach; ?>	
					</select>
				</td>
				<td>
					<a href="/admin/showO2iComments/<?= $order->order2in_id; ?>">Посмотреть</a>
					<? if ($order->order2in_2admincomment): ?>
						<br />Добавлен новый коментарий
					<? endif; ?>
				</td>
				<td>
					<input type="text" name="amount_<?= $order->order2in_id ?>" value="<?= $order->order2in_amount ?>" maxlength="4" style="width:51px;" class="input amount" rel="<?= $order->order2in_payment_service ?>" />
				</td>
				<td nowrap>
					<input type="text" name="amount_local_<?= $order->order2in_id ?>" value="<? if ( ! empty($order->order2in_amount_local)) : ?><?= $order->order2in_amount_local ?><? elseif ( ! empty($order->order2in_amount_rur)) : ?><?= $order->order2in_amount_rur ?><? elseif ( ! empty($order->order2in_amount_kzt)) : ?><?= $order->order2in_amount_kzt ?><? endif; ?>" maxlength="5" style="width:51px;" class="input amount_local" rel="<?= $order->order2in_payment_service ?>" />
					<? if ( ! empty($order->order2in_amount_local)) : ?>
					<?= $order->order2in_currency ?>
					<? elseif ( ! empty($order->order2in_amount_rur)) : ?>
					руб.
					<? elseif ( ! empty($order->order2in_amount_kzt)) : ?>
					<em class="tenge">&nbsp;&nbsp;&nbsp;</em>
					<? endif; ?>
				</td>
				<td align="center">
					<? if ($order->order2in_status != 'payed'): ?>
						<a href="/admin/deleteOrder2In/<?= $order->order2in_id; ?>"><img title="Удалить" border="0" src="/static/images/delete.png"></a>
					<? endif; ?>
				</td>
			</tr>
			<? endforeach; ?>
			<tr class='last-row'>
				<td colspan='10'>
					<div class='float'>	
						<div class='submit'><div><input type='submit' value='Сохранить' /></div></div>
					</div>
				</td>
			</tr>
		</table>
		<? else: ?>
			<div align="center">Заявки отсутствуют</div>
			<br>
		<? endif; ?>
	</div>
	<? if (isset($pager)) echo $pager; ?>
</form>