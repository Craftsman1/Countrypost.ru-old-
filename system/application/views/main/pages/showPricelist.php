<? if (isset($pricelist) && $pricelist) 
	{
		$summary = array();
		
		// формируем массив тарифов
		foreach($pricelist as $delivery) 
		{
			$deliveries[$delivery->delivery_id]['name'] = $delivery->delivery_name;
			$deliveries[$delivery->delivery_id]['time'] = $delivery->delivery_time;
			$deliveries[$delivery->delivery_id]['items'][] = array(
				'weight' => $delivery->pricelist_weight,
				'price' => $delivery->pricelist_price,
				'price_local' => $delivery->pricelist_price_local,
				'country_from' => $delivery->pricelist_country_from,
				'currency_symbol' => $delivery->currency_symbol);
		
			// формируем сводную таблицу
			if (empty($summary[$delivery->pricelist_weight]))
			{
				$summary[$delivery->pricelist_weight] = array();
			}
			
			$summary[$delivery->pricelist_weight][$delivery->delivery_id] = $delivery->pricelist_price;
		}
		
		ksort($deliveries);
		ksort($summary);
		$delivery_keys = array_keys($deliveries);
		$summary_keys = array_keys($summary);
	} 
	
	$collapsed_size = 6;
?>
<div class='content'>
	<div style='text-align:center'>
		<form id="filterForm" action="<?=$selfurl?>filterPricelist" method="POST">
			<div style='float:right;margin-right:150px;'>
				<h2>Тарифы на доставку</h2>
				<div id="orderFilter" style="margin:0 auto; width:225px;">
					Доставка из: 
					<select class="select" name="pricelist_country_from" style="width: 225px;">
						<option value="">выбрать...</option>
						<?if ($from_countries) : foreach($from_countries as $country) : ?>
							<option value="<?=$country->country_id?>" <? if ($country->country_id == $filter->pricelist_country_from) : ?>selected="selected"<? endif; ?>><?=$country->country_name?></option>
						<?endforeach; endif;?></select> 
					Доставка в: 
					<select class="select" name="pricelist_country_to" style="width: 225px;">
						<option value="">выбрать...</option>
						<?if ($to_countries) : foreach($to_countries as $country) : ?>
							<option value="<?=$country->country_id?>" <? if ($country->country_id == $filter->pricelist_country_to) : ?>selected="selected"<? endif; ?>><?=$country->country_name?></option>
						<?endforeach; endif;?>
					</select>
					Вес посылки: 
					<input type="text" class="select pricelist_weight" name="pricelist_weight" maxlength='4' style="width:225px;cursor:auto;" />
				</div>
			</div>
		</form>
		<form id="filterOurForm" action="<?=$selfurl?>filterOurPricelist" method="POST">
			<div style='float:left;margin-left:150px;'>
				<h2>Наши тарифы</h2>
				<div id="ourPricelistFilter" style="margin:0 auto; width:225px;">
					в: 
					<select class="select" name="our_pricelist" style="width: 225px;">
						<option value="">выбрать...</option>
						<? if ($from_countries) : foreach($from_countries as $country) : ?>
							<option value="<?=$country->country_id?>" <? if ($country->country_id == $filter->our_pricelist) : ?>selected="selected"<? endif; ?>><?=$country->country_name?></option>
						<? endforeach; endif; ?></select>
					</select>
				</div>
			</div>
		</form>
	</div>
	<br style='clear:both;' />
	<br />
	<? if (isset($deliveries)) : ?>
	<hr />
	<?= empty($pricelist_description) ? '' : html_entity_decode($pricelist_description->pricelist_description) . '<hr />' ?>
	<? foreach($deliveries as $id=>$delivery) : ?>
	<div class='table delivery admin-inside' style="width:250px;float:left; margin:5px; cursor: pointer;" title="Нажмите, чтобы развернуть тариф">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div style="text-align:center;">
			<span style="font-size:1.6em;font-weight:bold;"><?=$delivery['name']?></span><br>  <i>Срок доставки: <?=$delivery['time']?></i>               
		</div>
		<table>
		<tr>
			<td>Вес (кг)</td>
			<td>Цена (<?= $delivery['items'][0]['currency_symbol'] ?>)</td>
			<td>Цена ($)</td>
		</tr>
		<? $count = 0; foreach ($delivery['items'] as $val) : $count++; ?>
		<tr <?= $collapsed_size < $count ? 'class="collapseable" style="display:none;"' : '' ?>>
			<td><?= $val['weight'] ?></td>
			<td><?= $val['price_local'] ?></td>
			<td><?= $val['price'] ?></td>
		</tr>
		<? endforeach; ?>
		<? if ($collapsed_size < $count) : ?>
		<tr class="last-row<?= $collapsed_size < $count ? ' collapseable" style="display:none;' : '' ?>">
			<td colspan="3">
				<br />
				<a href="javascript:return void(0);">Свернуть тариф</a>
			</td>
		</tr>
		<tr class="last-row<?= $collapsed_size < $count ? ' collapseable' : '' ?>">
			<td colspan="3">
				<br />
				<a href="javascript:return void(0);">Развернуть тариф</a>
			</td>
		</tr>
		<? endif; ?>
		</table>
	</div>
	<? endforeach; ?>
	<? elseif ( ! empty($our_pricelist)) : ?>
	<hr />
	<?= html_entity_decode($our_pricelist->description) ?>
	<? endif; ?>	

	<? if ( ! empty($summary)) : ?>
	<div class='table summary admin-inside' style="width:880px; float:left; margin:5px 0; display:none;">
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		<div style="text-align:center;">
			<span style="font-size:1.6em;font-weight:bold;">Сравните тарифы:</span>
		</div>
		<br />
		<table>
			<tr class="weightheader">
				<th>Вес</th>
				<? foreach ($deliveries as $delivery) : ?>
				<th>
					<b><?= $delivery['name'] ?></b>
				</th>
				<? endforeach; ?>
			</tr>
			<? foreach ($summary as $weight => $delivery_prices) : ?>
			<tr class="weight<?= $weight * 10 ?>">
				<td><?= $weight ?>кг</td>
				<? foreach ($delivery_keys as $delivery_key) : ?>
				<td><?= empty($delivery_prices[$delivery_key]) ? '-' : '$' . $delivery_prices[$delivery_key] ?></td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
		</table>
	</div>
	<? endif; ?>	
