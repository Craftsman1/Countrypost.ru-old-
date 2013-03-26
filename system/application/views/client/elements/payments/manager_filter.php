<form class='admin-inside'
	  id="balanceFilterForm"
	  action='<?= BASEURL ?>client/filterBalance'
	  method='POST'
	  style="position: relative;">
	<input type='hidden' name='balanceReset' id='balanceReset' value='' />
	<input type='hidden'
		   name='svalue'
		   id="balance_search">
	<img class="float-right"
		 id="balanceProgress"
		 style="display: no1ne;position: absolute;bottom: -30px; left: 80px;"
		 src="/static/images/lightbox-ico-loading.gif"/>
	<a href='#'
	   id='reset_balance'
	   style="display: none;">Все посредники</a>
</form>
<script>
	function processBalanceFilter()
	{
		$("form#balanceFilterForm #balanceReset").val("0");
		$("form#balanceFilterForm").submit();
	}

	function balanceReset(e)
	{
		e.preventDefault();

		$("form#balanceFilterForm #balanceReset").val("1");
		$("form#balanceFilterForm input#balance_search").val('');
		$("form#balanceFilterForm").submit();
	}

	$(function() {
		$('input#balance_search')
			.change(function() {
				processBalanceFilter();
			});

		$('a#reset_balance').click(function(e) {
			balanceReset(e);
		});

		$('#balanceFilterForm').ajaxForm({
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#balanceProgress").show();
			},
			success: function(response)
			{
				$('div#balance').replaceWith(response);
				$("#balanceProgress").hide();
			},
			error: function(response)
			{
				$("#balanceProgress").hide();
			}
		});
	});
</script>