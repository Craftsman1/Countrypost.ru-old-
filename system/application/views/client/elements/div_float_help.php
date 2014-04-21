<div class='table' id="lay_block" style="width:400px; position:absolute; z-index: 1000; display:none; top:390px; left:290px;">
	<form class='admin-inside' action="<?=$selfurl?>proxy/" method="GET">
		<table>
			<tr>
				<td>Название магазина:</td>
				<td><input type="text" name="shop" value="" size=40></td>
			</tr>
			<tr>
				<td>Адрес сайта:</td>
				<td><input type="text" name="url" value="http://" size=40></td>
			</tr>
			<tr class='last-row'>
				<td colspan='9'>
					<div class='float'>	
						<div class='submit'><div><input type='submit' name="add" value='Добавить' /></div></div>
					</div>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	var fclick = 0;

	function lay(){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#lay_block').fadeIn("slow");
		
		if (!fclick){
			fclick = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#lay_block').fadeOut("slow");
			})
		}
	}
</script>