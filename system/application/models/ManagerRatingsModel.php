<?
require_once(MODELS_PATH.'Base/BaseModel.php');

class ManagerRatingsModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'manager_ratings';		// table name
	protected	$PK					= 'rating_id';	// primary key name
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties = new stdClass();
    	$this->properties->rating_id = '';
    	$this->properties->manager_id = '';
    	$this->properties->client_id = '';
    	$this->properties->rating_type = '';
    	$this->properties->communication_rating = '';
    	$this->properties->buy_rating = '';
    	$this->properties->consolidation_rating = '';
    	$this->properties->pack_rating = '';
    	$this->properties->status = '';
    	$this->properties->created = '';

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
	
	public function addRating($com_obj){
		
		$props = $this->getPropertyList();
				
		foreach ($props as $prop){
			if (isset($com_obj->$prop)){
				$this->_set($prop, $com_obj->$prop);
			}
		}
		
		$new_id = $this->save(true);
		
		if ($new_id){
			return $this->getInfo(array($new_id));
		}
		
		return FALSE;
	}

	
	/**
	 * Get comment by id
	 *
	 * @return array
	 */
	public function getById($id){
		$r = $this->select(array(
			$this->getPK()	=> (int) $id,
		));					
		
		return ((count($r==1) &&  $r) ? array_shift($r) : FALSE);
	}

	public function getRatings($manager_id)
	{
		$result = $this->db->query("
			SELECT *
			FROM `{$this->table}`
			WHERE `status` != 'deleted'
				AND `manager_id` = $manager_id
		")->result();
		return ($result) ? $result : FALSE;
	}


	public function getStatistics($rating_id)
	{
		$statistics = $this->getById($rating_id);

		// manager
		$result = $this->db->query(
			"SELECT
				user_login,
				positive_rating,
				neutral_rating,
				negative_rating
			FROM
				`users`
			WHERE
				`users`.`user_id` = {$statistics->manager_id}"
		)->result();

		$user = ($result) ? $result[0] : FALSE;

		if ($user)
		{
			$statistics->manager_fullname = $user->login;
		}

		$statistics = $this->getById($rating_id);

		// client
		$result = $this->db->query(
			"SELECT
				user_login,
				positive_rating,
				neutral_rating,
				negative_rating
			FROM
				`users`
			WHERE
				`users`.`user_id` = {$statistics->client_id}"
		)->result();

		$user = ($result) ? $result[0] : FALSE;

		if ($user)
		{
			$statistics->client_fullname = $user->user_login;
		}

		return $statistics;
	}
}
?>