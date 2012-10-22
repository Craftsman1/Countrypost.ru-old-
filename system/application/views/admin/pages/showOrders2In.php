<div class='content'>

	<?View::show($viewpath.'elements/div_submenu');?>
	
	<h3>Заявки на ввод</h3>
	
	<br />
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>Назад</span></a>
	</div><br />

	<div class='table'>
		<div class='angle angle-lt'></div>
		<div class='angle angle-rt'></div>
		<div class='angle angle-lb'></div>
		<div class='angle angle-rb'></div>
		
		<?if(count($Orders2In)):?>
		<table>
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<col width='auto' />
			<tr>
				<th>Дата</th>
				<th>Сумма перевода</th>
				<th>+ Комиссия</th>
				<th>Скриншот квитанции</th>
				<th>Комментарии</th>
				<th>Статус</th>
			</tr>
			
			
			<?foreach ($Orders2In as $order):?>
			<tr>
				<td>
					<?=date("d.m.Y H:i", strtotime($order->order2in_createtime));?>
					<br />
					(update: <?=date("d.m.Y H:i", strtotime($order->order2in_lastchange));?>)
				
				</td>
				<td><?=$order->order2in_amount;?>$</td>
				<td><?=$order->order2in_tax;?>$</td>
				<td>
					
					<? if (isset($Orders2InFoto[$order->order2in_id])): ?>
						<a href="javascript:void(0)" onclick="setRel(<?=$order->order2in_id?>)">
							Посмотреть <?=count($Orders2InFoto[$order->order2in_id]);?> фото
							<?foreach ($Orders2InFoto[$order->order2in_id] as $o2iFoto):?>
								<a rel="lightbox_<?=$order->order2in_id?>" href="/admin/showOrder2InFoto/<?=$order->order2in_id?>/<?=$o2iFoto?>" style="display:none;">Посмотреть</a>
							<?endforeach;?>
						</a>
					<?else:?>
						Отсутствует					
					<? endif; ?>
				</td>
				<td>
					<a href="/admin/showOrder2InComments/<?=$order->order2in_id;?>">Посмотреть</a>
					<?if ($order->order2in_2admincomment):?>
						<br />Добавлен новый коментарий
					<?endif;?>
				</td>
				<td>
					<select onchange="window.location.href='<?=BASEURL?>admin/changeOrder2InStatus/<?=$order->order2in_id;?>/'+this.value">
					<?foreach ($Orders2InStatuses as $o2istatus => $statusName):?>
							<option value="<?=$o2istatus?>" <?=$o2istatus==$order->order2in_status?'selected':''?>><?=$statusName?></option>
					<?endforeach;?>
					</select>
				</td>
			</tr>
			<?endforeach;?>
		</table>
		<?else:?>
			<div align="center">Заявки отсутствуют</div>
		<?endif;?>
		<br>
		<font color="red"><b>*</b></font> В данной таблице показываются заявки на пополнение посредством перевода денег на наш долларовый расчетный счет.
	</div>
</div>
<script type="text/javascript">
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
</script>