<? require_once(MODELS_PATH.'Base/BaseModel.php');

class RatingCommentModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'rating_comments';		// table name
	protected	$PK					= 'comment_id';	// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties = new stdClass();
    	$this->properties->comment_id = '';
    	$this->properties->user_id = '';
    	$this->properties->rating_id = '';
    	$this->properties->message = '';
    	$this->properties->created = '';
    	$this->properties->status = '';
    	
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
	
	public function addComment($com_obj){
		
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
	
	
	public function deleteComment($comment_id)
	{
		
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
	
	/**
	 * Get order's comments
	 *
	 * @return array
	 */
	public function getCommentsByOrderId($order_id)
	{
		$id = intval($order_id);
		
		$result = $this->db->query("
			SELECT `rating_comments`.*
			FROM `rating_comments`
				INNER JOIN `ratings` on `ratings`.`rating_id` = `rating_comments`.`rating_id`
				INNER JOIN `orders` on `orders`.`order_id` = `ratings`.`order_id`
			WHERE 
				`orders`.`order_id` = '$id'
			ORDER BY `rating_comments`.`comment_id`")->result();

		return (isset($result)) ? $result : FALSE;
	}

	public function getCommentsByRatingId($rating_id)
	{
		$id = intval($rating_id);
		
		$result = $this->db->query("
			SELECT `rating_comments`.*
			FROM `rating_comments`
			WHERE 
				`rating_comments`.`rating_id` = '$id' AND
				status <> 'deleted'
			ORDER BY 
				`rating_comments`.`created` ASC")->result();

		return (isset($result)) ? $result : FALSE;
	}

    public function delRating($id_rating)
    {

        $id = intval($id_rating);

        $this->db->query("

			DELETE FROM rating_comments
			WHERE rating_id = '$id';

			");

        $this->db->query("

			DELETE FROM manager_ratings
			WHERE rating_id = '$id'

			");

    }

    public function delCommentRating($id_message)
    {
        $id = intval($id_message);

        $this->db->query("

			DELETE rating_comments
			FROM rating_comments
			WHERE rating_comments.comment_id = '$id'

			");

    }
}
?>