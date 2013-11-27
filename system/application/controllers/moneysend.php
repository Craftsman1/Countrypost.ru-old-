<? require_once BASE_CONTROLLERS_PATH . 'BaseController' . EXT;

class Moneysend extends BaseController {

    function __construct()
    {
        parent::__construct();
        Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
        Breadcrumb::setCrumb(array('/manager/orders/' => 'Мои заказы'), 1, TRUE);
    }

    function index()
    {
        //$this->css('money.css');
        View::show('moneysend/index');
    }
}