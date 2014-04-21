<?

if(isset($currencies) AND $currencies) : ?>
<div class='adittional-block' style="    position: absolute;    right: 0px;    top: 0px;    z-index: 1;    width: 239px;font-size:20px;">
	<div class='headlines' style="    margin-right: 20px;    width: 235px;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<center><h2 >Курсы валют Countrypost</h2></center>
		<center>
			<select id="currency_select">
				<?  foreach($currencies as $cur): ?>
				<option value="<?= $cur['currency_name']; ?>" <?= ($cur_currency == $cur['currency_name'] ? 'selected="selected"' : ''); ?>>1 <?= $cur['currency_name']; ?> =</option>
				<? endforeach; ?>
			</select>
		</center>
		<dl style="width: 222px;">
			<dd>
				<?= number_format($rate_rur, 6) ?> RUB
			</dd>
			<dd>
				<?= number_format($rate_usd, 6) ?> USD
			</dd>
			<dd>
				<?= number_format($rate_kzt, 6) ?> KZT
			</dd>
			<dd>
				<?= number_format($rate_uah, 6)?> UAH
			</dd>
		</dl>
	</div>
</div>
<script type="text/javascript">
		$(function() {
			$('#currency_select').change(function(){
			   jQuery.post( '<?= base_url().'client/get_exchange_rate_table' ?>',								{
						   "cur_name": $('#currency_select').val()
					   },
					   function(data)
					   {	
							if(data.status == 'ok')
							{
							   $('#exchnge_rates_container').html(data.table);
							}
					   }, 'json'
			   );
		   })
		})
		</script>
<? endif; ?>