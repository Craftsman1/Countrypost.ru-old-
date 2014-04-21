<? require_once(MODELS_PATH.'Base/BaseModel.php');

class OdetailJointModel extends BaseModel implements IModel{

	protected  $properties = null;				// array of properties
	protected  $table = 'odetail_joints';			// table name
	protected  $PK = 'joint_id';		// primary key name

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->joint_id = '';
    	$this->properties->cost = '';
    	$this->properties->count = '';
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
	
	public function addOdetailJoint($odetail_joint_obj) {
		$props = $this->getPropertyList();
		
		foreach ($props as $prop){
			if (isset($odetail_joint_obj->$prop)){
				$this->_set($prop, $odetail_joint_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id)
		{
			return $this->getInfo(array($new_id));
		}
		
		return false;
	}
	
	public function generateJoint()
	{
		$this->_set('cost', 0);
		$this->_set('count', 0);

		$new_id = $this->save(true);
		
		if ($new_id)
		{
			return $this->getInfo(array($new_id));
		}
		
		return false;
	}
	
	public function getById($id)
	{
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : false);
	}

	public function getOrderJoints($order_id)
	{
		$joints = $this->db->query("
			SELECT
				`odetail_joints`.*
			FROM
				`odetail_joints`
			INNER JOIN
				`odetails` ON `odetails`.`odetail_joint_id` = `odetail_joints`.`joint_id`
			INNER JOIN
				`orders` ON `odetails`.`odetail_order` = `orders`.`order_id`
			WHERE
				`orders`.`order_id` = '$order_id'
				AND `orders`.`order_status` <> 'deleted'
				AND `odetails`.`odetail_status` <> 'deleted'
		")->result();

		$result = array();

		foreach ($joints as $joint)
		{
			$result[$joint->joint_id] = $joint;
		}

		return $result;
	}

	public function getPrivilegedJoint($order_id, $joint_id, $user_id, $user_group)
	{
		if ($user_group == 'client')
		{
			return $this->getClientJointById($order_id, $joint_id, $user_id);
		}
		else if ($user_group == 'manager')
		{
			return $this->getManagerJointById($order_id, $joint_id, $user_id);
		}

		return FALSE;
	}

	public function getClientJointById($order_id, $joint_id, $client_id)
	{
		$row = $this->db->query("
			SELECT
				`odetail_joints`.*
			FROM
				`odetail_joints`
			INNER JOIN
				`odetails` ON `odetails`.`odetail_joint_id` = `odetail_joints`.`joint_id`
			INNER JOIN
				`orders` ON `odetails`.`odetail_order` = `orders`.`order_id`
			WHERE
				`odetail_joints`.`joint_id` = '$joint_id'
				AND `orders`.`order_id` = '$order_id'
				AND `orders`.`order_client` = '$client_id'
				AND `orders`.`order_status` <> 'deleted'
				AND `odetails`.`odetail_status` <> 'deleted'
			LIMIT 1
		")->result();

		if (empty($row) OR count($row) != 1)
		{
			return FALSE;
		}

		return $row[0];
	}

	public function getManagerJointById($order_id, $joint_id, $manager_id)
	{
		$row = $this->db->query("
			SELECT
				`odetail_joints`.*
			FROM
				`odetail_joints`
			INNER JOIN
				`odetails` ON `odetails`.`odetail_joint_id` = `odetail_joints`.`joint_id`
			INNER JOIN
				`orders` ON `odetails`.`odetail_order` = `orders`.`order_id`
			WHERE
				`odetail_joints`.`joint_id` = '$joint_id'
				AND `orders`.`order_id` = '$order_id'
				AND `orders`.`order_manager` = '$manager_id'
				AND `orders`.`order_status` <> 'deleted'
				AND `odetails`.`odetail_status` <> 'deleted'
			LIMIT 1
		")->result();

		if (empty($row) OR count($row) != 1)
		{
			return FALSE;
		}

		return $row[0];
	}


}
?>