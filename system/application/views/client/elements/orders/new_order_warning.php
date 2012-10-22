<div class='table' id='new_order_warning_block' style="width:400px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Предупреждение. <b class="red-color">Важно!!!</b></h3>
	<p>
		У Вас были найдены уже ранее добавленные:
		<br />
		<? if ( ! empty($new_orders) OR ! empty($payed_orders)) : ?>
		<b>Заказы:</b> 
		<? endif; ?>
		<? if ( ! empty($new_orders)) : ?>
		Новые: <a href="<?= $selfurl  ?>showOpenOrders"><?= $new_orders ?></a>
		<? endif; ?>
		<? if ( ! empty($payed_orders)) : ?>
		Оплаченные: <a href="<?= $selfurl  ?>showOpenOrders"><?= $payed_orders ?></a>
		<? endif; ?>
		<? if ( ! empty($new_packages) OR ! empty($payed_packages)) : ?>
		<b>Посылки:</b> 
		<? endif; ?>
		<? if ( ! empty($new_packages)) : ?>
		Новые: <a href="<?= $selfurl  ?>showOpenPackages"><?= $new_packages ?></a>
		<? endif; ?>
		<? if ( ! empty($payed_packages)) : ?>
		Оплаченные: <a href="<?= $selfurl  ?>showPayedPackages"><?= $payed_packages ?></a>
		<? endif; ?>
	</p>
	</center>
	<p>
		Предупреждаем, что после добавления нового заказа Вы не сможете его объединить с уже ранее добавленными, но еще не отправленными заказами и посылками (заказ будет отправлен отдельной посылкой).
		<br />
		<br />
		Если Вы все же планируете объединить этот заказ с ранее добавленным заказом, то Вы можете не добавлять новый заказ, а добавить новые товары в любой уже добавленный ранее заказ.
		<br />
		<br />
		Если Вы все же хотите добавить новый заказ и объединить его потом с другим заказом или посылкой, то после добавления заказа свяжитесь с нами по email: <a href='mailto:info@countrypost.ru'>info@countrypost.ru</a> или skype: <a href='skype:info@countrypost.ru'>country_post</a>.
	</p>
	<br />
	<div class='admin-inside' style='float:left;'>	
		<div class='submit'>
			<div>
				<input type='button' value='Отказаться' onclick="$('#lay').fadeOut('slow');$('#new_order_warning_block').fadeOut('slow');"/>
			</div>
		</div>
	</div>
	<div class='admin-inside' style='float:right;'>
		<div class='submit'>
			<div>
				<input type='button' value='Продолжить' onclick="$('#new_order_warning_block').fadeOut('slow');lay2();"/>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var new_order_warning_click = 0;
	
	function openWarningPopup()
	{
		<? if (empty($hasActiveOrdersOrPackages)) : ?>
		lay2();
		<? else : ?>
		var offsetLeft	= (window.innerWidth - $('#new_order_warning_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#new_order_warning_block').height()) / 2;
		
		$('#new_order_warning_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#new_order_warning_block').fadeIn("slow");
		
		if (!new_order_warning_click)
		{
			new_order_warning_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#new_order_warning_block').fadeOut("slow");
			})
		}
		<? endif; ?>
	}
</script>