<form class='admin-inside'
	  id="filterForm"
	  action='<?=$selfurl?>filterTaxes'
	  method='POST'
	  style="position: relative;">
	<input type='hidden' name='resetFilter' id='resetFilter' value='' />
	<b>Поиск комиссии:</b>
	<input type='text'
		   name='svalue'
		   value="<?= isset($filter->svalue)? $filter->svalue : '' ?>"
		   id="search_string"
		   onchange="processFilter();">
	по
	<select name='sfield'
			onchange="processFilter();">
		<option value='manager_id'
			<?= (isset($filter->sfield) AND $filter->sfield == 'manager_id') ?
			'selected' :
			'' ?>>номеру посредника</option>
		<option value='manager_login'
			<?= (isset($filter->sfield) AND $filter->sfield == 'manager_login') ?
			'selected' :
			'' ?>>логину посредника</option>
		<option value='order_id'
			<?= (isset($filter->sfield) AND $filter->sfield == 'order_id') ?
			'selected' :
			'' ?>>номеру заказа</option>
		<option value='tax_id'
			<?= (isset($filter->sfield) AND $filter->sfield == 'tax_id') ?
			'selected' :
			'' ?>>номеру комиссии</option>
		<option value='amount'
			<?= (isset($filter->sfield) AND $filter->sfield == 'amount') ?
			'selected' :
			'' ?>>сумме комиссии</option>
	</select>
	дата
	<input type="text"
		   id="from"
		   name="from"
		   value="<?= $filter->from ?>"
		   onchange="processFilter();">
	-
	<input type="text"
		   id="to"
		   name="to"
		   value="<?= $filter->to ?>"
		   onchange="processFilter();" >
	статус
	<select name='status'
			onchange="processFilter();">
		<option value='' <?= empty($filter->status) ? 'selected' : '' ?>></option>
		<option value='not_payed'
			<?= (isset($filter->status) AND $filter->status == 'not_payed') ?
			'selected' :
			'' ?>>К выплате</option>
		<option value='payed'
			<?= (isset($filter->status) AND $filter->status == 'payed') ?
			'selected' :
			'' ?>>Выплачено</option>
	</select>
	<br>
	<a href='#' id='reset_filter'>Все комиссии</a>
	<br>
	<img class="float-left"
		 id="filterProgress"
		 style="display: none; right: 0; position: absolute; top: 0;"
		 src="/static/images/lightbox-ico-loading.gif"/>
</form>
<script>
	function processFilter()
	{
		$("#resetFilter").val("0");
		$("#filterForm").submit();
	}

	function resetFilter(e)
	{
		e.preventDefault();

		$("#resetFilter").val("1");
		$("input#search_string,input#to,input#from,#filterForm select").val('');
		$("#filterForm").submit();
	}

	$(function() {
		$.datepicker.setDefaults( $.datepicker.regional[ "" ] );
		$( "#from,#to" ).datepicker( $.datepicker.regional[ "ru" ] );

		$('a#reset_filter').click(function(e) {
			resetFilter(e);
		});

		$('#filterForm').ajaxForm({
			type: 'POST',
			dataType: 'html',
			iframe: true,
			beforeSubmit: function(formData, jqForm, options)
			{
				$("#filterProgress").show();
			},
			success: function(response)
			{
				$("div.pages").remove();
				$('div#pagerForm').replaceWith(response);
				$("#filterProgress").hide();
			},
			error: function(response)
			{
				$("#filterProgress").hide();
			}
		});
	});
</script>