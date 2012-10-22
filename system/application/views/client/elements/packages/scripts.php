<script type="text/javascript">
	$(function() {
		$('#packagesForm input:checkbox:nth-child(2n)').change(function() {
			if ($(this).attr('checked') &&
				confirm('Запросить помощь партнера в заполнении декларации выбранной посылки?'))
			{
				window.location = '<?= $selfurl ?>addDeclarationHelp/' + $(this).attr('id').replace('help', '');	
			}
		});
	});
	
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}

	function payItem(id) {
		if (confirm("Оплатить посылку №" + id + "?")){
			window.location.href = '<?= $selfurl ?>payPackage/' + id;
		}
	}
	
	function updatePerPage(dropdown)
	{
		var id = $(dropdown).find('option:selected').val();
		window.location.href = '<?= $selfurl ?>updatePerPage/' + id;
	}
	
	function updateDelivery(id) {
		var selectedDelivery = $('#delivery' + id + ' option:selected').val();
		
		if (selectedDelivery != '0' &&
			confirm("Изменить способ доставки посылки №" + id + "?"))
		{			
			window.location.href = '<?= $selfurl ?>updatePackageDelivery/' + id + '/' + selectedDelivery;
		}
	}
	
	function deleteItem(id){
		if (confirm("Вы уверены, что хотите удалить посылку №" + id + "?")){
			window.location.href = '<?= $selfurl ?>deletePackage/' + id;
		}
	}
	
	function repayItem(id) 
	{
		if (confirm("Доплатить за посылку №" + id + "?")){
			window.location.href = '<?= $selfurl ?>repayPackage/' + id;
		}
	}
</script>