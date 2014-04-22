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
        View::show('moneysend/index',array('money'=>$this->m->select(),'currency'=>$this->_get_exchange_rate_table('RUB')));
    }

    public function _get_exchange_rate_table($cur_name = false)
    {
        $currencies = array('UAH','RUB','CNY','USD');
        if(!$cur_name OR !in_array($cur_name, $currencies))
        {
            return false;
        }
        $this->load->model('CurrencyModel', 'Currencies');
        $view = array (
            'cur_currency'=>$cur_name,
            'currencies'=>$this->Currencies->getExchangeCurrencies($currencies, true),
            'rate_usd' => $this->Currencies->getExchangeRate($cur_name, 'USD', 'landing'),
            'rate_kzt' => $this->Currencies->getExchangeRate($cur_name, 'CNY', 'landing'),
            'rate_uah' => $this->Currencies->getExchangeRate($cur_name, 'UAH', 'landing'),
            'rate_rur' => $this->Currencies->getExchangeRate($cur_name, 'RUB', 'landing')
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

            case 'moneysend':
                $post['id']       = $this->input->post('id');
                $post['price']    = $this->input->post('price');
                $post['contacts'] = $this->input->post('contacts');
                foreach($post as $k => $v)
                {
                    if(empty($v))
                    {
                        $response['message'] = 'Все поля обязательны для заполнения';
                        die(json_encode($response));
                    }
                }
                $this->_notify($post);
                die(json_encode(array('status'=>'success','message'=>'Вы успешно отправили заявку на перевод денег.')));

            default:
                show_404();die();
        }
    }

    public function _notify($data)
    {
        $this->load->model('MoneysendModel','m');
        $this->load->library('email');
        $result = current($this->m->select(array('id'=>$data['id'])));

        $msg = 'Заявка на перевод денег - "'.$result->name.'"<br/>
        Сумма - '.$data['price'].' '.$result->currency.'<br/>
        Итого к оплате - '.((float)$data['price']+((float)$data['price']/100*(float)$result->percent)).' '.$result->currency.'<br/>
        Контактные данные - "'.$data['contacts'].'"';

        $this->email->from('info@countrypost.ru', 'Countrypost.ru');
        $this->email->to('at3@yandex.ru');
        $this->email->subject('Countrypost.ru');
        $this->email->message($msg);
        return $this->email->send();
    }
}