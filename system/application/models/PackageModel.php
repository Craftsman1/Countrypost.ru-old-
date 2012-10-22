<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author tua
 * 
 * модель для посылки
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class PackageModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'packages';		// table name
	protected	$PK					= 'package_id';		// primary key name	
	
	private $statuses = array(
		'processing'	=> 'Ждем прибытия',
		'not_payed'		=> 'Не оплачено',
		'payed'			=> 'Оплачено',
		'sent'			=> 'Отправлено',
		'not_delivered' => 'Не получено');
	
	private $filter_statuses = array(
		'processing'	=> 'Ждем прибытия',
		'not_payed'		=> 'Не оплачено',
		'not_delivered' => 'Не получено');
	
	public function getPackageStatusDescription($package_status)
    {
		if ($package_status != '' AND 
			$package_status != 'deleted')
		{
			return $this->statuses[$package_status];
		}
		
		return '';
    }
	
	/**
	 * конструктор
	 */
	function __construct()
    {
    	$this->properties								= new stdClass();
    	$this->properties->package_id					='';
    	$this->properties->package_client				='';
    	$this->properties->package_manager				='';
    	$this->properties->package_weight				='';
    	$this->properties->package_cost					='';
    	$this->properties->package_cost_payed			='';
    	$this->properties->package_delivery_cost		='';
    	$this->properties->package_declaration_cost		='';
    	$this->properties->package_comission			='';
		$this->properties->package_manager_comission	='';
		$this->properties->package_manager_comission_payed	='';
    	$this->properties->package_system_comission		='';
    	$this->properties->package_system_comission_payed	='';
    	$this->properties->package_status				='';
    	$this->properties->package_date					='';
    	$this->properties->package_address				='';
    	$this->properties->declaration_status			='';
		$this->properties->comment_for_manager			='';
		$this->properties->comment_for_client			='';
		$this->properties->package_trackingno			='';
		$this->properties->package_manager_login		='';
		$this->properties->package_manager_country		='';
		$this->properties->package_age					='';
		$this->properties->package_country_from			='';
		$this->properties->package_country_to			='';
		$this->properties->package_delivery				='';
		$this->properties->package_delivery_name		='';
		$this->properties->package_delivery_list		='';
		$this->properties->package_join_count 			='';
		$this->properties->package_join_cost			='';
		$this->properties->package_join_ids				='';
        $this->properties->package_insurance			='';
        $this->properties->package_insurance_cost		='';
        $this->properties->package_insurance_comission	='';
		$this->properties->package_manager_cost			='';
		$this->properties->package_manager_cost_payed	='';
		$this->properties->package_payed_to_manager		='';

		$this->properties->package_manager_cost_local	='';
		$this->properties->package_manager_cost_payed_local	='';
		$this->properties->package_delivery_cost_local	='';
		$this->properties->package_manager_comission_local	='';
		$this->properties->package_manager_comission_payed_local	='';
		$this->properties->package_special_comment		='';
		$this->properties->package_special_cost			='';
		$this->properties->package_special_cost_usd		='';
		$this->properties->package_foto_cost			='';
		$this->properties->package_foto_cost_local		='';
		$this->properties->package_foto_cost_system		='';
		$this->properties->package_foto_cost_system_local	='';
		
		$this->properties->order_id						='';

        parent::__construct();
    }
    
   /**
     * @see IModel
     * Инкапсуляция
     *
     * @return string
     */
	public function getPK()
	{
		return $this->PK;
	}
	
    /**
     * @see IModel
     * Инкапсуляция
     *
     * @return string
     */	
	public function getTable()
	{
		return $this->table;
	}     
    
    /**
     * Get delivery list
     */
	public function getList()
	{
		$sql = $this->select();
		return ($sql)?($sql):(false);
	}	
	
	/**
	 * Get property list
	 *
	 * @return array
	 */
	public function getPropertyList()
	{
		return array_keys((array) $this->properties);
	}	
	
	/**
	 * Get package by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}
	
	/**
	 * Get new packages by manager id
	 *
	 * @return array
	 */
	public function getNewPackagesByManagerId($id) {		
		$result = $this->select(array('package_manager' => $id, 'package_status' => 'not_payed'));
		
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}

	
	public function getByManagerId($id){
		if (!is_numeric($id))
			return false;
			
		return $this->select(array('package_manager' => $id));
	}
	

	public function getByClientId($id, $status = null){
		if (!is_numeric($id))
			return false;
			
		return $this->select(array('package_client' => $id));
	}
	
	
	/**
	 * Фильтрует посылки по статусам:
		1. sent возвращает отправленные
		2. payed - оплаченные (для партнера и админа, у клиента такой страниц нет)
		3. open - все остальные (для партнера и админа)
				- все которые не отправлены
	 *
	 * @return array
	 */
	public function getPackages($filter=null, $packageStatus='not_payed', $clientId=null, $managerId=null) {
		$managerFilter = '';
		$periodFilter = '';
		$idFilter = '';
		$clientIdAccess = '';
		$managerIdAccess = '';
		$statusFilter = '';
		
		// обработка статуса
		if ($packageStatus == 'open')
		{
			$statusFilter = 'NOT (`packages`.`package_status` IN ("deleted", "payed", "sent"))';
		}
		else
		{
			$statusFilter = '`packages`.`package_status` = "'.$packageStatus.'"';
		}		
		
		// обработка фильтра
		if (isset($filter))
		{
			if (is_numeric($filter->manager_user))
			{
				$managerFilter = ' AND `managers`.`manager_user` = '.$filter->manager_user;
			}

			if (is_numeric($filter->search_id))
			{
				if ($filter->id_type == 'package')
				{
					$idFilter = ' AND `packages`.`package_id` = '.$filter->search_id;
				}
				else if ($filter->id_type == 'client')
				{
					$idFilter = ' AND `packages`.`package_client` = '.$filter->search_id;
				}
			}

			if ($filter->period == 'day' ||
				$filter->period == 'week' ||
				$filter->period == 'month')
			{
				$periodFilter = ' AND TIMESTAMPDIFF('.strtoupper($filter->period).', `packages`.`package_date`, NOW()) < 1';
			}
			
			if ( ! empty($filter->id_status))
			{
				$statusFilter = '`packages`.`package_status` = "'.$filter->id_status.'"';
			}
		}
		
		// обработка ограничения доступа клиента и менеджера
		if (isset($clientId))
		{
			$clientIdAccess = ' AND `packages`.`package_client` = '.$clientId;		
		}
		else if (isset($managerId))
		{
			$managerIdAccess = ' AND `packages`.`package_manager` = '.$managerId;		
		}		
		
		// выборка
		$result = $this->db->query('
			SELECT `packages`.*, @package_day:=TIMESTAMPDIFF(DAY, `packages`.`package_date`, NOW()) as package_day,
				DATE_FORMAT(`package_date`, "%d.%m.%Y %h:%i") AS `package_date`,
				`users`.`user_login`  as `package_manager_login`, 
				`countries`.`country_name` as `package_manager_country`,
				`deliveries`.`delivery_name` as `package_delivery_name`,
				TIMESTAMPDIFF(HOUR, `packages`.`package_date`, NOW() - INTERVAL @package_day DAY) as `package_hour`
			FROM `packages`
			INNER JOIN `users` on `users`.`user_id` = `packages`.`package_manager`
			INNER JOIN `managers` on `managers`.`manager_user` = `packages`.`package_manager`
			INNER JOIN `countries` on `managers`.`manager_country` = `countries`.`country_id`
			LEFT JOIN `deliveries` on `deliveries`.`delivery_id` = `packages`.`package_delivery`
			WHERE '
			.$statusFilter
			.$managerFilter
			.$periodFilter
			.$idFilter
			.$clientIdAccess
			.$managerIdAccess.'
			ORDER BY `packages`.`package_date` DESC'
		)->result();
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}
	
	/**
	 * Updates packages with available deliveries
	 *
	 * @return array
	 */
	public function getAvailableDeliveries($packages, $deliveries) {
		if (!$packages) return false;
		
		foreach ($packages as $package)
		{
			// проверка доступности списка способов доставки
			if (!$package->package_country_from ||
				!$package->package_country_to ||
				!$package->package_status == 'payed')
			{
				$package->delivery_list = false;
				continue;
			}
			
			// выборка способов доставки
			$package->delivery_list = $deliveries->getAvailableDeliveries(
				$package->package_country_from, 
				$package->package_country_to, 
				$package->package_manager);
		}
		
		return $packages;
	}
	
	/**
	 * Get payed packages by manager id
	 *
	 * @return array
	 */
	public function getPayedPackagesByManagerId($id){
		
		$result = $this->select(array('package_manager' => $id, 'package_status' => 'payed'));
		
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}
	
	/**
	 * Get sent packages by manager id
	 *
	 * @return array
	 */
	public function getSentPackagesByManagerId($id)
	{
		$result = $this->select(array('package_manager' => $id, 'package_status' => 'sent'));
		
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}
	
	/**
	 * Возвращает посылку, если она есть у партнера
	 *
	 * @return array
	 */
	public function getManagerPackageById($package_id, $manager_id){
		$package = $this->getById($package_id);
		
		if ($package &&
			$package->package_manager == $manager_id)
		{
			return $package;
		}

		return false;
	}
	
	/**
	 * Возвращает посылку, если она есть у клиента
	 *
	 * @return array
	 */
	public function getClientPackageById($package_id, $client_id){
		$package = $this->getById($package_id);
		
		if ($package &&
			$package->package_client == $client_id)
		{
			return $package;
		}

		return false;
	}
	
	/**
	 * Get all statuses
	 *
	 * @return array
	 */
	public function getStatuses() {
		return $this->statuses;
	}
	
	public function getFilterStatuses() {
		return $this->filter_statuses;
	}
	
	// пересчитываем статус и стоимость посылки
	public function recalculatePackage($package)
	{
		// 1. вычисляем статус посылки
		$recent_status = $package->package_status;
		$status = $this->getTotalStatus($package->package_id, $recent_status);
				
		if ( ! $status)
		{
			throw new Exception('Невозможно вычислить статус посылки. Попоробуйте еще раз.');
		}
		
		$is_new_status = ($recent_status != $status);
		
		if ($is_new_status)
		{
			$package->package_status = $status;
		}
		
		// 2. пересчитываем стоимость посылки
		$ci = get_instance();
		$ci->load->model('ConfigModel', 'Config');
		$package = $this->calculateCost($package, $ci->Config);
		
		if ( ! $package)
		{
			throw new Exception('Невожможно вычислить стоимость посылки. Попоробуйте еще раз.');
		}
		
		// 3. сохраняем все изменения в посылке
		$new_package = $this->savePackage($package);
		
		if ( ! $new_package)
		{
			throw new Exception('Невожможно вычислить статус и стоимость посылки. Попоробуйте еще раз.');
		}
		
		return $new_package;
	}
	
	/**
	 * Вычисление статуса посылки по статусам товаров:
		'processing','not_delivered','exchange','delivered','return','deleted'
		возможные статусы посылки:
		'processing','not_payed','payed','sent','not_delivered','deleted'

	 Статусы товаров в посылках должны быть следующие:
		•	Ждем прибытия - ставится автоматом при добавлении товара в посылку (если хотя бы у одного товара в посылке стоит статус Ждем прибытия, то у всего заказа статус будет Ждем прибытия")
		•	Не получено или Обмен - если хотя бы у одного товара в посылке статус стоит не получено у всей посылке статус будет Не получено
		•	Получено - если у всех товаров в посылке стоит статус «Получено», статус посылки ставится «Не оплачено» (дальше после оплаты посылки статус ставится «Оплачена» и дальше «Отправлена» как сейчас).
		•	Возврат - просто ставится для информации. не связан со статусом всей посылки.
	 *
	 * @return string
	 */
	public function getTotalStatus($package_id, $default)
	{
		$row = $this->db->query('
			SELECT MAX(`pdetails`.`pdetail_status`) as `status`
			FROM `pdetails`
			WHERE `pdetails`.`pdetail_package` = '.intval($package_id).'
				AND NOT (`pdetails`.`pdetail_status` IN (
					"deleted", 
					"return"))
			GROUP BY `pdetails`.`pdetail_package`
		')->result();
		
		// если не нашли товары со статусами 'processing','not_delivered','exchange','delivered' оставляем прошлый статус
		if ( ! $row OR 
			count($row) != 1)
		{
			return $default;
		}

		$status = $row[0]->status;
		
		// для статуса 'delivered' проверяем статусы посылки, чтобы не сбрасывать например у доставленных посылок их статусы
		// или возвращаем 'not_payed'
		if ($status == 'delivered')
		{
			if ($default == 'not_payed' OR
				$default == 'payed' OR
				$default == 'sent')
			{
				return $default;
			}
			else
			{
				return 'not_payed';
			}
		}
		
		// вместо 'exchange' возвращаем 'not_delivered'
		if ($status == 'exchange')
		{
			return 'not_delivered';
		}
		
		// статусы 'processing','not_delivered' возвращаем как есть
		return $status;		
	}
	
	/**
	 * Добавление/изменение посылки
	 * Выкидывает исключения на некорректные данные
	 * 
	 * @param (object) 	- $package
	 * @return (mixed)	- объект package или false в случае ошибки записи в базу
	 */
	public function savePackage($package)
	{		
		$props = $this->getPropertyList();

		foreach ($props as $prop){
			if (isset($package->$prop)){
				$this->_set($prop, $package->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ( ! $new_id) return false;
		
		return $this->getInfo(array($new_id));
	}

	/**
	 * Рассчитывает стоимость посылки
	 *
	 * @return array
	 */
	public function calculateCost($package, $config, $pricelist=null)
	{
		$ci = get_instance();
		$ci->load->model('CurrencyModel', 'Currencies');
		$ci->load->model('CountryModel', 'Countries');
		$ci->load->model('ManagerModel', 'Manager');
		$ci->load->model('TaxModel', 'Taxes');
		$ci->load->model('PdetailModel', 'Pdetail');
		$ci->load->model('PdetailJointModel', 'Joints');
			
		$country = $ci->Countries->getById($package->package_country_from);
		$cross_rate = $ci->Currencies->getById($country->country_currency);

		// комиссии для страны
		$tax = $ci->Taxes->getByCountryId($package->package_country_from);

		if ($tax === FALSE)
		{
			throw new Exception('Невозможно рассчитать стоимость посылки. Данные для расчета недоступны.');
		}		
		
		// комиссия партнера, %
		$manager = $ci->Manager->getById($package->package_manager);
		
		if ( ! $manager)
		{
			throw new Exception('Невозможно рассчитать стоимость посылки. Менеджер не найден.');
		}

		// стоимость заполнения декларации
		if ($package->declaration_status == 'help')
		{
			$package->package_declaration_cost = $tax->package_declaration;
		}
		else if (empty($package->package_declaration_cost))
		{
			$package->package_declaration_cost = 0;
		}
		
		// стоимость объединения посылок
		if ($package->package_join_count)
		{
			$package->package_join_cost = 
				$tax->package_joint * 
				$package->package_join_count;
		}

		// стоимость страховки
		if (isset($package->package_insurance) && 
			$package->package_insurance && 
			$package->package_insurance_cost)
		{
			// максимальная стоимость			
			if ((float)$tax->max_package_insurance < (float)$package->package_insurance_cost)
			{
				$package->package_insurance_cost = $tax->max_package_insurance;
			}
		
			$package->package_insurance_comission = ceil(
				$tax->package_insurance * 
				$package->package_insurance_cost *
				0.01);
		}
		else
		{
			$package->package_insurance = 0;
			$package->package_insurance_cost = 0;
			$package->package_insurance_comission = 0;
		}
		
		// стоимость международной доставки
		if (isset($package->package_delivery) &&
			$package->package_delivery &&
			isset($pricelist))
		{
			$package->package_delivery_cost = 
				$pricelist->getPriceByWeight(
					$package->package_weight, 
					$package->package_country_from, 
					$package->package_country_to, 
					$package->package_delivery);

			$package->package_delivery_cost_local = 
				$pricelist->getPriceByWeightLocal(
					$package->package_weight, 
					$package->package_country_from, 
					$package->package_country_to, 
					$package->package_delivery);
			
			if ( ! $package->package_delivery_cost || 
				 ! $package->package_delivery_cost_local)
			{
				throw new Exception('Невозможно рассчитать стоимость посылки. Ошибка расчета международной доставки.',-1);
			}			
		}

		// стоимость фото товаров
		$package->package_foto_cost = 0;
		$package->package_foto_cost_system = 0;
		$package->package_foto_cost_local = 0;
		$package->package_foto_cost_system_local = 0;
		
		// 1. фото, запрошенные клиентами
		$pdetails_client = $ci->Pdetail->getFilteredDetails(
			array(
				'pdetail_package' => $package->package_id,
				'pdetail_foto_request' => 1,
				'pdetail_joint_id' => 0
			),
			true);
			
		$pdetails_client_joined_count = $ci->Joints->getPackageJointsCount($package->package_id);
		$requested_foto_count = count($pdetails_client) + $pdetails_client_joined_count;
		
		if ($requested_foto_count)
		{
			$package->package_foto_cost = 
				$requested_foto_count * $tax->package_foto;	
		}
		
		// 2. фото, залитые по инициативе партнера или админа
		$pdetails = $ci->Pdetail->getFilteredDetails(
			array(
				'pdetail_package' => $package->package_id,
				'pdetail_joint_id' => 0
			),
			true);
			
		// TODO: добавить объединенные фото залитые партнером
		if ($pdetails)
		{
			$foto_count = $ci->Pdetail->getPackagesFotoCount($pdetails);
			
			if ($foto_count > $requested_foto_count)
			{
				$package->package_foto_cost_system = 
					($foto_count - $requested_foto_count) * $tax->package_foto_system;
			}
		}
		
		// 3. стоимость фото в местной валюте
		$package->package_foto_cost_local = $this->convert($manager->manager_country, $package->package_foto_cost);
		$package->package_foto_cost_system_local = $this->convert($manager->manager_country, $package->package_foto_cost_system);
		
		// общая комиссия
		$package->package_comission = (empty($package->order_id) ? $tax->package_disconnected : $tax->package);

		// общая стоимость
		$package->package_cost = 
			$package->package_delivery_cost +
			$package->package_declaration_cost +
			$package->package_join_cost +
            $package->package_insurance_comission +
			$package->package_special_cost_usd +
			$package->package_foto_cost + 
			$package->package_foto_cost_system +
			$package->package_comission;

		// комиссия партнера в местной валюте
		$package->package_manager_comission_local = 
			empty($package->order_id) ? 
			$manager->package_disconnected_tax : 
			$manager->package_tax;

		// комиссия партнера в долларах
		$package->package_manager_comission = 
			(0.01 * ceil($package->package_manager_comission_local * 100 / $cross_rate->cbr_cross_rate));

		// сумма для оплаты партнеру в местной валюте
		$package->package_manager_cost_local = 
			$package->package_delivery_cost_local +
			$package->package_manager_comission_local +
			$package->package_special_cost +
			($package->package_foto_cost_local * intval($manager->package_foto_tax)) + 
			($package->package_foto_cost_system_local * intval($manager->package_foto_system_tax));
			
		// сумма для оплаты партнеру в долларах
		$package->package_manager_cost = 
			$package->package_delivery_cost +
			(0.01 * ceil($package->package_manager_comission_local * 100 / $cross_rate->cbr_cross_rate)) +
			($package->package_foto_cost * intval($manager->package_foto_tax)) + 
			($package->package_foto_cost_system * intval($manager->package_foto_system_tax)) +
			$package->package_special_cost_usd;
			
		// комиссия системы
		$package->package_system_comission = 
			$package->package_cost -
			$package->package_manager_cost;
		
		return $package->package_cost ? $package : false;
	}

	private function convert($country_id, $price)
	{
		$ci = get_instance();
		$ci->load->model('CountryModel', 'Countries');
		$ci->load->model('CurrencyModel', 'Currencies');
		
		$country = $ci->Countries->getById($country_id);
		$cross_rate = $ci->Currencies->getById($country->country_currency);
		
		if (!$cross_rate)
		{
			throw new Exception('Невозможно конвертировать цену в доллары. Попробуйте еще раз.');
		}
		
		return $price * $cross_rate->cbr_cross_rate;
	}
	
	public function getPackageInsuranceCost()
	{
		$config = $config->getConfig();
		if ( ! $config)
		{
			throw new Exception('Невозможно рассчитать стоимость посылки. Данные для расчета недоступны.',-1);
		}
		return $package->insurance * $config['price_for_insurance']->config_value;
	}

	public function autocomplete($query) 
	{
		$r = $this->db->query('
			SELECT GROUP_CONCAT(`package_id` SEPARATOR ",") AS ids
			FROM (
				SELECT `package_id` 
				FROM `'.$this->table."`
				WHERE `package_id` LIKE '$query%'
					AND	`package_status` <> 'deleted'
				ORDER BY `package_id`
				LIMIT 10) 
			AS A"			
		)->result();
		
		return ((count($r == 1) &&  $r) ? $r[0]->ids : false);
	}

	public function autocompleteManager($query, $manager_id) 
	{
		$r = $this->db->query('
			SELECT GROUP_CONCAT(`package_id` SEPARATOR ",") AS ids
			FROM (
				SELECT `package_id` 
				FROM `'.$this->table."`
				WHERE `package_id` LIKE '$query%'
					AND	`package_status` <> 'deleted'
					AND `package_manager` = {$manager_id}
				ORDER BY `package_id`
				LIMIT 10) 
			AS A"			
		)->result();
		
		return ((count($r == 1) &&  $r) ? $r[0]->ids : false);
	}
	
	public function getPackageFotos($package_id, $pdetails)
	{
		$fotos	= array();
		
		if ( ! is_numeric($package_id))
		{
			return $fotos;
		}
		
		foreach ($pdetails as $pdetail)
		{
			if ( ! empty($pdetail->pdetail_joint_id))
			{
				continue;
			}
			
			$counter = 1;
			
			$scandir = UPLOAD_DIR."packages/{$package_id}/{$pdetail->pdetail_id}/";
			
			if (is_dir($scandir))
			{
				foreach (scandir($scandir) as $filename)
				{
					$filepath = $scandir . $filename;
					
					if (is_file($filepath))
					{
						if (empty($fotos[$pdetail->pdetail_id]))
						{
							$fotos[$pdetail->pdetail_id] = array();
						}
						
						$fotos[$pdetail->pdetail_id]["photo $counter.jpg"] = $filepath;
						$counter++;
					}
				}
			}
		}
		
		return $fotos;
	}
	
	public function getPackageJointFotos($package_id, $pdetails)
	{
		$fotos	= array();
		$pdetail_joint_id = 0;
		
		if ( ! is_numeric($package_id))
		{
			return $fotos;
		}
		
		foreach ($pdetails as $pdetail)
		{
			if (empty($pdetail->pdetail_joint_id) OR
				$pdetail_joint_id == $pdetail->pdetail_joint_id)
			{
				continue;
			}
			else
			{
				$pdetail_joint_id = $pdetail->pdetail_joint_id;
			}
			
			$counter = 1;
			
			$scandir = UPLOAD_DIR."packages/{$package_id}/joint_{$pdetail_joint_id}/";
			
			if (is_dir($scandir))
			{
				foreach (scandir($scandir) as $filename)
				{
					$filepath = $scandir . $filename;
					
					if (is_file($filepath))
					{
						if (empty($fotos[$pdetail_joint_id]))
						{
							$fotos[$pdetail_joint_id] = array();
						}
						
						$fotos[$pdetail_joint_id]["photo $counter.jpg"] = $filepath;
						$counter++;
					}
				}
			}
		}
		
		return $fotos;
	}
}
?>