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
			case "w1": tax = <?= W1_IN_TAX ?>; break;
			case "bm": tax = <?= BM_IN_TAX ?>; break;
			case "sb": tax = <?= BM_IN_TAX ?>; break;
			case "rbk": tax = <?= RBK_IN_TAX ?>; break;
			case "qw": tax = <?= QW_IN_TAX ?>; break;
			case "sv": tax = <?= SV_IN_TAX ?>; break;
        }
		
		return tax;
	}
	
	function getExtra(service)
	{
		var extra = 0;
	
		switch (service) 
		{
			case "wmz": extra = <?= WMZ_IN_EXTRA ?>; break;
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

	function calculateTotals()
	{
		calculateTotal('sv');
		calculateTotal('sb');
		calculateTotal('wmr');
		calculateTotal('qw');
		calculateTotal('wmz');
		calculateTotal('rbk');
	}
	
	function calculateTotal(service)
	{
		updateExcessAmount('countrypost_payment_box');

		if (service == 'sv' ||
			service == 'sb' ||
			service == 'qw' ||
			service == 'wmr' ||
			service == 'rbk')
		{
			calculateTotalRUB(service);
		} 
		else
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
		
		$('.delayed .total b, div.total_' + service).html(ru_amount + ' RUB');
	}

	function calculateTotalUSD(service)
	{
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.delayed .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_usd;

		var total = Math.ceil(amount + percentage * amount * 0.01 + extra);
		
		$('.delayed .total b, div.total_' + service).html(total + ' USD');
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
		$('.payment_system input:radio').change(function(e) {
			$('.payment_system label').removeClass('payment_selected');
			$(this).parent().addClass('payment_selected');

			var total = $(this).parent().find('div.totals').html();
			$('div.countrypost_payment_box div.total b').html(total);

			$('#total_ru,#total_usd,#immediate_ru,#delayed_ru').val(total.substr(0, total.length - 4));
		});

		// подсветка
		$('div.payment_system label')
			.hover(function() {
				$(this).addClass('payment_hover');
			},
			function() {
				$(this).removeClass('payment_hover');
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
				<span class="label">Сумма к оплате* :</span>
				<input type="text"
					   class="textbox"
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
				<span>Итого к оплате: <b style="margin-left: 82px;">0 RUB</b></span>
				<div>
					<div class='submit'>
						<div>
							<input type='button' onclick="processPayment();" value='Оплатить' >
						</div>
					</div>
				</div>
			</div>
			<div title="Оплата переводом с карты на карту через <?= SV_SERVICE_NAME ?>"
				 class="payment_type"
				 style="margin-left: 190px;">
				<label for="delayed_sv" class="payment_selected">
					<input type="radio"
						   rel="delayed"
						   id="delayed_sv"
						   name="payment_selector" checked />
					<img src="/static/images/sviaznoy.png" />
					<div class="payment_system_name totals total_sv">
						12345 RUB
					</div>
				</label>
			</div>
			<div title="Оплата переводом с карты на карту через Сбербанк (Россия)"
				 class="payment_type">
				<label for="delayed_sb">
					<input type="radio"
						   rel="delayed"
						   id="delayed_sb"
						   name="payment_selector" />
					<img src="/static/images/sberbank.png" />
					<div class="payment_system_name totals total_sb">
						12345 RUB
					</div>
				</label>
			</div>
		</div>
		<form method="POST" action="/syspay/showGate" class="immediate">
			<input type="hidden" name="section" value="immediate">
			<input type="hidden" name="total_ru" id="immediate_ru" value="">
			<div class="immediate">
				<div title="Оплата через webmoney (WMR)"
					 class="payment_type">
					<label for="immediate_wmr">
						<input type="radio"
							   id="immediate_wmr"
							   value="wmr"
							   name="payment_selector"
							   rel="immediate" />
						<img src="/static/images/wmr.png" />
						<div class="payment_system_name totals total_wmr">
							12345 RUB
						</div>
					</label>
				</div>
				<br style="clear: both">
				<div title="Оплата через Qiwi кошелек"
					 class="payment_type"
					 style="margin-left: 190px;">
					<label for="immediate_qw">
						<input type="radio"
							   id="immediate_qw"
							   value="qw"
							   name="payment_selector"
							   rel="immediate" />
						<img src="/static/images/qiwi.png" />
						<div class="payment_system_name totals total_qw">
							12345 RUB
						</div>
					</label>
				</div>
				<div title="Оплата через платежную систему Robokassa"
					 class="payment_type">
					<label for="immediate_rbk">
						<input type="radio"
							   id="immediate_rbk"
							   value="rbk"
							   name="payment_selector"
							   rel="immediate" />
						<img src="/static/images/robokassa.png" />
						<div class="payment_system_name totals total_rbk">
							12345 RUB
						</div>
					</label>
				</div>
			</div>
		</form>
		<form method="POST" action="/syspay/showGate" class="usd">
			<input type="hidden" name="section" value="usd">
			<input type="hidden" name="total_usd" id="total_usd">
			<div title="Оплата через webmoney (WMZ)"
				 class="payment_type">
				<label for="usd_wmz">
					<input type="radio"
						   id="usd_wmz"
						   value="wmz"
						   name="payment_selector"
						   rel="usd"/>
					<img src="/static/images/wmz.png" />
					<div class="payment_system_name totals total_wmz">
						12345 RUB
					</div>
				</label>
			</div>
		</form>
	</div>
</div>