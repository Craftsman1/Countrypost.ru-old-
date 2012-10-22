<?php
function getPageByItemN($itemN, $perPage)
{
  return (int)($itemN / $perPage) + 1;
}
function getItemNByPage($page, $perPage)
{
  $res = ($page - 1) * $perPage;
  return $res;
}
$baseUrl = rtrim($baseUrl, "/");
$itemN = (int)$this->uri->segment($pagination->uri_segment);
$perPage = $pagination->per_page;
$curPage = getPageByItemN($itemN, $perPage);
$itemsCount = $pagination->total_rows;
// Generating array of page numbers
$pageNumbers = array();
if ($itemsCount > $perPage)
{
	$j = 1;
	for($i = 0; $i < $itemsCount; $i++)
	{
	  if ($i % $perPage == 0)
	  {
		$pageNumbers[] = $j;
		$j++;
	  }
	}
}
?>
<div class='pages'><div class='block'><div class='inner-block' style='padding-top:2px;'>
<?php
$c = 0;
foreach($pageNumbers as $n)
{
  $class = '';
  if (($c == 0) || ($c == count($pageNumbers) - 1)) $class = 'endpoints';
  if ($n == $curPage)
  {
	if (count($pageNumbers) > 7 AND
		$this->user->user_group == 'admin') :
?>
<span>
<select id='pager_page_selector' style='margin-top:-2px;background:#E2E2E2;color:#BF0090;border-color:#bbb;'>
<? 
foreach($pageNumbers as $m) :
?>
<option value='<?= $m - 1 ?>' <?= $m == $curPage ? 'selected' : '' ?>><?= $m ?></option>
<? endforeach; ?>
</select>
</span>
<? else : ?>
	<span><?= $n ?></span>
<? endif;	
  }
  else if (($n == $curPage - 1) ||
           ($n == $curPage + 1))
  {
    echo '<a class="'.$class.'" href="#" onclick="goto_page(\''.$baseUrl.'/'.getItemNByPage($n, $perPage).'/ajax\');return false;">'.$n.'</a>';
  }
  else if ((($n == $curPage - 2) && ($n != 1)) ||
           (($n == $curPage + 2) && ($n != count($pageNumbers))) )
  {
    echo '<span>...</span>';
  }
  else if (($n <= 3) ||
           ($n > count($pageNumbers) - 3))
  {
    echo '<a class="'.$class.'" href="#" onclick="goto_page(\''.$baseUrl.'/'.getItemNByPage($n, $perPage).'/ajax\');return false;">'.$n.'</a>';
  }
  $c++;
}
?>
</div></div></div>
<script>
	$('select#pager_page_selector').change(function() {
		var offset = $(this).val() * <?= getItemNByPage(2, $perPage) ?>;
		goto_page('<?= $baseUrl ?>/' + offset + '/ajax');
	});

function goto_page(pager_url)
{
	window.location = '#pagerScroll';
	$('em#ajax_message').hide();

    $.ajax({
		url: pager_url,
		success: function (response){
			$('.pages').remove();
			$('#pagerForm,#packagesForm,#ordersForm,#partnersForm,#clientsForm').before(response).remove();
		}});
}
</script>
