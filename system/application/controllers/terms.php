<? require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Terms extends BaseController {
	function __construct()
	{
		parent::__construct();

		Breadcrumb::setCrumb(array(BASEURL => 'Главная'), 0);
		Breadcrumb::setCrumb(array(BASEURL . "terms" => 'Правила использования'), 1, TRUE);
	}
	
	function index()
	{
		View::showChild($this->viewpath.'/pages/index');
	}
}