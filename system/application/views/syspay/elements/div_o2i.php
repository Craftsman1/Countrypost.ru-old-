<?
$payable_amount =
	($order->order_cost > ($order->order_cost_payed + $order->excess_amount)) ?
		($order->order_cost - $order->order_cost_payed - $order->excess_amount) :
		'';
?>
<script type="text/javascript">
	var rate_usd = <?= $rate_usd ?>;
    var rate_kzt = <?= $rate_kzt ?>;
    var rate_uah = <?= $rate_uah ?>;
    var rate_rur = <?= $rate_rur ?>;

	function getTax(service)
	{
		var tax = 0;
	
		switch (service) 
		{
			case "wmr": tax = <?= WM_IN_TAX ?>; break;
			case "wmz": tax = <?= WMZ_IN_TAX ?>; break;
			case "bm": tax = <?= BM_IN_TAX ?>; break;
			case "sb": tax = <?= BM_IN_TAX ?>; break;
			case "rbk": tax = <?= RBK_IN_TAX ?>; break;
			case "qw": tax = <?= QW_IN_TAX ?>; break;
			case "sv": tax = <?= SV_IN_TAX ?>; break;
			case "alf": tax = <?= AL_RUB_IN_TAX ?>; break;
			case "ald": tax = <?= AL_USD_IN_TAX ?>; break;
			case "wur": tax = <?= WU_RUB_IN_TAX ?>; break;
			case "wud": tax = <?= WU_USD_IN_TAX ?>; break;
			case "con": tax = <?= CON_RUB_IN_TAX ?>; break;
			case "cod": tax = <?= CON_USD_IN_TAX ?>; break;
			case "unr": tax = <?= UNI_RUB_IN_TAX ?>; break;
			case "und": tax = <?= UNI_USD_IN_TAX ?>; break;
			case "gcr": tax = <?= GC_RUB_IN_TAX ?>; break;
			case "gcd": tax = <?= GC_USD_IN_TAX ?>; break;
			case "anr": tax = <?= AN_RUB_IN_TAX ?>; break;
			case "and": tax = <?= AN_USD_IN_TAX ?>; break;
			case "pp": tax = <?= PP_IN_TAX ?>; break;
			case "cus": tax = <?= CUS_USD_IN_TAX ?>; break;
			case "cuu": tax = <?= CUS_UAH_IN_TAX ?>; break;
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
	
	function getService(code)
	{
		var service = '';
	
		switch (code) 
		{
			case "immediate_wmr" : service = "wmr"; break;
			case "immediate_qw" : service = "qw1"; break;
			case "immediate_rbk" : service = "rk"; break;
			case "immediate_wmz" : service = "wmz"; break;
			case "delayed_sb" : service = "bm"; break;
			case "delayed_pp" : service = "pp"; break;
			case "delayed_sv" : service = "sv"; break;
			case "delayed_alf" : service = "alf"; break;
			case "delayed_ald" : service = "ald"; break;
			case "delayed_wur" : service = "wur"; break;
			case "delayed_wud" : service = "wud"; break;
			case "delayed_con" : service = "con"; break;
			case "delayed_cod" : service = "cod"; break;
			case "delayed_unr" : service = "unr"; break;
			case "delayed_und" : service = "und"; break;
			case "delayed_gcr" : service = "gcr"; break;
			case "delayed_gcd" : service = "gcd"; break;
			case "delayed_anr" : service = "anr"; break;
			case "delayed_and" : service = "and"; break;
			case "delayed_cus" : service = "cus"; break;
			case "delayed_cuu" : service = "cuu"; break;
		}
		
		return service;
	}
	
	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]/;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}

	function updateExcessAmount(id)
	{
		var excess_amount = <?= $order->excess_amount ?>;
		var amount = $('.' + id + ' .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount);

		$('div.countrypost_payment_box b.total_local_amount').html(excess_amount + amount);
	}

	function calculateTotals()
	{
		calculateTotal('alf');
		calculateTotal('ald');
		calculateTotal('con');
		calculateTotal('cod');
		calculateTotal('wur');
		calculateTotal('wud');
		calculateTotal('unr');
		calculateTotal('und');
		calculateTotal('gcr');
		calculateTotal('gcd');
		calculateTotal('anr');
		calculateTotal('and');
		calculateTotal('pp');
		calculateTotal('wmr');
		calculateTotal('qw');
		calculateTotal('wmz');
		calculateTotal('rbk');
		calculateTotal('sv');
		calculateTotal('sb');
		calculateTotal('cus');
		calculateTotal('cuu');
	}
	
	function calculateTotal(service)
	{
		updateExcessAmount('countrypost_payment_box');

		if (service == 'sv' ||
			service == 'sb' ||
			service == 'qw' ||
			service == 'wmr' ||
			service == 'rbk' ||
			service == 'alf' ||
			service == 'wur' ||
			service == 'con' ||
			service == 'unr' ||
			service == 'gcr' ||
			service == 'anr')
		{
			calculateTotalRUB(service);
		} 
		else if (service == 'ald' ||
				service == 'wud' ||
				service == 'cod' ||
				service == 'und' ||
				service == 'gcd' ||
				service == 'and' ||
				service == 'cus')
		{
			calculateTotalDelayedUSD(service);
		}
		else if (service == 'cuu')
		{
			calculateTotalDelayedUAH(service);
		}
		else if (service == 'pp' || service == 'wmz')
		{
			calculateTotalUSD(service);
		}
	}
	
	function calculateTotalRUB(service)
	{
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_rur;

		var ru_amount = Math.ceil(amount + percentage * amount * 0.01 + extra);

		$('div.total_' + service).html(ru_amount + ' RUB');
		updateTotalCountrypost(service, ru_amount + ' RUB');
	}

	function updateTotalCountrypost(service, amount)
	{
		var radio = $('div.payment_system input:checked');

		if (radio.length == 0)
		{
			return;
		}

		var attr = radio.attr('id');
		if (strpos(attr, service) !== false)
		{
			$('.delayed .total b').html(amount);
		}
	}

	function strpos(haystack, needle, offset)
	{
		var i = (haystack).indexOf(needle, (offset || 0));
		return i === -1 ? false : i;
	}

	function calculateTotalDelayedUSD(service)
	{
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_usd;

		var ru_amount = Math.ceil(amount + percentage * amount * 0.01 + extra);

		$('div.total_' + service).html(ru_amount + ' USD');
		updateTotalCountrypost(service, ru_amount + ' USD');
	}

	function calculateTotalDelayedUAH(service)
	{
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_uah;

		var converted_amount = Math.ceil(amount + percentage * amount * 0.01 + extra);

		$('div.total_' + service).html(converted_amount + ' UAH');
		updateTotalCountrypost(service, converted_amount + ' UAH');
	}

	function calculateTotalUSD(service)
	{
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_usd;

		var total = Math.ceil(amount + percentage * amount * 0.01 + extra);
		
		$('div.total_' + service).html(total + ' USD');
		updateTotalCountrypost(service, total + ' USD');
	}

    function openO2iPopup(x)
	{
		var payment_option = $('input:radio#' + x).filter(':checked').attr('id');
        var service = getService(payment_option);
		calculateTotals();
		var amount_usd = $('.payment_system input:text').val();
		var user_id = '<?= isset($user->user_id) ? $user->user_id : '' ?>';

		switch (service) 
		{
			case "bm": openSberbankPopup(
					user_id,
					amount_usd,
					$('#delayed_ru').val());
				break;
			case "sv": openSvPopup(
					user_id,
					amount_usd,
					$('#delayed_ru').val());
				break;
			case "alf": openGenericPopup(
					'<?= AL_SERVICE_NAME ?>',
					'<?= AL_RUB_IN_ACCOUNT ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;
			case "ald": openGenericPopup(
					'<?= AL_SERVICE_NAME ?>',
					'<?= AL_USD_IN_ACCOUNT ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			case "wur": openGenericPopup(
					'<?= WU_SERVICE_NAME ?>',
					'<?= WU_RUB_IN_ACCOUNT ?>',
					'<?= WU_ACCOUNT_TYPE ?>',
					'<?= WU_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;
			case "wud": openGenericPopup(
					'<?= WU_SERVICE_NAME ?>',
					'<?= WU_USD_IN_ACCOUNT ?>',
					'<?= WU_ACCOUNT_TYPE ?>',
					'<?= WU_ACCOUNT_EXAMPLE ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			case "con": openGenericPopup(
					'<?= CON_SERVICE_NAME ?>',
					'<?= CON_RUB_IN_ACCOUNT ?>',
					'<?= CON_ACCOUNT_TYPE ?>',
					'<?= CON_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;
			case "cod": openGenericPopup(
					'<?= CON_SERVICE_NAME ?>',
					'<?= CON_USD_IN_ACCOUNT ?>',
					'<?= CON_ACCOUNT_TYPE ?>',
					'<?= CON_ACCOUNT_EXAMPLE ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			case "unr": openGenericPopup(
					'<?= UNI_SERVICE_NAME ?>',
					'<?= UNI_RUB_IN_ACCOUNT ?>',
					'<?= UNI_ACCOUNT_TYPE ?>',
					'<?= UNI_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;
			case "und": openGenericPopup(
					'<?= UNI_SERVICE_NAME ?>',
					'<?= UNI_USD_IN_ACCOUNT ?>',
					'<?= UNI_ACCOUNT_TYPE ?>',
					'<?= UNI_ACCOUNT_EXAMPLE ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			case "gcr": openGenericPopup(
					'<?= GC_SERVICE_NAME ?>',
					'<?= GC_RUB_IN_ACCOUNT ?>',
					'<?= GC_ACCOUNT_TYPE ?>',
					'<?= GC_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;
			case "gcd": openGenericPopup(
					'<?= GC_SERVICE_NAME ?>',
					'<?= GC_USD_IN_ACCOUNT ?>',
					'<?= GC_ACCOUNT_TYPE ?>',
					'<?= GC_ACCOUNT_EXAMPLE ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			case "anr": openGenericPopup(
					'<?= AN_SERVICE_NAME ?>',
					'<?= AN_RUB_IN_ACCOUNT ?>',
					'<?= AN_ACCOUNT_TYPE ?>',
					'<?= AN_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;
			case "and": openGenericPopup(
					'<?= AN_SERVICE_NAME ?>',
					'<?= AN_USD_IN_ACCOUNT ?>',
					'<?= AN_ACCOUNT_TYPE ?>',
					'<?= AN_ACCOUNT_EXAMPLE ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			/*case "pp": openGenericPopup(
					'<?= VM_SERVICE_NAME ?>',
					'<?= VM_RUB_IN_ACCOUNT ?>',
					'<?= VM_ACCOUNT_TYPE ?>',
					'<?= VM_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd);
				break;*/
			case "cus": openGenericPopup(
					'<?= CUS_SERVICE_NAME ?>',
					'',
					'<?= CUS_ACCOUNT_TYPE ?>',
					'<?= CUS_ACCOUNT_EXAMPLE ?>',
					service,
					0,
					$('#delayed_ru').val(),
					amount_usd);
				break;
			case "cuu": openGenericPopup(
					'<?= CUS_SERVICE_NAME ?>',
					'',
					'<?= CUS_ACCOUNT_TYPE ?>',
					'<?= CUS_ACCOUNT_EXAMPLE ?>',
					service,
					$('#delayed_ru').val(),
					0,
					amount_usd,
					'UAH');
				break;
		}
		
		return false;
	}

    function processPayment()
	{
		var payment_option = $('.payment_system input:radio').filter(':checked').attr('id');
        var service = getService(payment_option);

		switch (service)
		{
			case "bm":
				openO2iPopup('delayed_sb');
				break;
			case "sv":
			case "alf":
			case "wur":
			case "con":
			case "unr":
			case "und":
			case "gcr":
			case "anr":
			case "and":
			case "ald":
			case "wud":
			case "cod":
			case "gcd":
			case "cus":
			case "cuu":
				openO2iPopup('delayed_' + service);
				break;
			case "wmr":
			case "qw1":
			case "rk":
				$('.countrypost_payment_box form.immediate input.total_local').val($('div.delayed input:text').val());
				$('.countrypost_payment_box form.immediate').submit();
				break;
			case "wmz":
				$('.countrypost_payment_box form.usd input.total_local').val($('.user_total').val());
				$('.countrypost_payment_box form.usd input.payment_selector').val('wmz');
				$('.countrypost_payment_box form.usd').submit();
				break;
			case "pp":
				$('.countrypost_payment_box form.usd input.total_local').val($('.user_total').val());
				$('.countrypost_payment_box form.usd input.payment_selector').val('pp');
				$('.countrypost_payment_box form.usd').submit();
				break;
		}

		return false;
	}

	function redirect(target)
	{
		if (window.currentPay == 'bm')
		{
			target.action = '/client/addOrder2in/<?= $order->order_id ?>';
		}
		
		return true;
	}

	function switchPaymentSystem()
	{
		$('.payment_system label').removeClass('payment_selected');
		$(this).parent().parent().addClass('payment_selected');

		var total = $(this).next().html();
		$('div.countrypost_payment_box div.total b').html(total);

		$('#total_ru,#total_usd,#immediate_ru,#delayed_ru').val(total.substr(0, total.length - 4));

		// трюк с радио в 3х формах
		var boxes = '';

		switch ($(this).attr('rel'))
		{
			case 'delayed' :
				boxes = $('form.immediate,form.usd');
				break;
			case 'immediate' :
				boxes = $('div.delayed,form.usd');
				break;
			case 'usd' :
				boxes = $('form.immediate,div.delayed');
				break;
		}

		boxes
			.find('input:radio')
			.removeAttr('checked');
	}

	function initPaymentSystem()
	{
		var total = $('.payment_system input#delayed_sb').next().html();
		$('#total_ru,#total_usd,#immediate_ru,#delayed_ru').val(total.substr(0, total.length - 4));
	}

	function paymentSystemHover()
	{
		$(this).addClass('payment_hover');
	}

	function paymentSystemUnhover() {
		$(this).removeClass('payment_hover');
	}

	$(function() {
		// подсчет сумм
		$('.countrypost_payment_box .amount input')
			.keypress(function(event) {
				validate_number(event);
			})
			.bind('keypress keydown mouseup keyup blur', function() {
				calculateTotals();
				
				var payment_option = $('.payment_system input:radio').filter(':checked').attr('id');
				var service = getService(payment_option);
				if(service=='qw1')
					service='qw';
				else if(service=='bm')
					service='sb';
				else if(service=='rk')
					service='rbk';
				var total = $('div.total_' + service).html();
				$('div.countrypost_payment_box div.total b').html(total);

				$('#total_ru,#total_usd,#immediate_ru,#delayed_ru').val(total.substr(0, total.length - 4));
			});

		// переключение платежек
		$('.payment_system input:radio')
			.change(switchPaymentSystem);

		// подсветка
		$('div.payment_system label')
			.hover(paymentSystemHover, paymentSystemUnhover);

		// инициализация
		calculateTotals();
		initPaymentSystem();
	});
</script>
<? $i = 1; ?>
<div class='table countrypost_payment_box'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="payment_system">
		<div class="delayed">
			<div class="amount delayed">
				<span class="label">Сумма к оплате* :</span>
				<input type="text"
					   class="textbox user_total"
					   rel="delayed"
					   name="total_usd"
					   value="<?= $payable_amount ?>" >
				<b class="currency" style="left: 345px;">
					<?= $order->order_currency ?>
				</b>
				<? if ($order->excess_amount) : ?>
				<b class="excess_amount">
					+
					<?= $order->excess_amount ?>
					<?= $order->order_currency ?>
					<img class="tooltip"
						 src="/static/images/mini_help.gif"
						 title="Доступный остаток от предыдущих заказов">
					=
					<b class="total_local_amount">
						<?= $payable_amount + $order->excess_amount ?>
					</b>
					<?= $order->order_currency ?>
				</b>
				<? endif; ?>
				<input type="hidden" id="delayed_ru" value="" />
			</div>
			<div class="total">
				<span style="font-weight: bold;margin-left: 275px;">Итого к оплате: <b>0 RUB</b></span>
				<div>
					<div class='submit'>
						<div>
							<input type='button' onclick="processPayment();" value='Оплатить' >
						</div>
					</div>
				</div>
			</div>
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'sb',
				'service_code_usd' => '',
				'service_name' => BM_SERVICE_NAME,
				'title' => BM_SERVICE_DESCRIPTION,
				'image' => 'sberbank.png',
				'selected' => TRUE
			)); ?>
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'sv',
				'service_code_usd' => '',
				'service_name' => SV_SERVICE_NAME,
				'title' => SV_SERVICE_DESCRIPTION,
				'image' => 'sviaznoy.png',
				'selected' => FALSE
			)); ?>
			<!--? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'alf',
				'service_code_usd' => 'ald',
				'service_name' => AL_SERVICE_NAME,
				'title' => AL_SERVICE_DESCRIPTION,
				'image' => 'alfabank.png',
				'selected' => FALSE
			)); ?-->
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'pp',
				'service_code_usd' => '',
				'service_name' => PP_SERVICE_NAME,
				'title' => PP_SERVICE_DESCRIPTION,
				'image' => 'visa_mastercard.png',
				'selected' => FALSE
			)); ?>
		</div>
		<form method="POST" action="/syspay/showGate/<?= $order->order_id ?>" class="usd">
			<input type="hidden" name="section" value="usd">
			<input type="hidden" name="total_usd" id="total_usd">
			<input type="hidden" name="total_local" class="total_local" value="">
			<input type="hidden" name="payment_selector" class="payment_selector" value="wmz">
		</form>
		<form method="POST" action="/syspay/showGate/<?= $order->order_id ?>" class="immediate">
			<input type="hidden" name="section" value="immediate">
			<input type="hidden" name="total_ru" id="immediate_ru" value="">
			<input type="hidden" name="total_local" class="total_local" value="">
			<div class="immediate">
				<div title="<?= QW_SERVICE_DESCRIPTION ?>"
					 class="payment_type">
					<label for="immediate_qw">
						<img src="/static/images/qiwi.png" />
						<br style="clear: both;">
						<span>
							<input type="radio"
							   id="immediate_qw"
							   value="qw"
							   name="payment_selector"
							   rel="immediate" />
							<div class="payment_system_name totals total_qw">
							</div>
						</span>
					</label>
				</div>
				<br style="clear: both;">
				<div title="<?= WM_SERVICE_DESCRIPTION ?>"
					 class="payment_type">
					<label for="immediate_wmr">
						<img src="/static/images/wmr.png" />
						<br style="clear: both;">
						<span>
							<input type="radio"
								   id="immediate_wmr"
								   value="wmr"
								   name="payment_selector"
								   rel="immediate" />
							<div class="payment_system_name totals total_wmr">
							</div>
							<br style="clear: both;">
							<input type="radio"
								   id="immediate_wmz"
								   value="wmz"
								   name="payment_selector"
								   rel="immediate"/>
							<div class="payment_system_name totals total_wmz"></div>
						</span>
					</label>
				</div>
				<div title="<?= RK_SERVICE_DESCRIPTION ?>"
					 class="payment_type">
					<label for="immediate_rbk">
						<img src="/static/images/robokassa.png" />
						<br style="clear: both;">
						<span>
							<input type="radio"
								   id="immediate_rbk"
								   value="rbk"
								   name="payment_selector"
								   rel="immediate" />
							<div class="payment_system_name totals total_rbk"></div>
						</span>
					</label>
				</div>
			</div>
		</form>
		<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'wur',
				'service_code_usd' => 'wud',
				'service_name' => WU_SERVICE_NAME,
				'title' => WU_SERVICE_DESCRIPTION,
				'image' => 'western_union.png',
				'selected' => FALSE
			)); ?>
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'con',
				'service_code_usd' => 'cod',
				'service_name' => CON_SERVICE_NAME,
				'title' => CON_SERVICE_DESCRIPTION,
				'image' => 'contact.png',
				'selected' => FALSE
			)); ?>
			<br style="clear: both;">
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'unr',
				'service_code_usd' => 'und',
				'service_name' => UNI_SERVICE_NAME,
				'title' => UNI_SERVICE_DESCRIPTION,
				'image' => 'unistream.png',
				'selected' => FALSE
			)); ?>
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'gcr',
				'service_code_usd' => 'gcd',
				'service_name' => GC_SERVICE_NAME,
				'title' => GC_SERVICE_DESCRIPTION,
				'image' => 'golden_crown.png',
				'selected' => FALSE
			)); ?>
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'anr',
				'service_code_usd' => 'and',
				'service_name' => AN_SERVICE_NAME,
				'title' => AN_SERVICE_DESCRIPTION,
				'image' => 'anelik.png',
				'selected' => FALSE
			)); ?>
			<? View::show('/syspay/elements/generic_o2i', array(
				'service_code' => 'cuu',
				'service_code_usd' => 'cus',
				'service_name' => CUS_SERVICE_NAME,
				'title' => CUS_SERVICE_DESCRIPTION,
				'image' => 'cash_usd.png',
				'selected' => FALSE
			)); ?>
	</div>
</div>