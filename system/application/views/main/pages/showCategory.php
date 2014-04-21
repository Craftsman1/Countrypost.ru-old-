<div class='content'>
	<h2>Каталог магазинов</h2>
	<div class="back">
		<a href="javascript:history.back();" class="back"><span>Назад</span></a>
	</div>
	<center>
		<h3><?=$category->scategory_name?></h3>
	</center>
	<?if ($is_authorized):?>
		<div style='float:left;'>	
			<div class='submit'><div><input type='submit' style="width:150px;" value='Добавить новый магазин' onclick="window.location = '<?=$this->config->item('base_url')?>main/showAddShop'" /></div></div>
		</div>
		<br /><br /><br />
	<?endif;?>
	<?View::show($viewpath.'ajax/showCategory', array(
		'category' => $category,
		'shops' => $shops,
		'is_admin' => $is_admin,
		'is_authorized' => $is_authorized,
		'pager' => $pager));?>
</div>
<script type="text/javascript">
function deleteItem(id){
	if (confirm("Вы уверены, что хотите удалить магазин №" + id + "?")){
		window.location.href = '<?=$selfurl?>deleteShop/' + id;
	}
}
</script>