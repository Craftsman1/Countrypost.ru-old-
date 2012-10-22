<div class='table' id="add_package_block" style="width:400px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">
			Жду посылку
		</h3>
		<em id="new_package_message" style="display:none;" class="pink-color"></em>
	</center>
	<form class='admin-inside' id="addPackageForm" action="<?= $selfurl ?>addPackage/ajax/" enctype="multipart/form-data" method="POST">
		<table>
			<tr>
				<td id="itemcountry">Страна:&nbsp;<? if (empty($package->package_id)) : ?>*<? endif; ?></td>
				<td>
					<select id="package_manager" name="package_manager" style="width:150px;">
						<option value="">выберите страну...</option>
					<? foreach($partners as $address) : 
						if (isset($partner_id) && $address->manager_user == $partner_id):?>
							<option value="<?=$address->manager_user?>" selected><?=$address->country_name?></option>
						<?else:?>
							<option value="<?=$address->manager_user?>"><?=$address->country_name?></option>
						<?endif;?>
					<? endforeach; ?>
					</select>
					<input name="package_id" type="hidden" value="0" />
				</td>
			</tr>
			<tr>
				<td id="itemtrackingno">Номер отслеживания: </td>
				<td>
					<input id="package_trackingno" name="package_trackingno" type="text" style="width:100px;" >
				</td>
			</tr>
			<tr>
				<td id="itemweight">Примерный вес (кг): *</td>
				<td>
					<input id="package_weight" name="package_weight" type="text" maxlength="5" style="width:100px;" onkeypress="javascript:validate_number(event);" value="1.0">
				</td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'>
							<div>
								<input type='submit' name="add" value='Добавить' />
							</div>
						</div>
					</div>
					<img class="float" id="add_package_progressbar" style="display:none;margin:5px;" src="/static/images/lightbox-ico-loading.gif"/>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</form>
</div>
<script src="<?= JS_PATH ?>jquery.form.js"></script>
<script type="text/javascript">
	function validate_number(evt) 
	{
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
		$('#addPackageForm').ajaxForm({
			target: $('#addPackageForm').attr('action'),
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$('#add_package_progressbar').show();
			},
			success: function(response)
			{
				$('#add_package_progressbar').hide();
				$('#itemweight,#itemcountry').removeClass('red-color');
				
				if (response)
				{
					$('em#new_package_message').html(response + '<br /><br />').show();
					
					if (response === 'Выберите страну.')
					{
						$('#itemcountry').addClass('red-color');
					}
					else if (response === 'Введите вес.')
					{
						$('#itemweight').addClass('red-color');
					}
				}
				else
				{
					$('em#new_package_message')
						.removeClass('pink-color')
						.addClass('green-color')
						.html('Посылка успешно добавлена.<br /><br />')
						.show();
					window.location = '';
				}
			},
			error: function(response)
			{
				$('#add_package_progressbar').hide();
				$('em#new_package_message').html('Товар не добавлен. Попробуйте еще раз.<br /><br />').show();
			}
		});
	});

	var add_package_click = 0;
	function add_package()
	{
		var offsetLeft	= (window.innerWidth - $('#add_package_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#add_package_block').height()) / 2;
		
		$('#add_package_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
			
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#add_package_block').fadeIn("slow");

		if ( ! add_package_click)
		{
			add_package_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#add_package_block').fadeOut("slow");
			});
		}
	}
</script>