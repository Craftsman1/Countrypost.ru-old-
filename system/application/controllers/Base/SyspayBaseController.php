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

class SyspayBaseController extends BaseController 
{
	function __construct()
	{
		
		parent::__construct();
		
		header("Content-Type: text/html; charset=UTF-8");
		
		if (Check::user()){
			$this->load->model('UserModel', 'User');
			foreach ($this->User->getPropertyList() as $prop){
				$this->session->unset_userdata(array($prop=>''));
			}
			
			$this->session->set_userdata($this->User->getById($this->user->user_id));
			$this->user = Check::user();
			View::$data['user']	= $this->user;
		}
	}
}
?>