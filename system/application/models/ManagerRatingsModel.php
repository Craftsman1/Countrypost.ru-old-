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
		
		if ($new_id)
		{
			$this->updateManagerRating($com_obj->manager_id);
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
			ORDER BY
				rating_id DESC
		")->result();
		return ($result) ? $result : FALSE;
	}

	public function updateManagerRating($manager_id)
	{
		// считаем отзывы
		$result = $this->db->query("
			SELECT
				COUNT(*) positive_reviews
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `rating_type` = '1'
			GROUP BY `manager_id`
		")->result();

		$positive_reviews = ($result) ? $result[0]->positive_reviews : 0;

		$result = $this->db->query("
			SELECT
				COUNT(*) neutral_reviews
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `rating_type` = '0'
			GROUP BY `manager_id`
		")->result();

		$neutral_reviews = ($result) ? $result[0]->neutral_reviews : 0;

		$result = $this->db->query("
			SELECT
				COUNT(*) negative_reviews
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `rating_type` = '-1'
			GROUP BY `manager_id`
		")->result();

		$negative_reviews = ($result) ? $result[0]->negative_reviews : 0;

		$this->db->query("
			UPDATE
				users
			SET
				`positive_reviews` = $positive_reviews,
				`neutral_reviews` = $neutral_reviews,
				`negative_reviews` = $negative_reviews
			WHERE
				`user_id` = $manager_id
		");

		// считаем средние рейтинги
		$result = $this->db->query("
			SELECT
				AVG(`buy_rating` - 1) buy_rating,
				COUNT(*) count
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `buy_rating` IS NOT NULL
			GROUP BY `manager_id`
		")->result();

		$buy_rating = $result ? ($result[0]->buy_rating * 0.25) : 'NULL';
		$buy_rating_count = $result ? $result[0]->count : 0;

		$result = $this->db->query("
			SELECT
				AVG(`communication_rating` - 1) communication_rating,
				COUNT(*) count
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `communication_rating` IS NOT NULL
			GROUP BY `manager_id`
		")->result();

		$communication_rating = $result ? ($result[0]->communication_rating * 0.25) : 'NULL';
		$communication_rating_count = $result ? $result[0]->count : 0;

		$result = $this->db->query("
			SELECT
				AVG(`consolidation_rating` - 1) consolidation_rating,
				COUNT(*) count
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `consolidation_rating` IS NOT NULL
			GROUP BY `manager_id`
		")->result();

		$consolidation_rating = $result ? ($result[0]->consolidation_rating * 0.25) : 'NULL';
		$consolidation_rating_count = $result ? $result[0]->count : 0;

		$result = $this->db->query("
			SELECT
				AVG(`pack_rating` - 1) pack_rating,
				COUNT(*) count
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
				AND `pack_rating` IS NOT NULL
			GROUP BY `manager_id`
		")->result();

		$pack_rating = $result ? ($result[0]->pack_rating * 0.25) : 'NULL';
		$pack_rating_count = $result ? $result[0]->count : 0;

		// суммарный рейтинг
		// нет звезд - 0
		// 1 звезда - -2
		// 2 звезда - -1
		// 3 звезда - 0
		// 4 звезда - 1
		// 5 звезда - 2

		$result = $this->db->query("
			SELECT
				SUM(
					IFNULL(pack_rating + 0, 3) +
					IFNULL(buy_rating + 0, 3) +
					IFNULL(consolidation_rating + 0, 3) +
					IFNULL(communication_rating + 0, 3) -
					12
				) rating
			FROM
				`manager_ratings`
			WHERE
				`status` <> 'deleted'
				AND `manager_id` = $manager_id
			GROUP BY `manager_id`
		")->result();

		$rating = $result ? $result[0]->rating : 0;

		// средние значения вычеслены по индексам ENUM, которые в 4 раза превышают строковые значения,
		// поэтому предварительно делим их на 4
		$this->db->query("
			UPDATE
				managers
			SET
				`buy_rating` = $buy_rating,
				`pack_rating` = $pack_rating,
				`consolidation_rating` = $consolidation_rating,
				`communication_rating` = $communication_rating,
				`buy_rating_count` = $buy_rating_count,
				`pack_rating_count` = $pack_rating_count,
				`consolidation_rating_count` = $consolidation_rating_count,
				`communication_rating_count` = $communication_rating_count,
				`rating` = $rating
			WHERE
				`manager_user` = $manager_id
		");
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