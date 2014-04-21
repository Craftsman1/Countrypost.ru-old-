<div class='content'>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<br />
	<h2><?=(!isset($manager_user) ? 'Добавление нового партнера' : 'Редактирование партнера')?></h2>
	<br />
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>Назад</span></a>
	</div>
	<br />
	<form id='partner_form' class='registration' action='<?=isset($manager_user) && isset($manager_user->user_id ) ? $selfurl.'updatePartner/'.$manager_user->user_id : $selfurl.'addPartner'?>' method='POST'>
		<? if ($result->e < 0) : ?>
			<em style="color:red !important"><?=$result->m?></em>
			<br />
		<? endif; ?>
		<div class='field <?= isset($manager_user) && $manager_user->user_login ? 'done' :'' ?>'>
			<span>Логин:</span>
			<div class='text-field'><div><input type='text' name='user_login' value='<?=isset($manager_user) ? $manager_user->user_login :'';?>'/></div></div>
		</div>		
		<div class='field <?=isset($manager_user) && $manager_user->user_login ? 'done' :'';?>'>
			<span>Пароль:</span>
			<div class='text-field'><div><input type='text' name='user_password' value='<?=(isset( $_POST['user_password'] ) ? $_POST['user_password'] : '')?>'/></div></div>
		</div>
		<div class='field <?=isset($manager_user) && $manager_user->user_email ? 'done' :'';?>' >
			<span>E-mail:</span>
			<div class='text-field'><div><input type='text' name='user_email' value='<?=isset($manager_user) ? $manager_user->user_email :'';?>'/></div></div>
		</div>
		<div class='hr'></div>
		<div class='field done'>
			<span>Имя:</span>
			<div class='text-field'><div><input type='text' name='manager_name' value='<?=isset($manager) ? $manager->manager_name :'';?>'/></div></div>
		</div>
		<div class='field <?=isset($manager) && $manager->manager_addres ? 'done' :'';?>'>
			<span>Адрес:</span>
			<div class='text-field'><div><input type='text' name='manager_addres' value='<?=isset($manager) ? $manager->manager_addres :'';?>'/></div></div>
		</div>
		<div class='field <?=isset($manager) && $manager->manager_address_local ? 'done' :'';?>'>
			<span>Местный адрес:</span>
			<div class='text-field'><div><input type='text' name='manager_address_local' value='<?=isset($manager) ? $manager->manager_address_local :'';?>'/></div></div>
		</div>
		<div class='field <?=isset($manager) && $manager->manager_phone ? 'done' : '';?>'>
			<span>Телефон:</span>
			<div class='text-field'><div><input type='text' name='manager_phone' maxlength='1024' value='<?=isset($manager) ? $manager->manager_phone :'';?>'/></div></div>
		</div>
		<div class='field <?=isset($manager) && $manager->manager_skype ? 'done' : '';?>'>
			<span>Skype:</span>
			<div class='text-field'><div><input type='text' name='manager_skype' maxlength='1024' value='<?=isset($manager) ? $manager->manager_skype :'';?>'/></div></div>
		</div>
		<div class='field done' id='country'>
			<span>Страна:</span>
			<select class="select" name="manager_country" id="manager_country">
				<?if (count($countries)>0): foreach ($countries as $country):?>
					<option value="<?=$country->country_id;?>" <?= (isset($manager) && $manager->manager_country==$country->country_id) ? 'selected' :'';?>><?=$country->country_name?></option>
				<?endforeach; endif;?>	
			</select>
		</div>
		<div class='hr'></div>
		<div class='field done'>
			<span>Кредит $:</span>
			<div class='text-field'><div><input type='text' id='manager_credit' name='manager_credit' value='<?=isset($manager) ? $manager->manager_credit : 0?>'/></div></div>
		</div>
		<div class='field done'>
			<span>Кредит <?=isset($manager->currency_symbol) ? $manager->currency_symbol : '(местная валюта)' ?>:</span>
			<div class='text-field'><div><input type='text' id='manager_credit_local' name='manager_credit_local' value='<?=isset($manager) ? $manager->manager_credit_local : 0?>'/></div></div>
		</div>
		<div class='field done'>
			<span>Комиссия за заказ %:<br />(0% - комиссия отключена<? if (isset($max_order_tax)) : ?>,
				максимум <?= $max_order_tax ?>%<? endif; ?>)</span>
			<div class='text-field'>
				<div>
					<input type='text' id='order_tax' name='order_tax' value='<?= isset($manager) && isset($manager->order_tax) ? $manager->order_tax : '' ?>' />
				</div>
			</div>
		</div>
		<div class='field done'>
			<span>Комиссия за связанную посылку <?=isset($manager->currency_symbol) ? $manager->currency_symbol : '(местная валюта)' ?>:
				<br />(<?= isset($manager->currency_symbol) ? $manager->currency_symbol : '' ?>0 - комиссия отключена<? if (isset($max_package_tax)) : ?>,
				максимум <?= isset($manager->currency_symbol) ? $manager->currency_symbol : '' ?><?= $max_package_tax ?><? endif; ?>)
			</span>
			<div class='text-field'>
				<div>
					<input type='text' id='package_tax' name='package_tax' value='<?= isset($manager) && isset($manager->package_tax) ? $manager->package_tax : '' ?>' />
				</div>
			</div>
		</div>
		<div class='field done'>
			<span>Комиссия за самостоятельную посылку <?=isset($manager->currency_symbol) ? $manager->currency_symbol : '(местная валюта)' ?>:
				<br />(<?= isset($manager->currency_symbol) ? $manager->currency_symbol : '' ?>0 - комиссия отключена<? if (isset($max_package_disconnected_tax)) : ?>,
				максимум <?= isset($manager->currency_symbol) ? $manager->currency_symbol : '' ?><?= $max_package_disconnected_tax ?><? endif; ?>)
			</span>
			<div class='text-field'>
				<div>
					<input type='text' id='package_disconnected_tax' name='package_disconnected_tax' value='<?= isset($manager) && isset($manager->package_disconnected_tax) ? $manager->package_disconnected_tax : '' ?>' />
				</div>
			</div>
		</div>
		<div class='field'>
			<span>Комиссия за фото в посылке (клиент) $:</span>
			<div>
				<input class="checkbox" type="checkbox" name="package_foto_tax" <?= isset($manager) && $manager->package_foto_tax ? 'checked' : '' ?>> 
			</div>
		</div>
		<div class='field'>
			<span>Комиссия за фото в посылке (партнер и админ) $:</span>
			<div>
				<input class="checkbox" type="checkbox" name="package_foto_system_tax" <?= isset($manager) && $manager->package_foto_system_tax ? 'checked' : '' ?>> 
			</div>
		</div>
		<div class='hr'></div>
		<div class='field'>
			<span>Способы доставки:</span>
			<div>
				<?if (count($deliveries) > 0): 
					foreach ($deliveries as $delivery) : ?>
					<div class="checkbox delivery_box" id="delivery_box_<?=$delivery->delivery_id?>">
						<label style="visibility:invisible;" for="delivery<?=$delivery->delivery_id?>"><?=$delivery->delivery_name?></label>
						<input style="visibility:invisible;" class="checkbox" type="checkbox" name="delivery[<?=$delivery->delivery_id?>]" id="delivery<?=$delivery->delivery_id?>" <?=$delivery->checked?> 
						<? if (!isset($manager_user) || !isset($manager_user->user_id )) 
						{
							echo 'checked'; 
						}
						?> />
					</div>
				<?endforeach; endif;?>
			</div>
		</div>
		<div class='field <?=isset($manager) && $manager->manager_status ? 'done' : '';?>'>
			<span>Статус:</span>
			<div class='text-field'><div>
				<select class="select" name='manager_status'>
					<option value="0">выберите...</option>
					<?if (count($statuses)>0): foreach ($statuses as $key=>$status):?>
						<option value="<?=$key;?>" <?= (isset($manager) && $manager->manager_status==$key) ? 'selected' :'';?>><?=$status?></option>
					<?endforeach; endif;?>
				</select>
			</div></div>
		</div>
		<div class='field <?=isset($manager) && $manager->manager_max_clients ? 'done' : '';?>'>
			<span>Максимальное кол-во пользователей:</span>
			<div class='text-field'><div><input type='text' id='manager_max_clients' name='manager_max_clients' value='<?=isset($manager) ? $manager->manager_max_clients :'50';?>'/></div></div>
		</div>
		<div class='field done'>
			<span>
				Максимальное кол-во заказов:
				<br />(0 - не может брать заказы, пусто - без ограничений)
			</span>
			<div class='text-field'><div><input type='text' id='manager_max_orders' name='manager_max_orders' value='<?= isset($manager) ? $manager->manager_max_orders  : '' ?>'/></div></div>
		</div>
		<div>
			<script type='text/javascript' src='/system/plugins/fckeditor/fckeditor.js'></script>
			<textarea id='description' name='description'>
				<?=isset($manager->manager_description) ? $manager->manager_description : '' ?>
			</textarea>
		</div>
		
		<div class='submit' style="margin:0;"><div><input type='submit' value='Сохранить' /></div></div>
	</form>
