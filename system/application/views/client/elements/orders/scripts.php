<script type="text/javascript">
	function deleteItem(id) {
		if (confirm("Вы уверены, что хотите удалить заказ №" + id + "?")){
			window.location.href = '<?= $selfurl ?>deleteOrder/' + id;
		}
	}

	function payItem(id) {
		if (confirm("Оплатить заказ №" + id + "?")){
			window.location.href = '<?= $selfurl ?>payOrder/' + id;
		}
	}

	function repayItem(id) {
		if (confirm("Доплатить за заказ №" + id + "?")){
			window.location.href = '<?= $selfurl ?>repayOrder/' + id;
		}
	}
</script>