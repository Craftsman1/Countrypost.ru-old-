<div class='table' id="upload_pdetail_foto_block" style="width:400px; position:fixed; z-index: 1000; display:none; top:300; left:400px;">
	<form enctype="multipart/form-data" class='admin-inside' action="" method="POST" id='upload_pdetail_foto_form'>			
		<input type="hidden" name="pdetail_id" id="pdetail">
		<input type="hidden" name="pdetail_joint_id" id="pdetail_joint">
		<table>
			<tr>
				<td>
					<input type="file" name="userfile1" size="40">
				</td>
			</tr>
			<tr>
				<td>
					<input type="file" name="userfile2" size="40">
				</td>
			</tr>
			<tr>
				<td>
					<input type="file" name="userfile3" size="40">
				</td>
			</tr>
			<tr>
				<td>
					<input type="file" name="userfile4" size="40">
				</td>
			</tr>
			<tr>
				<td>
					<input type="file" name="userfile5" size="40">
				</td>
			</tr>
			<tr class='last-row'>
				<td>
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

	var pdetail_foto_click = 0;
	function lay(){
		$('#lay').css({
			'width': document.body.clientWidth,
			'height': document.body.clientHeight
		});
		
		$('#lay').fadeIn("slow");
		$('#upload_pdetail_foto_block').fadeIn("slow");
		
		if (!pdetail_foto_click){
			pdetail_foto_click = 1;
			$('#lay').click(function(){
				$('#lay').fadeOut("slow");
				$('#upload_pdetail_foto_block').fadeOut("slow");
			})
		}
	}
	
	function uploadPdetailFoto(pdetail_id)
	{
		$('input#pdetail').val(pdetail_id);
		$('form#upload_pdetail_foto_form').attr('action', '<?= $selfurl ?>addPdetailFoto');
		lay();
	}

	function uploadPdetailJointFoto(pdetail_joint_id)
	{
		$('input#pdetail_joint').val(pdetail_joint_id);
		$('form#upload_pdetail_foto_form').attr('action', '<?= $selfurl ?>addPdetailJointFoto');
		lay();
	}

</script>