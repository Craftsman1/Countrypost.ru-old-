<script type="text/javascript">
	var services = {0:'wm',1:'lp',2:'qw',3:'bm'};
	
	var loadMount = function(){
		var buk = "<?=$usd?>"; // Курс бакса.
		var ourPercent = getPercentByPayment($('#payment_service option:selected').val()); /* наши кровные :) */
		var val = $('#ammount').val();
		if (val.indexOf('.') > -1) $('#ammount').val(parseInt(val));
		val = parseInt(val);
		val = (isNaN(val) ? 0 : val)*parseFloat(buk);
		val = Math.ceil(val - ourPercent*val/100);
		$('#total_amount_manual').text('Итого: ' + val + ' руб.');
		$('#hidden_ammount').val(val);
	}

	function getPercentByPayment(pay){
		var percent = 0;
		switch (pay) {
			case "wm": percent = "<?=WM_OUT_TAX?>"; break;
			case "qw": percent = "<?=QW_OUT_TAX?>"; break;
			case "lp": percent = "<?=LP_OUT_TAX?>"; break;
			case "bm": percent = "<?=BM_OUT_TAX?>"; break;
		}
		return parseFloat(percent);
	}
	
	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить заявку №" + id + "?")){
			window.location.href = '<?=$selfurl?>deleteOrder2out/' + id;
		}
	}

	function updatePaymentService()
	{
		loadMount();
		
		$('#payment_service').find('option:selected').each(function () {
				$('div.wm,div.qw,div.lp,div.bm').slideUp('fast');
				$('div.' + $(this).val()).slideDown('fast');
		    });
	}
	
	$(document).ready(function(){
		$('#payment_service').change(function(){
				updatePaymentService();
			});
		
		updatePaymentService();
		window.loadMount();
		
		$('#ammount').bind('keypress keydown mouseup keyup blur', function(){
			loadMount();
		});		
	});
</script>
<div class='content'>
	<form class='admin-inside' action="<?=$selfurl?>order2out" method='POST'>
		<input type='hidden' name='ammount' id='hidden_ammount' value=""/>
		
		<div class='table manual request_box' style="margin:0 20px;margin-left:168px;position:relative;background:#fff;">
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			
			<h3 style="margin-left:193px">Вывод денег</h3>
			<p style="margin-left:140px">Вывод денег со счета осуществляется в рублях.</p>
			<?if(isset($result->m) && $result->m):?><em style="color:red;margin-left:0;"><?=$result->m?></em><br/><?endif;?>
			<? if (isset($result->order_details) && $result->order_details) : $order = $result->order_details; endif; ?>
			<div class='field done'>
				<div class='field done'>
					<span>Способ вывода:</span>
					<select class="select" name="payment_service" id="payment_service">
						<? foreach ($services as $service) : 
							$tax = strtoupper($service->payment_service_id).'_OUT_TAX'; ?>
						<option value="<?=$service->payment_service_id?>" <? if (isset($order) && $order->payment_service == $service->payment_service_id) : ?>selected<?endif;?>><?=$service->payment_service_outprompt?> (<?=constant($tax)?>%)</option>
						<? endforeach; ?>
					</select>
				</div>
				<div class='field done'>
					<span>Сумма ($):</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="4" type='text' id='ammount' name="ammount_raw" value="<?= isset($order) ? $order->ammount : '100'?>"/>
						</div>
					</div>
				</div>
				<div class='field done wm'>
					<span>Номер кошелька:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="12" type='text' name='wm_number' value="<?= isset($order) ? $order->wm_number : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done bm' style="display:none;">
					<span>Фамилия:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="127" type='text' name='bm_surname' value="<?= isset($order) ? $order->bm_surname : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done bm' style="display:none;">
					<span>Имя:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="127" type='text' name='bm_name' value="<?= isset($order) ? $order->bm_name : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done bm' style="display:none;">
					<span>Отчество</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="127" type='text' name='bm_otc' value="<?= isset($order) ? $order->bm_otc : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done bm' style="display:none;">
					<span>Счет:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="20" type='text' name='bm_number' value="<?= isset($order) ? $order->bm_number : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done bm' style="display:none;">
					<span>БИК банка:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="9" type='text' name='bm_bik' value="<?= isset($order) ? $order->bm_bik : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done bm' style="display:none;">
					<span>Назначение платежа:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="127" type='text' name='bm_target' value="<?= isset($order) ? $order->bm_target : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done lp' style="display:none;">
					<span>Номер телефона:</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="9" type='text' name='lp_number' value="<?= isset($order) ? $order->lp_number : ''?>"/>
						</div>
					</div>
				</div>
				<div class='field done qw' style="display:none;">
					<span>Номер телефона (кошелька):</span>
					<div class='text-field'>
						<div>
							<input class="input" maxlength="10" type='text' name='qw_number' value="<?= isset($order) ? $order->qw_number : ''?>"/>
						</div>
					</div>
				</div>
				<br>
				<b id="total_amount_manual" style="margin-left:40%;">Итого: 100 руб.</b>
				<br>
				<div class='submit' style="width:107px;margin-left:213px;">
					<div>
						<input type='submit' value='Добавить заявку' style="width:91px"/>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<br />
	<br />
	<hr />
	<h3>Ваши заявки на вывод</h3>
	<?View::show($viewpath.'ajax/showOutMoney', array(
		'Orders' => $Orders,
		'statuses'	=> $statuses,
		'services'	=> $services,
		'pager' => $pager));?>
</div>