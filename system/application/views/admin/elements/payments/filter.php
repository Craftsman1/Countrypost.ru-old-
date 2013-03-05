<? //print_r($filter);die(); ?>
<form class='admin-inside'
	  id="filterForm"
	  action='<?=$selfurl?>filterAllPayments'
	  method='POST'
	  style="position: relative;">
	<input type='hidden' name='resetFilter' id='resetFilter' value='' />
	<b>Поиск заявки:</b>
	<input type='text'
		   name='svalue'
		   value="<?= isset($filter->svalue)? $filter->svalue : '' ?>"
		   id="search_string"
		   onchange="processFilter();">
	по
	<select name='sfield'
			onchange="processFilter();">
		<option value=''>все</option>
		<option value='client_id'
			<?= (isset($filter->sfield) AND $filter->sfield == 'client_id') ?
			'selected' :
			'' ?>>номеру клиента</option>
		<option value='order2in_id'
			<?= (isset($filter->sfield) AND $filter->sfield == 'order2in_id') ?
			'selected' :
			'' ?>>номеру заявки</option>
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
	<br>
	<a href='#' id='reset_filter'>Все заявки</a>
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
				$('div#payments').replaceWith(response);
				$("#filterProgress").hide();
			},
			error: function(response)
			{
				$("#filterProgress").hide();
			}
		});
	});
</script>