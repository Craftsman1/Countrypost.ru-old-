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
			case "delayed_vi" : service = "rbk"; break;
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
			calculateTotal('immediate');
			calculateTotal('delayed');
			calculateTotal('usd');
            calculateTotal('kzt');
            calculateTotal('uah');
		}
	}
	
	function calculateTotal(id) 
	{
		updateExcessAmount(id);

		if (id == 'usd')
		{
			calculateTotalUSD(id);
		} 
		else if (id == 'kzt') 
		{
            calculateTotalKZT(id);
		} 
		else if (id == 'uah') 
		{
            calculateTotalUAH(id);
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

		var amount = $('.' + id + ' .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_rur;

		var ru_amount = Math.ceil(amount + percentage * amount * 0.01);
		
		$('#' + id + '_ru').val(ru_amount);
		$('.' + id + ' .total b').html(ru_amount + ' RUB');
	}

	function calculateTotalUSD(id) 
	{			
		var payment_option = $('.' + id + ' input:radio').filter(':checked').attr('id');
		var service = getService(payment_option);
		var percentage = getTax(service);
		var extra = getExtra(service);

		var amount = $('.' + id + ' .amount input:text').val();
		amount = parseFloat(amount);
		amount = (isNaN(amount) ? 0 : amount) * rate_usd;

		var total = Math.ceil(amount + percentage*amount*0.01 + extra);
		
		$('.' + id + ' #total_usd').val(total);
		$('.' + id + ' .total b').html(total + ' USD');
	}

    function calculateTotalKZT(id) 
    {            
        var payment_option = $('.' + id + ' input:radio').filter(':checked').attr('id');
        var service = getService(payment_option);
        var percentage = getTax(service);
        var extra = getExtra(service);

        var amount = $('.' + id + ' .amount input:text').val();
        amount = parseFloat(amount);
        amount = (isNaN(amount) ? 0 : amount) * rate_kzt;

        var total = Math.ceil(amount + percentage*amount*0.01 + extra);
        
        $('.' + id + ' #total_kzt').val(total);
        $('.' + id + ' .total b').html(total + ' KZT');
        $('#delayed_kzt').val(total);
    }
	
    function calculateTotalUAH(id) 
    {
        var payment_option = $('.' + id + ' input:radio').filter(':checked').attr('id');
		var service = getService(payment_option);
        var percentage = getTax(service);
        var extra = getExtra(service);

        var amount = $('.' + id + ' .amount input:text').val();
        amount = parseFloat(amount);
        amount = (isNaN(amount) ? 0 : amount) * rate_uah;

        var total = Math.ceil(amount + percentage*amount*0.01 + extra);
        
        $('.' + id + ' #total_uah').val(total);
        $('.' + id + ' .total b').html(total + ' UAH');
        $('#uah').val(total);
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
			case "rbk": openRbkPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
			case "qw": openQiwiPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
            case "bta": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "ccr": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "kkb": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "nb": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "tb": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "atf": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "ab": openKZTPopup(user_id, amount_usd, $('#delayed_kzt').val(), service); break;
            case "pb": openPbPopup(user_id, amount_usd, $('#uah').val()); break;
			case "sv": openSvPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
			case "vtb": openVTBPopup(user_id, amount_usd, $('#delayed_ru').val()); break;
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
			calculateTotals($(this).attr('name'));
		});

		// переключение валют
		$('select.currency_selector').change(function() {
			var usd_amount = $(this).parent().parent().find('div.amount input:text').val();
			
			switch ($(this).val())
			{
				case "rur": 
					$('.usd').hide();
					$('.kzt').hide();
					$('.uah').hide();
					$('.rouble div.amount input:text').val(usd_amount);
					$('.rouble')
						.show()
						.find('select.currency_selector')
						.val('rur');
					break;
				case "usd": 
					$('.rouble').hide();
					$('.kzt').hide();
					$('.uah').hide();
					$('.usd div.amount input:text').val(usd_amount);
					$('.usd')
						.show()
						.find('select.currency_selector')
						.val('usd');
					break;
				case "kzt": 
					$('.rouble').hide();
					$('.usd').hide();
					$('.uah').hide();
					$('.kzt div.amount input:text').val(usd_amount);
					$('.kzt')
						.show()
						.find('select.currency_selector')
						.val('kzt');
					break;
				case "uah": 
					$('.rouble').hide();
					$('.usd').hide();
					$('.kzt').hide();
					$('.uah div.amount input:text').val(usd_amount);
					$('.uah')
						.show()
						.find('select.currency_selector')
						.val('uah');
					break;
			}
			
			calculateTotals();
		});

		// переключение скорости оплаты
		$('select.speed_selector').change(function() 
		{
			var usd_amount = $(this).parent().parent().find('div.amount input:text').val();
			
			switch ($(this).val())
			{
				case "immediate": 
					$('.delayed').hide();
					$('.immediate div.amount input:text').val(usd_amount);
					$('.immediate')
						.show()
						.find('select.speed_selector')
						.val('immediate');
					break;
				case "delayed": 
					$('.immediate').hide();
					$('.delayed div.amount input:text').val(usd_amount);
					$('.delayed')
						.show()
						.find('select.speed_selector')
						.val('delayed');
					break;
			}
			
			calculateTotals();
		});

		calculateTotalRUB('immediate');
	});
