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
            case "vm": tax = <?= RK_RUB_IN_TAX ?>; break;
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
			case "immediate_wmz" : service = "wmz"; break;
			case "immediate_vi" : service = "rk"; break;
			case "immediate_qw" : service = "qw1"; break;
			case "immediate_ya" : service = "rk"; break;
			case "immediate_mi" : service = "rk"; break;
			case "immediate_ek" : service = "rk"; break;
			case "immediate_rbk" : service = "rk"; break;
			case "immediate_mm" : service = "rk"; break;
			case "immediate_mr" : service = "rk"; break;
			case "immediate_zp" : service = "rk"; break;
			case "immediate_ca" : service = "rk"; break;
			case "usd_wmz" : service = "wmz"; break;
			case "delayed_sb" : service = "bm"; break;
			case "delayed_vm" : service = "vm"; break;
			case "delayed_qw" : service = "qw"; break;
			case "delayed_rp" : service = "rbk"; break;
			case "delayed_co" : service = "rbk"; break;
			case "delayed_gc" : service = "rbk"; break;
			case "delayed_li" : service = "rbk"; break;
			case "delayed_rbk" : service = "rbk"; break;
			case "delayed_mts" : service = "rbk"; break;
			case "delayed_me" : service = "rbk"; break;
			case "delayed_es" : service = "rbk"; break;
			case "delayed_sv" : service = "sv"; break;
			case "delayed_at" : service = "rbk"; break;
			case "delayed_ib" : service = "rbk"; break;
			case "delayed_tm" : service = "rbk"; break;
			case "delayed_vtb" : service = "vtb"; break;
			case "paypal" : service = "pp"; break;
            case "uah_pb" : service = "pb"; break;
            case "kzt_bta" : service = "bta"; break;
            case "kzt_ccr" : service = "ccr"; break;
            case "kzt_kkb" : service = "kkb"; break;
            case "kzt_nb" : service = "nb"; break;
            case "kzt_tb" : service = "tb"; break;
            case "kzt_atf" : service = "atf"; break;
            case "kzt_ab" : service = "ab"; break;
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

	function calculateTotals(section) 
	{
		if (section)
		{
			calculateTotal(section);
		}
		else
		{
			calculateTotal('usd');
			calculateTotal('immediate');
			calculateTotal('delayed');
		}
	}
	
	function calculateTotal(id) 
	{
		updateExcessAmount(id);

		if (id == 'usd')
		{
			calculateTotalUSD(id);
		} 
		else
		{
			calculateTotalRUB(id);
		}
	}
	
	function calculateTotalRUB(id)
	{			
		var payment_option = $('.' + id + ' input:radio').filter(':checked').attr('id');
		var service = getService(payment_option);
		var percentage = getTax(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_rur;

		var ru_amount = Math.ceil(amount + percentage * amount * 0.01);
		
		$('#' + id + '_ru').val(ru_amount);
		$('.delayed .total b').html(ru_amount + ' RUB');
	}

	function calculateTotalUSD(id) 
	{			
		var payment_option = $('.' + id + ' input:radio').filter(':checked').attr('id');
		var service = getService(payment_option);
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_usd;

		var total = Math.ceil(amount + percentage*amount*0.01 + extra);
		
		$('.' + id + ' #total_usd').val(total);
		$('.delayed .total b').html(total + ' USD');
	}

    function openO2iPopup(x)
	{
		var payment_option = $('.' + x + ' input:radio').filter(':checked').attr('id');
        var service = getService(payment_option);
		var amount_usd = $('.' + x + ' input:text').val();
		var user_id = '<?= isset($user->user_id) ? $user->user_id : '' ?>';

		switch (service) 
		{
			case "bm": openSberbankPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
			case "sv": openSvPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
		}
		
		return false;
	}

    function processPayment()
	{
		var payment_option = $('.payment_system input:radio').filter(':checked:first').attr('id');
        var service = getService(payment_option);
		var amount_usd = $('.payment_system input:text').val();
		var user_id = '<?= isset($user->user_id) ? $user->user_id : '' ?>';

		switch (service)
		{
			case "bm": openSberbankPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
			case "sv": openSvPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
			case "vm": openVMPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
			case "vm": openVMPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
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
	
	$(function() {
		// подсчет сумм
		$('.amount input')
			.keypress(function(event) {
				validate_number(event);
			})
			.bind('keypress keydown mouseup keyup blur', function() {
				calculateTotals($(this).attr('rel'));
			});
			
		// переключение платежек
		$('.payment_system input:radio').change(function() {
			calculateTotals($(this).attr('rel'));
		});

		// переключение валют
		calculateTotals();
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
				<span>Сумма к оплате* :</span>
				<input type="text"
					   rel="delayed"
					   name="total_usd"
					   value="<?= $payable_amount ?>" >
				<b class="currency">
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
				<span>Итого к оплате: <b>0 руб.</b></span>
				<div>
					<div class='submit'>
						<div>
							<input type='button' onclick="processPayment();" value='Оплатить' >
						</div>
					</div>
				</div>
			</div>
			<div>
				<input type="radio"
					   rel="delayed"
					   id="delayed_sv"
					   name="payment_selector" checked />
				<label for="delayed_sv">
					<img src="/static/images/sviaznoy.png" style="margin-right: 2px;" />
					<div class="payment_system_name">
						Оплата переводом с карты на карту через <?= SV_SERVICE_NAME ?>
					</div>
				</label>
			</div>
			<div>
				<input type="radio"
					   rel="delayed"
					   id="delayed_sb"
					   name="payment_selector" />
				<label for="delayed_sb">
					<img src="/static/images/sberbank.png" />
					<div class="payment_system_name">
						Оплата переводом с карты на карту через Сбербанк (Россия)
					</div>
				</label>
			</div>
		</div>
		<form method="POST" action="/syspay/showGate" class="immediate">
			<input type="hidden" name="section" value="immediate">
			<input type="hidden" name="total_ru" id="immediate_ru" value="">
			<div class="immediate">
				<div>
					<input type="radio"
						   id="immediate_wmr"
						   value="wmr"
						   name="payment_selector"
						   rel="immediate" />
					<label for="immediate_wmr">
						<img src="/static/images/wmr.png" style="margin-left: 6px;margin-right:6px;" />
						<div class="payment_system_name">
							Оплата через webmoney (WMR)
						</div>
					</label>
				</div>
				<div>
					<input type="radio"
						   id="immediate_qw"
						   value="qw"
						   name="payment_selector"
						   rel="immediate" />
					<label for="immediate_qw">
						<img src="/static/images/qiwi.png" />
						<div class="payment_system_name">
							Оплата через Qiwi кошелек
						</div>
					</label>
				</div>
				<div>
					<input type="radio"
						   id="immediate_rbk"
						   value="rbk"
						   name="payment_selector"
						   rel="immediate" />
					<label for="immediate_rbk">

						<img src="/static/images/robokassa.png" />
						<div class="payment_system_name">
							Оплата через платежную систему Robokassa
						</div>
					</label>
				</div>
			</div>
		</form>
		<form method="POST" action="/syspay/showGate" class="usd">
			<input type="hidden" name="section" value="usd">
			<input type="hidden" name="total_usd" id="total_usd">
			<div style="height: 40px;">
				<input type="radio"
					   id="usd_wmz"
					   value="wmz"
					   name="payment_selector"
					   rel="usd"/>
				<label for="usd_wmz">
					<img src="/static/images/wmz.png" style="margin-left: 8px!important;" />
					<div class="payment_system_name">
						Оплата через webmoney (WMZ)
					</div>
				</label>
			</div>
		</form>
	</div>
</div>