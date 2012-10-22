<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Terms extends BaseController {
	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		View::showChild($this->viewpath.'/pages/index');
	}
}