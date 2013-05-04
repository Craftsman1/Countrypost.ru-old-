<span class="label">
	Скриншот (max. 3 MB):
</span>
<span class="label screenshot_switch" style="font-size:11px;margin:0;width:300px;padding-top: 4px;">
	<a href="javascript: showScreenshotLink();">Добавить ссылку</a>&nbsp;или&nbsp;<a href="javascript: showScreenshotUploader();" class="screenshot_switch">Загрузить файл</a>
</span>
<input class="textbox screenshot_link_box screenshot_default"
	   type='text'
	   id='oimg'
	   name="userfileimg"
	   style='display:none;width:180px;'
	   value=""
	   onfocus="screenshotUnDefault(this);"
	   onblur="screenshotDefault(this);">
<input class="textbox screenshot_uploader_box"
	   type='file'
	   id='ofile'
	   name="userfile"
	   style='display:none;'>
<span class="label screenshot_link_box screenshot_uploader_box"
	  style='display:none;'
	  onclick="showScreenshotSwitch();">
	<img border="0" src="/static/images/delete.png" title="Удалить">
</span>
