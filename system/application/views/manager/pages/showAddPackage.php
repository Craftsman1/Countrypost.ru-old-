<script type="text/javascript" src="/static/js/jquery-ui.min.js"></script>
<div class='content'>
	<h3>Добавление посылки</h3>
	<br />
	<form class='admin-inside'  action="<?=$selfurl?>addPackage" method="POST">
	
		<ul class='tabs'>
			<li class='active'><div><a href='<?=$selfurl?>showAddPackage'>Добавить посылку</a></div></li>
			<li><div><a href='<?=$selfurl?>showNewPackages'>Новые</a></div></li>
			<li><div><a href='<?=$selfurl?>showPayedPackages'>Оплаченные</a></div></li>
			<li><div><a href='<?=$selfurl?>showSentPackages'>Отправленные</a></div></li>
			<li><div><a href='<?=$selfurl?>showOpenOrders'>Заказы “Помощь в покупке”</a></div></li>
			<li><div><a href='<?=$selfurl?>showPayedOrders'>Оплаченные заказы</a></div></li>
			<li><div><a href='<?=$selfurl?>showSentOrders'>Закрытые заказы</a></div></li>
		</ul>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th>Клиент</th>
					<th>Вес, кг</th>
				</tr>
				<tr>
					<td>
						<input id="package_client" name="package_client" type="text" style="width:100px;" onkeypress="javascript:validate_number(event);" >
					</td>
					<td><input id="package_weight" name="package_weight" type="text" maxlength="5" style="width:100px;" onkeypress="javascript:validate_number(event);" value="1.0" ></td>
				</tr>
				<tr class='last-row'>
					<td colspan='9'>
						<br />
						<div class='float'>	
							<div class='submit'><div><input type='submit' value='Добавить' /></div></div>
						</div>
					</td>
					<td></td>
				</tr>
			</table>
		</div>
	</form>
</div>

<script>
	function validate_number(evt) {
		var theEvent = evt || window.event;
		var key = theEvent.keyCode || theEvent.which;
		key = String.fromCharCode( key );
		var regex = /[0-9]|\./;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			theEvent.preventDefault();
		}
	}
	
		
	$(function() {
		$('#package_client')
			.focus()
			.keypress(function(event){validate_number(event);})
			.autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: '<?=$selfurl?>autocompleteClient/' + request.term,
						success: function(data) {
							response($.map(eval(data), function( item ) {
								return {
									label: item,
									value: item
								}
							}));
						}
					});
				},
				minLength: 1
			});
	});
</script>