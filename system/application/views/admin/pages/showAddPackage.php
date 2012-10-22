<script type="text/javascript" src="/static/js/jquery-ui.min.js"></script>
<div class='content'>
	<?View::show($viewpath.'elements/div_submenu');?>

	<h3>Добавление посылки</h3>
	<div class='back'>
		<a class='back' href='javascript:history.back();'><span>Назад</span></a>
	</div><br />
	
	<form class='admin-inside'  action="<?=$selfurl?>addPackage" method="POST">
	
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th>Клиент</th>
					<th>Добавить от имени партнера</th>
					<th>Вес (кг)</th>
				</tr>
				<tr>
					<td>
						<input id="package_client" name="package_client" type="text" style="width:100px;" onkeypress="javascript:validate_number(event);" >
					</td>
					<td>
						<select id="package_manager" name="package_manager" style="width:150px;">
							<option value="">выберите...</option>
							<?if ($managers) : foreach ($managers as $manager):?>
						    <option value="<?=$manager->manager_user?>"><?=$manager->user_login?></option>
							<?endforeach; endif;?>
						</select>
					</td>
					<td><input id="package_weight" name="package_weight" type="text" maxlength="5" style="width:100px;" onkeypress="javascript:validate_number(event);" value="1.0"></td>
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