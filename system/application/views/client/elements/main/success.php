<div class='table' id='success_block' style="width:370px; position:fixed; z-index: 1000; display:none;">
	<center>
		<h3 style="margin-top:0;margin-bottom:20px;">Спасибо за заявку.</h3>
	</center>
	<p>
		Ваша заявка принята и будет обработана в течение 24 часов (кроме выходных и праздников). После выполнения в статистике платежей появится новая запись. Если через 24 часа запись не появилась, Ваша заявка не обработана по какой-то причине. Чтобы узнать причину, напишите на <a href='mailto:info@countrypost.ru'>info@countrypost.ru</a>.
	</p>
	<br />
	<div class='admin-inside' style='margin-left:133px;'>	
		<div class='submit'>
			<div>
				<input type='button' value='Закрыть' onclick="$('#lay').fadeOut('slow');$('#success_block').fadeOut('slow');"/>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function openSuccessPopup()
	{
		var offsetLeft	= (window.innerWidth - $('#success_block').width()) / 2;
		var offsetTop	= (window.innerHeight - $('#success_block').height()) / 2;
		
		$('#success_block').css({
			'left' : offsetLeft,
			'top' : offsetTop
		});
		
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#success_block').fadeIn("slow");
		
		if (!success_click)
		{
			success_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#success_block').fadeOut("slow");
			})
		}
	}
</script>