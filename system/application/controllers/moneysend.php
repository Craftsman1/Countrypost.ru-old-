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
        $this->load->model('MoneysendModel','m');
        View::show('moneysend/index',array('money'=>$this->m->select(),'currency'=>$this->_get_exchange_rate_table('AUD')));
    }

    public function _get_exchange_rate_table($cur_name = false)
    {
        $currencies = array('AUD', 'BRL', 'BTC', 'CAD', 'CHF', 'CNY', 'KRW', 'EUR', 'GBP', 'INR', 'HKD', 'JPY', 'NZD', 'SEK', 'SGD', 'USD');
        if(!$cur_name OR !in_array($cur_name, $currencies))
        {
            return false;
        }
        $this->load->model('CurrencyModel', 'Currencies');
        $view = array (
            'cur_currency'=>$cur_name,
            'currencies'=>$this->Currencies->getExchangeCurrencies(),
            'rate_usd' => $this->Currencies->getExchangeRate($cur_name, 'USD', 'client'),
            'rate_kzt' => $this->Currencies->getExchangeRate($cur_name, 'KZT', 'client'),
            'rate_uah' => $this->Currencies->getExchangeRate($cur_name, 'UAH', 'client'),
            'rate_rur' => $this->Currencies->getExchangeRate($cur_name, 'RUB', 'client')
        );
        return $this->load->view('/moneysend/exchange_rates', $view, true);
    }

    public function ajax()
    {
        $post['action'] = $this->input->post('action');
        if(empty($post['action'])) show_404();

        $response = array('status'=>'error','message'=>'');
        switch($post['action'])
        {
            case 'exchange_rate':
                $post['cur_name'] = $this->input->post('cur_name');
                $view = $this->_get_exchange_rate_table($post['cur_name']);
                if(!$view) $response['message'] = 'Неверные данные';
                else $response = array('status'=>'success','view'=>$view);

                die(json_encode($response));
                break;
            default:
                show_404();die();
        }
    }
}