<label>Посылок на странице:</label>
<select class="per_page" name="per_page" onchange="javascript:updatePerPage(this);">
	<option value="10" <?= $per_page == 10 ? 'selected' : ''?>>10</option>
	<option value="50" <?= $per_page == 50 ? 'selected' : ''?>>50</option>
	<option value="100" <?= $per_page == 100 ? 'selected' : ''?>>100</option>
	<option value="200" <?= $per_page == 200 ? 'selected' : ''?>>200</option>
	<option value="350" <?= $per_page == 350 ? 'selected' : ''?>>350</option>
	<option value="500" <?= $per_page == 500 ? 'selected' : ''?>>500</option>
</select>