</div>
<script type="text/javascript">
	function validate_number(evt) 
	{
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if ( ! regex.test(key))
		{
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
		else
		{
			
		}
	}
	
	function validate_taxes() 
	{
		<? if (isset($max_package_tax) AND 
			isset($max_package_disconnected_tax) AND 
			isset($max_order_tax)) : ?>
		var max_order_tax = <?= $max_order_tax ?>;
		var max_package_tax = <?= $max_package_tax ?>;
		var max_package_disconnected_tax = <?= $max_package_disconnected_tax ?>;
		
		var valid = true;
		
		var order_tax = $('input#order_tax').val();
		order_tax = parseFloat(order_tax);
		order_tax = isNaN(order_tax) ? 0 : order_tax;

		if (order_tax > max_order_tax)
		{
			valid = false;
			$('input#order_tax').addClass('red-color');
		}
		else
		{
			$('input#order_tax').removeClass('red-color');
		}
		
		var package_tax = $('input#package_tax').val();
		package_tax = parseFloat(package_tax);
		package_tax = isNaN(package_tax) ? 0 : package_tax;

		if (package_tax > max_package_tax)
		{
			valid = false;
			$('input#package_tax').addClass('red-color');
		}
		else
		{
			$('input#package_tax').removeClass('red-color');
		}
		
		var package_disconnected_tax = $('input#package_disconnected_tax').val();
		package_disconnected_tax = parseFloat(package_disconnected_tax);
		package_disconnected_tax = isNaN(package_disconnected_tax) ? 0 : package_disconnected_tax;

		if (package_disconnected_tax > max_package_disconnected_tax)
		{
			valid = false;
			$('input#package_disconnected_tax').addClass('red-color');
		}
		else
		{
			$('input#package_disconnected_tax').removeClass('red-color');
		}
		
		if (valid)
		{
			$('div.submit').show();
		}
		else
		{
			$('div.submit').hide();
		}
		<? endif; ?>
	}
	
	$(function() {
		validate_taxes();
	    getDelivery();
        $("#manager_country").bind("change", getDelivery);
		
		$('input#order_tax,input#package_tax,input#package_disconnected_tax,input#manager_max_clients,input#manager_max_orders,input#manager_credit,input#manager_credit_local')
			.keypress(function(event) {
				validate_number(event);
			});
		
		$('input#order_tax,input#package_tax,input#package_disconnected_tax')
			.bind('keypress keydown mouseup keyup blur', function() {
				validate_taxes();
			});
	});
	
	function getDelivery()
	{
		$.post(
			"/admin/getDeliveries",
			{country_id :$("#manager_country option:selected").val()},
			function(data)
			{
				$('.delivery_box').hide();
				
				if(data.items){
					for(i=0;i<data.items.length;i++){
						$('#delivery_box_'+data.items[i].id).show();
					}
				}
			},
			"json"); 
	}
  
	<?= editor('description', 212, 880, 'Basic') ?>
</script>