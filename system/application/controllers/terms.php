<? require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Terms extends BaseController {
	function __construct()
	{
		parent::__construct();

		Breadcrumb::setCrumb(array($this->config->item('base_url') => 'Главная'), 0);
		Breadcrumb::setCrumb(array($this->config->item('base_url') . "terms" => 'Правила использования'), 1, TRUE);
	}
	
	function index()
	{
		View::showChild($this->viewpath.'/pages/index');
	}
}