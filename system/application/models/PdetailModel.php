<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author omni
 * 
 * моделька для магазина
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class PdetailModel extends BaseModel implements IModel{

	protected  $properties			= null;				// array of properties
	protected  $table				= 'pdetails';		// table name
	protected  $PK					= 'pdetail_id';		// primary key name	
	private    $_status_desc = array(
					'processing' => 'Ждем прибытия', 
					'not_delivered' => 'Не получено',
					'delivered' => 'Получено',
					'exchange' => 'Обмен',
					'return' => 'Возврат'
					);

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->pdetail_id				='';
    	$this->properties->pdetail_client			='';
    	$this->properties->pdetail_manager			='';
    	$this->properties->pdetail_package			='';
    	$this->properties->pdetail_link				='';
    	$this->properties->pdetail_shop_name		='';
    	$this->properties->pdetail_product_name		='';
    	$this->properties->pdetail_product_color	='';
    	$this->properties->pdetail_product_size		='';
    	$this->properties->pdetail_product_amount	='';
    	$this->properties->pdetail_status			='';
		$this->properties->pdetail_price			='';
		$this->properties->pdetail_price_usd		='';
    	$this->properties->pdetail_pricedelivery    ='';
    	$this->properties->pdetail_pricedelivery_usd='';
    	$this->properties->pdetail_img				='';
    	$this->properties->pdetail_special_boxes    ='';
    	$this->properties->pdetail_special_invoices ='';
    	$this->properties->pdetail_foto_request		='';
    	$this->properties->pdetail_img 				='';
    	$this->properties->pdetail_joint_id			='';
    	$this->properties->odetail_id				='';
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
     * Get user list
     *
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
	
	public function addpdetail($pdetail_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($pdetail_obj->$prop)){
				$this->_set($prop, $pdetail_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id)
		{
			return $this->getById($new_id);
		}
		
		return false;
	}
	
	public function updatepdetail($pdetail_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop)
		{
			$this->db->set($prop, $pdetail_obj->$prop);	
		}
		
		$this->db->where($this->getPK(), $pdetail_obj->pdetail_id);
		$result = $this->db->update($this->table);
		
		if (isset($result) && $result)
		{
			return $result;
		}
		
		return false;
	}
	
	public function getFilteredDetails($filter, $clear=false) {
		
		$where = 1;
		if (count($filter)) {
			$where = '';			
			foreach ($filter as $key=>$val) {
				$where .= "`$key` = '$val' AND ";
			}
			$where = substr($where, 0, strlen($where)-5);
		}
		if($clear)	return $this->db->query('
			SELECT `'.$this->table.'`.*
			FROM `'.$this->table."`
			WHERE pdetail_status<>'deleted' AND ".$where
		)->result(); 
		else return $this->db->query('
			SELECT `'.$this->table.'`.*, `countries`.`country_name`, `countries`.`country_id`
			FROM `'.$this->table.'`
				INNER JOIN `managers` ON `'.$this->table.'`.`pdetail_manager` = `managers`.`manager_user` 
				INNER JOIN `countries` ON `managers`.`manager_country` = `countries`.`country_id`
			WHERE '.$where
		)->result();
	}
	
	/**
	 * Формируем заказы юзера (ставим им статус proccessing)
	 */
	public function checkoutClientDetails($client_id, $package_id) {		
		return $this->db->update($this->table, array('pdetail_package' => $package_id), array('pdetail_client' => $client_id, 'pdetail_package' => 0));
	}
	
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}

	public function getStatuses()
	{
		return $this->_status_desc;
	}

	public function getPackageDetailsStatusDescription($detail_status)
	{
		return $this->_status_desc[$detail_status];
	}

	/**
	 * Get package details
	 *
	 * @return array
	 */
	public function getPackageDetails($id)
	{
		$result = $this->db->query('
			SELECT 
				`pdetails`.*,
				`pdetail_joints`.pdetail_foto_request as joint_foto_request, 
				`pdetail_joints`.pdetail_joint_count as joint_count
			FROM `pdetails`
			LEFT OUTER JOIN `pdetail_joints` ON
				`pdetail_joints`.`pdetail_joint_id` = `pdetails`.`pdetail_joint_id`
			WHERE `pdetails`.`pdetail_status` <> "deleted"
				AND `pdetails`.`pdetail_package` = "'.intval($id).'"
			ORDER BY 
				`pdetails`.`pdetail_joint_id` DESC,
				`pdetails`.`pdetail_id` ASC
		')->result();

		// статус джоинтов
		if (count($result) > 0 &&  $result)
		{
			$joint_statuses = array();
		
			// расчет статуса
			foreach ($result as $pdetail)
			{
				if ($pdetail->pdetail_joint_id)
				{
					if ($pdetail->pdetail_status != 'purchased' &&
						$pdetail->pdetail_status != 'received' && 
						$pdetail->pdetail_status != 'not_delivered')
					{
						$joint_statuses[$pdetail->pdetail_joint_id] = 1;
					}
					else if (empty($joint_statuses[$pdetail->pdetail_joint_id]))
					{
						$joint_statuses[$pdetail->pdetail_joint_id] = 0;
					}
				}
			}
			
			// запись статуса
			foreach ($result as $pdetail)
			{
				if ($pdetail->pdetail_joint_id)
				{
					$pdetail->pdetail_joint_enabled = $joint_statuses[$pdetail->pdetail_joint_id];
				}
			}
		}
		
		return ((count($result) > 0 &&  $result) ? $result : false);		
	}

	public function clearJoints($id)
	{
		$this->db->query('
			UPDATE `pdetails`
			SET `pdetails`.`pdetail_joint_id` = 0
			WHERE `pdetails`.`pdetail_joint_id` = "'.intval($id).'"
		');
	
		$this->db->query('
			DELETE FROM `pdetail_joints`
			WHERE `pdetail_joints`.`pdetail_joint_id` = "'.intval($id).'"
		');
	}

	public function setStatus($id, $status)
	{
		$this->db->query('
			UPDATE `pdetails` 
			SET `pdetail_status` = \''.$status.'\'
			WHERE `pdetails`.`pdetail_id` = '.intval($id).'
		');
		
		return ;
	}
	
	public function getClientpdetailById($id, $client_id) {		
		$row = $this->db->query('
			SELECT `pdetails`.*
			FROM `pdetails`
			INNER JOIN `packages` ON `pdetails`.`pdetail_package` = `packages`.`package_id`
			WHERE `pdetails`.`pdetail_id` = '.intval($id).'
				AND `packages`.`package_client` = '.intval($client_id).'
		')->result();
		
		if (!$row || count($row) != 1)
		{
			return false;
		}

		return $row[0];		
	}
	
	public function getManagerpdetailById($id, $manager_id) {		
		$row = $this->db->query('
			SELECT `pdetails`.*
			FROM `pdetails`
			INNER JOIN `packages` ON `pdetails`.`pdetail_package` = `packages`.`package_id`
			WHERE `pdetails`.`pdetail_id` = '.intval($id).'
				AND `packages`.`package_manager` = '.intval($manager_id).'
		')->result();
		
		if (!$row || count($row) != 1)
		{
			return false;
		}

		return $row[0];		
	}
	
	public function getPackagesFoto(array $pdetails)
	{
		$packFotos	= array();
		
		foreach ($pdetails as $pdetail)
		{
			if ($pdetail->pdetail_joint_id)
			{
				continue;
			}
			
			$scandir = UPLOAD_DIR."packages/{$pdetail->pdetail_package}/{$pdetail->pdetail_id}/";
			
			if (is_dir($scandir))
			{
				foreach (scandir($scandir) as $scanFile)
				{
					if ($scanFile != '.' && $scanFile != '..')
					{
						$packFotos[$pdetail->pdetail_id][] = $scanFile;
					}
				}
			}
		}
		
		return $packFotos;
	}

	public function getPackagesFotoCount(array $pdetails)
	{
		$fotoCount = 0;
		
		foreach ($pdetails as $pdetail)
		{
			$scandir = UPLOAD_DIR."packages/{$pdetail->pdetail_package}/{$pdetail->pdetail_id}/";
			
			if (is_dir($scandir))
			{
				foreach (scandir($scandir) as $scanFile)
				{
					if ($scanFile != '.' && $scanFile != '..')
					{
						$fotoCount++;
					}
				}
			}
		}
		
		return $fotoCount;
	}
}
?>