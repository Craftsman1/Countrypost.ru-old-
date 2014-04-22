<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
function editor($name='text',$height=400,$width=798,$mode='Basic')
{
	echo "var oFCKeditor = new FCKeditor('$name', $width, $height);
oFCKeditor.ToolbarSet='".$mode."';
oFCKeditor.BasePath='/system/plugins/fckeditor/';
oFCKeditor.ReplaceTextarea();";
}
?>