</div>
<div style="clear:both;">
	<br />
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filterForm select').change(function() {
			document.getElementById('filterForm').submit();	
		});

		$('#filterOurForm select').change(function() {
			document.getElementById('filterOurForm').submit();	
		});
		
		$('div.delivery').click(function() {
			$(this)
				.find('tr.collapseable')
				.toggle();
			
			if ($(this).attr('title') == 'Нажмите, чтобы развернуть тариф')
			{
				$(this).attr('title', 'Нажмите, чтобы свернуть тариф');
			}
			else
			{
				$(this).attr('title', 'Нажмите, чтобы развернуть тариф');
			}
		});
		
		$('input.pricelist_weight')
			.keypress(function(event) {
				validate_number(event);
			})
			.keyup(function() {
				var weight = parseFloat($(this).attr('value'));
				weight = isNaN(weight) ? 0 : weight;
				
				if (weight)
				{
					show_summary(weight);
				}
				else
				{
					hide_summary();
				}
			});
	});
	
	<? if(isset($summary_keys)) : ?>
	function show_summary(weight)
	{
		var deliveries = <?= json_encode($summary_keys) ?>;
		var prevprev = '';
		var prev = 0;
		var curr = 0;
		var next = 0;
		var nextnext = 0;
		var found = false;
		
		for (id in deliveries)
		{
			if ( ! found)
			{
				prevprev = prev;
				prev = curr;
				curr = deliveries[id];
				
				if (curr >= weight && prev < weight)
				{
					found = true;
				}
			}
			else
			{
				if ( ! next)
				{
					next = deliveries[id];
				}
				else
				{
					nextnext = deliveries[id];
					break;
				}
			}
		}
		
		
		if (found)
		{
			$('div.summary tr')
				.hide()
				.find('td')
				.css('font-weight', 'normal');
				
			$('tr.weightheader,tr.weight' + 
				prevprev * 10 +
				',tr.weight' + 
				prev * 10 +
				',tr.weight' + 
				curr * 10 +
				',tr.weight' + 
				next * 10 +
				',tr.weight' + 
				nextnext * 10)
				.show();
				
			$('tr.weight' + curr * 10 + ' td').css('font-weight', 'bold');
				
			$('div.summary').slideDown('fast');
			$('div.delivery').slideUp('fast');
		}
		else
		{
			hide_summary();
		}
	}
	<? endif; ?>
	
	function hide_summary()
	{
		$('div.summary').slideUp('fast');
		$('div.delivery').slideDown('fast');
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
</script>