</script>
<div class='table countrypost_payment_box'>
	<div class='angle angle-lt'></div>
	<div class='angle angle-rt'></div>
	<div class='angle angle-lb'></div>
	<div class='angle angle-rb'></div>
	<div class="rouble payment_system" style="display:block;">
		<form method="POST" action="/syspay/showGate">
			<input type="hidden" name="section" value="immediate">
			<input type="hidden" name="total_ru" id="immediate_ru" value="">
			<div class="immediate" style="display:block;">
				<div class="amount" style="display:block;">
					<span>Сумма к оплате* :</span>
					<input
						type="text"
						rel="immediate"
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
				</div>
				<div class="amount" style="display:block;">
					<span>Выберите валюту, которой будeте оплачивать:</span>
					<select class="currency_selector">
						<option value="rur" selected>Российский рубль</option>
						<option value="usd">Доллар США</option>
						<option value="kzt">Казахстанский тенге</option>
						<option value="uah">Украинская гривна</option>
					</select>
				</div>
				<div class="amount" style="display:block;">
					<span>Как пополнить:</span>
					<select class="speed_selector">
						<option value="immediate" selected>Моментально</option>
						<option value="delayed">1-2 дня</option>
					</select>
				</div>
				<div>
					<input type="radio" id="immediate_vi" value="vi" name="immediate" checked />
					<label for="immediate_vi">
						<span>1.</span>
						<img src="/static/images/visa_mastercard.png" style="margin-left: 7px;" />
						<div>
							Оплата банковской картой
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_wmr" value="wmr" name="immediate" />
					<label for="immediate_wmr">
						<span>2.</span>
						<img src="/static/images/wmr.png" style="margin-left: 6px;margin-right:6px;" />
						<div>
							Оплата через webmoney (WMR)
							<br />
							Комиссия <?= WM_IN_TAX ?>%
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_ya" value="ya" name="immediate" />
					<label for="immediate_ya">
						<span>3.</span>
						<img src="/static/images/yandex.png" />
						<div>
							Оплата через Яндекс.Деньги
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_qw" value="qw" name="immediate" />
					<label for="immediate_qw">
						<span>4.</span>
						<img src="/static/images/qiwi.png" />
						<div>
							Оплата через Qiwi кошелек
							<br />
							Комиссия <?= QIWI_IN_TAX ?>%
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_ek" value="ek" name="immediate" />
					<label for="immediate_ek">
						<span>5.</span>
						<img src="/static/images/ek.png" />
						<div>
							Оплата через Единый Кошелек
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_rbk" value="rbk" name="immediate" />
					<label for="immediate_rbk">
						<span>6.</span>
						<img src="/static/images/rbk.png" />
						<div>
							Оплата через RBK Money
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_mm" value="mm" name="immediate" />
					<label for="immediate_mm">
						<span>7.</span>
						<img src="/static/images/money_mail.png" />
						<div>
							Оплата через MoneyMail RUB
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_mr" value="mr" name="immediate" />
					<label for="immediate_mr">
						<span>8.</span>
						<img src="/static/images/mail_ru.png" />
						<div>
							Оплата через Деньги@mail.ru
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_zp" value="zp" name="immediate" />
					<label for="immediate_zp">
						<span>9.</span>
						<img src="/static/images/zpayment.png" />
						<div>
							Оплата через Z-Payment
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
						</div>
					</label>
				</div>
				<div>
					<input type="radio" id="immediate_ca" value="ca" name="immediate" />
					<label for="immediate_ca">
						<span>10.</span>
						<img src="/static/images/terminal.png" />
						<div>
							Оплата наличными через денежные переводы и терминалы России и Украины.
							<br />
							Комиссия <?= RK_IN_TAX ?>% + комиссия робокассы
							<br />
							<a target="_blank" href="http://merchant.w1.ru/checkout/site/payments/#terminals">подробнее</a>
						</div>
					</label>
				</div>
				<div class="total">
					<br />
					<span>Итого к оплате: <b>0 руб.</b></span>
					<div>	
						<div class='submit'>
							<div>
								<input type='submit' onclick="" value='Оплатить' />
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<div class="delayed">
			<div class="amount delayed" style="display:none;">
				<span>Сумма к оплате* :</span>
				<input type="text" rel="delayed" name="total_usd" value=""/>
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
			<div class="amount delayed" style="display:none;">
				<span>Выберите валюту, которой будeте оплачивать:</span>
				<select class="currency_selector">
					<option value="rur" selected>Российский рубль</option>
					<option value="usd">Доллар США</option>
					<option value="kzt">Казахстанский тенге</option>
					<option value="uah">Украинская гривна</option>
				</select>
			</div>
			<div class="amount">
				<span>Как пополнить:</span>
				<select class="speed_selector">
					<option value="immediate">Моментально</option>
					<option value="delayed" selected>1-2 дня</option>
				</select>
			</div>
			<div>
				<input type="radio" rel="delayed" id="delayed_qw" name="delayed" checked />
				<label for="delayed_qw">
					<span>1.</span>
					<img src="/static/images/qiwi.png" />
					<div>
						Оплата через кошелек и терминалы Qiwi
						<br />
						Комиссия <?= QW_IN_TAX ?>%
					</div>
				</label>
			</div>
			<div>
				<input type="radio" rel="delayed" id="delayed_sb" name="delayed" />
				<label for="delayed_sb">
					<span>2.</span>
					<img src="/static/images/sberbank.png" />
					<div>
						Оплата переводом с карты на карту через Сбербанк
						<br />
						Комиссия <?= BM_IN_TAX ?>%
					</div>
				</label>
			</div>
			<div>
				<input type="radio" rel="delayed" id="delayed_sv" name="delayed" />
				<label for="delayed_sv">
					<span>3.</span>
					<img src="/static/images/sviaznoy.png" style="margin-right: 2px;" />
					<div>
						Оплата переводом с карты на карту через <?= SV_SERVICE_NAME ?>
						<br />
						Комиссия <?= SV_IN_TAX ?>%
					</div>
				</label>
			</div>
			<div>
				<input type="radio" rel="delayed" id="delayed_vtb" name="delayed" />
				<label for="delayed_vtb">
					<span>4.</span>
					<img src="/static/images/vtb.png" style="margin-right: 2px;" />
					<div>
						Оплата переводом с карты на карту через <?= VTB_SERVICE_NAME ?>
						<br />
						Комиссия <?= VTB_IN_TAX ?>%
					</div>
				</label>
			</div>
			<div class="total">
				<span>Итого к оплате: <b>0 руб.</b></span>
				<div>	
					<div class='submit'>
						<div>
							<input type='button' onclick="openO2iPopup('delayed');" value='Оплатить' />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="usd payment_system" style="display:none;">
		<form method="POST" action="/syspay/showGate">
			<input type="hidden" name="section" value="usd">
			<input type="hidden" name="total_usd" id="total_usd">
			<div class="amount">
				<span>Сумма к оплате* :</span>
				<input type="text" rel="usd" name="total_ru" value=""/>
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
			</div>
			<div class="amount">
				<span>Выберите валюту, которой будeте оплачивать:</span>
				<select class="currency_selector">
					<option value="rur">Российский рубль</option>
					<option value="usd" selected>Доллар США</option>
					<option value="kzt">Казахстанский тенге</option>
					<option value="uah">Украинская гривна</option>
				</select>
			</div>
			<div>
				<input type="radio" id="usd_wmz" value="wmz" name="usd" checked />
				<label for="usd_wmz">
					<span>1.</span>
					<img src="/static/images/wmz.png" style="margin-left: 8px!important;" />
					<div>
						Оплата через webmoney (WMZ)
						<br />
						Комиссия 1.6% + 2$
					</div>
				</label>
			</div>
			<div>
				<input type="radio" id="paypal" name="usd" value="pp" />
				<label for="paypal">
					<span>2.</span>
					<img  src="/static/images/paypal.png" border="0" alt="PayPal" style="margin-left: 8px!important;" >
					<div>
						Оплата через PayPal
						<br />
						Комиссия <?= PP_IN_TAX ?>% + <?= PP_IN_EXTRA ?>$
					</div>
				</label>
			</div>
			<div class="total">
				<span>Итого к оплате: <b>$0</b></span>
				<div>	
					<div class='submit'>
						<div>
							<input type='submit' value='Оплатить' />
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="kzt payment_system" style="display:none;">
		<form method="POST" action="/syspay/showGate">
		<input type="hidden" name="section" value="kzt">
		<input type="hidden" name="total_kzt" id="delayed_kzt" value="">
		<div class="amount">
			<span>Сумма к оплате* :</span>
			<input type="text" rel="kzt" name="total_kzt" value="1"/>
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
		</div>
		<div class="amount">
			<span>Выберите валюту, которой будeте оплачивать:</span>
			<select class="currency_selector">
				<option value="rur">Российский рубль</option>
				<option value="usd">Доллар США</option>
				<option value="kzt" selected>Казахстанский тенге</option>
				<option value="uah">Украинская гривна</option>
			</select>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_bta" name="delayed" checked />
			<label for="kzt_bta">
				<span>1.</span>
				<img src="/static/images/btabank.png" />
				<div>
					Оплата переводом с карты на карту через <?= BTA_SERVICE_NAME ?>
					<br />
					Комиссия <?= BTA_IN_TAX ?>%
				</div>
			</label>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_ccr" name="delayed" />
			<label for="kzt_ccr">
				<span>2.</span>
				<img src="/static/images/ckbank.png" />
				<div>
					Оплата переводом с карты на карту через <?= CCR_SERVICE_NAME ?>
					<br />
					Комиссия <?= CCR_IN_TAX ?>%
				</div>
			</label>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_kkb" name="delayed" />
			<label for="kzt_kkb">
				<span>3.</span>
				<img src="/static/images/kcbank.png" />
				<div>
					Оплата переводом с карты на карту через <?= KKB_SERVICE_NAME ?>
					<br />
					Комиссия <?= KKB_IN_TAX ?>%
				</div>
			</label>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_nb" name="delayed" />
			<label for="kzt_nb">
				<span>4.</span>
				<img src="/static/images/nbank.png" />
				<div>
					Оплата переводом с карты на карту через <?= NB_SERVICE_NAME ?>
					<br />
					Комиссия <?= NB_IN_TAX ?>%
				</div>
			</label>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_tb" name="delayed" />
			<label for="kzt_tb">
				<span>5.</span>
				<img src="/static/images/tbank.png" />
				<div>
					Оплата переводом с карты на карту через <?= TB_SERVICE_NAME ?>
					<br />
					Комиссия <?= TB_IN_TAX ?>%
				</div>
			</label>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_atf" name="delayed" />
			<label for="kzt_atf">
				<span>6.</span>
				<img src="/static/images/atfbank.png" />
				<div>
					Оплата переводом с карты на карту через <?= ATF_SERVICE_NAME ?>
					<br />
					Комиссия <?= ATF_IN_TAX ?>%
				</div>
			</label>
		</div>
		<div>
			<input type="radio" rel="kzt" id="kzt_ab" name="delayed" />
			<label for="kzt_ab">
				<span>7.</span>
				<img src="/static/images/albank.png" />
				<div>
					Оплата переводом с карты на карту через <?= AB_SERVICE_NAME ?>
					<br />
					Комиссия <?= AB_IN_TAX ?>%
				</div>
			</label>
		</div>

		<div class="total">
			<span>Итого к оплате: <b>0 тенге.</b></span>
			<br />
			<div>    
				<div class='submit'>
					<div><input type='button' onclick="openO2iPopup('kzt');" value='Оплатить' /></div>
				</div>
			</div>
		</div>
		</form>
	</div>
	<div class="uah payment_system">
		<div class="amount uah" style="display:none;">
			<span>Сумма к оплате* :</span>
			<input type="text" rel="uah" name="total_usd" value=""/>
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
			<input type="hidden" id="uah" value="" />
		</div>
		<div class="amount uah" style="display:none;">
			<span>Выберите валюту, которой будeте оплачивать:</span>
			<select class="currency_selector">
				<option value="rur">Российский рубль</option>
				<option value="usd">Доллар США</option>
				<option value="kzt">Казахстанский тенге</option>
				<option value="uah" selected>Украинская гривна</option>
			</select>
		</div>
		<div>
			<input type="radio" rel="uah" id="uah_pb" name="uah" checked />
			<label for="uah_pb">
				<span>1.</span>
				<img src="/static/images/privatbank.png" />
				<div>
					Приватбанк
					<br />
					Комиссия <?= PB_IN_TAX ?>%
				</div>
			</label>
		</div>
		
		<div class="total">
			<span>Итого к оплате: <b>0 руб.</b></span>
			<div>	
				<div class='submit'>
					<div>
						<input type='button' onclick="openO2iPopup('uah');" value='Оплатить' />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>