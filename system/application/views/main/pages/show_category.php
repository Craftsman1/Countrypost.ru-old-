<div class='content'>
	<h2>������� ���������</h2>
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>�����</span></a>
	</div><br />
	<center>
		<h3><?=$category->scategory_name?></h3>
	</center>
		<?if ($shops):?>
		������������� ��:&nbsp;
		<a href='<?=$this->config->item('base_url')?>main/showCategory/<?=$category->scategory_id?>/country'>������</a>
		<a href='<?=$this->config->item('base_url')?>main/showCategory/<?=$category->scategory_id?>/comments'>�������</a>
	<?endif;?>
	<?if ($is_authorized):?>
		<div align="right" style="float:right;">
			<a href='<?=$this->config->item('base_url')?>main/showAddShop'>�������� ����� �������</a>
		</div>
	<?endif;?>
	<div>&nbsp;</div>
	<form class='admin-inside' action='#'>
		
		<div class='table'>
			<div class='angle angle-lt'></div>
			<div class='angle angle-rt'></div>
			<div class='angle angle-lb'></div>
			<div class='angle angle-rb'></div>
			<table>
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<col width='auto' />
				<tr>
					<th>�</th>
					<th>�������</th>
					<th>������</th>
					<th>��������</th>
					<th>������</th>
				</tr>
				<?if ($shops):?>
					<?foreach ($shops as $shop):?>
					<tr>
						<td><?=$shop->shop_id?></td>
						<td><a href='<?=$shop->shop_name?>'><?=$shop->shop_name?></a></td><!--<?=$this->config->item('base_url')?>main/showShop/<?=$shop->shop_id?> -->
						<td><?=$countries[$shop->shop_country]?></td>
						<td><?=$shop->shop_desc?></td>
						<td><a href='<?=$this->config->item('base_url')?>main/showShop/<?=$shop->shop_id?>'><?=$shop->count?></a></td>
					</tr>
					<?endforeach;?>	
				<?else:?>
					<tr>
						<td colspan="5">��������� ���!</td>
					</tr>
				<?endif;?>
			</table>
		</div>
	</form>

	<div class='pages'><div class='block'><div class='inner-block'>
		<a href='#' class='endpoints'>1</a><a href='#'>2</a><a href='#'>3</a><span>...</span><a href='#'>17</a><span>18</span><a href='#'>19</a><span>...</span><a href='#'>83</a><a href='#'>84</a><a href='#' class='endpoints'>85</a>
	</div></div></div>
</div>


<?/*
<a href='<?=$this->config->item('base_url')?>main/showShopCatalog'>�����</a>
<center><b><?=$category->scategory_name?></b>
<?if ($is_authorized):?>
<br/><a href='<?=$this->config->item('base_url')?>main/showAddShop'>�������� ����� �������</a>
<?endif;?>
<br/>
<?if ($shops):?>
������������� ��: <a href='<?=$this->config->item('base_url')?>main/showCategory/<?=$category->scategory_id?>/country'>������</a> <a href='<?=$this->config->item('base_url')?>main/showCategory/<?=$category->scategory_id?>/comments'>�������</a>
<table>
	<tr>
		<td>�</td>
		<td>�������</td>
		<td>������</td>
		<td>��������</td>
		<td>������</td>
	</tr>
	<?foreach ($shops as $shop):?>
	<tr>
		<td><?=$shop->shop_id?></td>
		<td><a href='<?=$this->config->item('base_url')?>main/showShop/<?=$shop->shop_id?>'><?=$shop->shop_name?></a></td>
		<td><?=$countries[$shop->shop_country]?></td>
		<td><?=$shop->shop_desc?></td>
		<td><?=$shop->count?></td>
	</tr>
	<?endforeach;?>	
</table>
<?else:?>
��������� ���!
<?endif;?>
</center>
*/?>