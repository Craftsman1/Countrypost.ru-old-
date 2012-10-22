<?php
if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;
/**
 * Контроллер с правами управления посылкой
 *
 */


class UpdateController extends BaseController {

	public function __construct()
	{
		parent::__construct();
	}
	
}
?>