<div class='content'>
	<? $ending = '';
		if (isset($Odetails) && $Odetails) 
		{
			$amount = count($Odetails);
			if ($amount > 1)
			{
				if ($amount < 5)
				{
					$ending = 'а';
				}
				else
				{
					$ending = 'ов';
				}
			}
		}
		?>
	<h3>Корзина<?=(isset($amount)) ? ' ('.$amount.' товар'.$ending.')' : ''?></h3>
	<?if(isset($result->m) && $result->m):?><em class="order_result"><?=$result->m?></em><br/><?endif;?>
	<? View::show($viewpath.'elements/div_float_help') ?>
	<? View::show($viewpath.'elements/div_float_new_order', array('Country' => $Country)) ?>
	<? View::show($viewpath.'elements/orders/import_excel'); ?>
	<fieldset class='admin-inside'>
		<div class="submit">
			<div>
				<input type="button" onclick="lay2()" name="add" value="Добавить товар в корзину" style="width:160px !important;">
			</div>
		</div>
	</fieldset>
	<br />
	<form class='admin-inside' action="<?=$selfurl?>checkout" method="POST">
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			
			<?if($Odetails):?>
			<table>
				<tr>
					<th>Товар</th>
					<th>Страна</th>
					<th>Наименование<br />товара</th>
					<th>Параметры товара</th>
					<th>Скриншот</th>
					<th>Ссылка на товар</th>
					<th >Удалить</th>
				</tr>
				<?foreach ($Odetails as $Odetail):?>
				<tr>
					<td><?=$Odetail->odetail_id?></td>
					<td><?=$Country->country_name?></td>
					<td><?=$Odetail->odetail_product_name?></td>
					<td>
						Цвет: <?=$Odetail->odetail_product_color?><br/>
						Кол-во: <?=$Odetail->odetail_product_amount?><br/>
						Размер: <?=$Odetail->odetail_product_size?>
					</td>
					<td width="300px">
						<? if (isset($Odetail->odetail_img)) : ?>
						<a href="#" onclick="window.open('<?=$Odetail->odetail_img?>');return false;"><?=(strlen($Odetail->odetail_img)>20?substr($Odetail->odetail_img,0,20).'...':$Odetail->odetail_img)?></a>
						<? else : ?>
						<a href="javascript:void(0)" onclick="setRel(<?=$Odetail->odetail_id?>)">
							<img border="0" src="<?=$selfurl?>showScreen/<?=$Odetail->odetail_id?>" width="300px"/>
							<a rel="lightbox_<?=$Odetail->odetail_id?>" href="<?=$selfurl?>showScreen/<?=$Odetail->odetail_id?>" style="display:none;">Посмотреть</a>
						</a>
						<? endif; ?>
					</td>
					<td>
						<a href="#" onclick="window.open('<?=$Odetail->odetail_link?>');return false;"><?=(strlen($Odetail->odetail_link)>17?substr($Odetail->odetail_link,0,17).'...':$Odetail->odetail_link)?></a>
					</td>
					<td align="center"><a href="<?=$selfurl?>deleteDetail/<?=$Odetail->odetail_id?>"><img title="Удалить" border="0" src="/static/images/delete.png"></a></td>
				</tr>
				<?endforeach;?>
				<tr class='last-row'>
					<td colspan='9'>
						<div class='float'>	
							<div class='submit'><div><input type='submit' name="add" value='Сформировать заказ' style="width:125px !important;" /></div></div>
						</div>
					</td>
					<td>
					</td>
				</tr>
			</table>
			<?endif;?>
		</div>
	</form>
</div>
<script>
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
</script>