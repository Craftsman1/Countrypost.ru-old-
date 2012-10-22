<script type="text/javascript" src="/static/js/jquery-ui.min.js"></script>
<div class='table' id="lay2_block" style="width:280px; position:fixed; z-index: 1000; display:none; top:200px;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Выберите партнера</h3>
		<em style="" class="pink-color">Все пользователи удаляемого партнера будут обслуживаться выбранным из списка партнером</em>
	</center>
	<br />
	<form class='admin-inside' id="managerForm" action="<?=$selfurl?>deletePartner/" enctype="multipart/form-data" method="POST">
		<input type="hidden" id="manager_id" name="manager_id" />
		<table>
			<tr>
				<td>Номер партнера:</td>
				<td>
					<input id="new_partner" name="new_partner" type="text" style="width:100%;" >
				</td>
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

	function deletePartner(manager_id)
	{
		$('#manager_id').val(manager_id);
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
	
	$(function() {
		$('#new_partner')
			.focus()
			.keypress(function(event){validate_number(event);})
			.autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: '<?=$selfurl?>autocompleteManager/' + $('#manager_id').val() + '/' + request.term,
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