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
class PdetailJointModel extends BaseModel implements IModel{

	protected  $properties			= null;				// array of properties
	protected  $table				= 'pdetail_joints';			// table name
	protected  $PK					= 'pdetail_joint_id';		// primary key name	

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->pdetail_joint_id	= '';
    	$this->properties->package_id = '';
    	$this->properties->pdetail_foto_request	= '0';
    	$this->properties->pdetail_joint_count = '0';

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
		return ($sql)?($sql):(FALSE);
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
	
	public function saveJoint($pdetail_joint_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($pdetail_joint_obj->$prop)){
				$this->_set($prop, $pdetail_joint_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id){
			return $this->getInfo(array($new_id));
		}
		
		return FALSE;
	}
	
	public function generate($package_id)
	{
		$this->_set('package_id', $package_id);

		$new_id = $this->save(true);
		
		if ($new_id)
		{
			return $this->getById($new_id);
		}
		
		return FALSE;
	}
	
	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}
	
	public function cleanupJoints($package_id)
	{
		$this->db->query('
			DELETE FROM `pdetail_joints`
			WHERE `pdetail_joint_count` < 1
				AND `package_id` = "'.intval($package_id).'"
		');
	}

	public function deleteJoint($joint)
	{
		$this->db->query('
			UPDATE 
				`pdetails`
				INNER JOIN `pdetail_joints`
					ON `pdetail_joints`.pdetail_joint_id = `pdetails`.`pdetail_joint_id`
			SET `pdetails`.pdetail_joint_id = 0
			WHERE 
				`pdetail_joints`.`pdetail_joint_id` = ' . intval($joint->pdetail_joint_id) . '
		');
		
		$this->db->query('
			DELETE FROM `pdetail_joints`
			WHERE `pdetail_joint_id` = "'.intval($joint->pdetail_joint_id).'"
		');
	}

	public function getPackageJointsCount($package_id)
	{
		$result = $this->db->query('
			SELECT COUNT(DISTINCT  `pdetails`.pdetail_joint_id) AS joint_count
			FROM 
				`pdetail_joints`
				INNER JOIN `pdetails`
					ON `pdetail_joints`.pdetail_joint_id = `pdetails`.`pdetail_joint_id`
			WHERE 
				`pdetails`.`pdetail_package` = ' . intval($package_id) . '
				AND `pdetail_joints`.pdetail_foto_request = 1
				AND `pdetails`.pdetail_status <> "deleted"
		')->result();
		
		if (count($result == 1) &&  $result)
		{
			$result = $result[0];
			return $result->joint_count;
		}
		
		return 0;
	}

	public function getManagerJoint($pdetail_joint_id, $manager_id)
	{
		$result = $this->db->query('
			SELECT `pdetail_joints`.package_id
			FROM 
				`pdetail_joints`
				INNER JOIN `pdetails`
					ON `pdetail_joints`.pdetail_joint_id = `pdetails`.`pdetail_joint_id`
			WHERE 
				`pdetail_joints`.`pdetail_joint_id` = ' . intval($pdetail_joint_id) . '
				AND `pdetails`.pdetail_manager = ' . intval($manager_id) . '
				AND `pdetails`.pdetail_status <> "deleted"
		')->result();
		
		if (count($result == 1) &&  $result)
		{
			return $result[0];
		}
		
		return FALSE;
	}
	
	public function getClientJoint($pdetail_joint_id, $package_id, $client_id)
	{
		$result = $this->db->query('
			SELECT `pdetail_joints`.package_id
			FROM 
				`pdetail_joints`
				INNER JOIN `pdetails`
					ON `pdetail_joints`.pdetail_joint_id = `pdetails`.`pdetail_joint_id`
			WHERE 
				`pdetail_joints`.`pdetail_joint_id` = ' . intval($pdetail_joint_id) . '
				AND `pdetails`.pdetail_package = ' . intval($package_id) . '
				AND `pdetails`.pdetail_client = ' . intval($client_id) . '
				AND `pdetails`.pdetail_status <> "deleted"
		')->result();
		
		if (count($result == 1) &&  $result)
		{
			return $result[0];
		}
		
		return FALSE;
	}
	
	public function getJointsFoto(array $pdetails)
	{
		$jointFotos	= array();
		
		foreach ($pdetails as $pdetail)
		{
			if (empty($pdetail->pdetail_joint_id) OR
				! empty($jointFotos[$pdetail->pdetail_joint_id]))
			{
				continue;
			}
			
			$scandir = UPLOAD_DIR."packages/{$pdetail->pdetail_package}/joint_{$pdetail->pdetail_joint_id}/";
			
			if (is_dir($scandir))
			{
				foreach (scandir($scandir) as $scanFile)
				{
					if ($scanFile != '.' AND
						$scanFile != '..' AND
						(empty($jointFotos[$pdetail->pdetail_joint_id]) OR
						! in_array($scanFile, $jointFotos[$pdetail->pdetail_joint_id])))
					{
						$jointFotos[$pdetail->pdetail_joint_id][] = $scanFile;
					}
				}
			}
		}
		
		return $jointFotos;
	}
}
?>