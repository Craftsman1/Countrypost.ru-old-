<?php
if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}
/**
 * Базовый контроллер клиента.
 * Имена переменных и загружаемых сдесь моделей начинаются с "__", дабы исключить возможность перегрузки онных
 *
 */

require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

abstract class ClientBaseController extends BaseController 
{
	protected $__partners;
	protected $__client;
	
	function __construct()
	{
		parent::__construct();
		
		$user	= Check::user();
		if (!$user || $user->user_group !== 'client'){
			Func::redirect('/');
			die('Access restricted');
		}
		
		#$this->load->helper('ssl');
		#ssl_on();
	}
}
?>