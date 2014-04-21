<div class='table' id="lay2_block" style="width:280px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Добавить посылку</h3>
		<em style="display:none;" class="pink-color"></em>
	</center>
	<form class='admin-inside' id="packageForm" action="<?=$selfurl?>addPackage/" enctype="multipart/form-data" method="POST">
		<input type="hidden" id="package_client" name="package_client" />
		<input type="hidden" id="package_manager" name="package_manager" />
		<input type="hidden" id="orderid" name="order_id" />
		<table>
			<tr>
				<td>Номер заказа:</td>
				<td id="order_id"></td>
			</tr>
			<tr>
				<td>Номер клиента:</td>
				<td id="client_id"></td>
			</tr>
			<tr>
				<td>Номер партнера:</td>
				<td id="manager_id"></td>
			</tr>
			<tr>
				<td>Вес, кг :</td>
				<td><input type="text" style="width:100%;" name="package_weight" value="1.0" onkeypress="javascript:validate_number(event);" /></td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'><div><input type='button' value='Отмена' onclick="$('#lay').click();"/></div></div>
					</div>
					<div class='float'>	
						<div class='submit'><div><input type='submit' name="add" value='Добавить' /></div></div>
					</div>
					<img class="float" id="progressbar" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</form>
</div>
<script src="<?=JS_PATH?>jquery.form.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#orderForm').ajaxForm({
			target: $('#packageForm').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#progressbar').show();
			},
			success: function(response)
			{
				$('#progressbar').hide();
				
				if (response)
				{
					$('em').html(response + '<br /><br />').show();
					
					$('#itemscreenshot,#itemlink').removeClass('red-color');
					
					if (response === 'Добавьте ссылку на товар.')
					{
						$('#itemlink').addClass('red-color');
					}
					else
					{
						$('#itemscreenshot').addClass('red-color');
					}
				}
				else
				{
					$('#itemscreenshot,#itemlink').removeClass('red-color');
					
					$('em')
						.removeClass('pink-color')
						.addClass('green-color')
						.html('Посылка успешно добавлена.<br /><br />')
						.show();
					window.location = '';
				}
			},
			error: function(response)
			{
				$('#progressbar').hide();
				$('em').html('Посылка не добавлена. Попробуйте еще раз.<br /><br />').show();
			}
		});
	});

	var fmclick = 0;
	function lay2(){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#lay2_block')
			.css('left', (((document.body.clientWidth - 960) / 2) + 330) + 'px')
			.fadeIn("slow");
		
		if (!fmclick){
			fmclick = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay2_block').fadeOut("slow");
			})
		}
		
		return false;
	}

	function addPackage(order_id, client_id, manager_id)
	{
		$('#order_id').html('№ ' + order_id);
		$('#client_id').html('№ ' + client_id);
		$('#manager_id').html('№ ' + manager_id);
		$('#package_client').val(client_id);
		$('#package_manager').val(manager_id);
		$('#orderid').val(order_id);
		lay2();
		return false;
	}

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
</script>