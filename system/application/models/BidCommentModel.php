<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author tua
 * 
 * модель для комментариев к заказу
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class BidCommentModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'bid_comments';		// table name
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
    	$this->properties->bid_id = '';
    	$this->properties->message = '';
    	$this->properties->created = '';
    	$this->properties->status = '';
        $this->properties->new = '';
    	
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
			SELECT `bid_comments`.*
			FROM `bid_comments`
				INNER JOIN `bids` on `bids`.`bid_id` = `bid_comments`.`bid_id`
				INNER JOIN `orders` on `orders`.`order_id` = `bids`.`order_id`
			WHERE 
				`orders`.`order_id` = '$id'
			ORDER BY `bid_comments`.`comment_id`")->result();

		return (isset($result)) ? $result : FALSE;
	}

    public function getNewCommentsByOrderId($order_id)
    {
        $id = intval($order_id);
        $result = $this->db->query("
            SELECT comment_id
            FROM bid_comments
            WHERE new = 0 AND bid_id = $id
        ")->result();

        return (isset($result)) ? $result : FALSE;
    }

	public function getCommentsByBidId($bid_id)
	{
		$id = intval($bid_id);
		
		$result = $this->db->query("
			SELECT `bid_comments`.*
			FROM `bid_comments`
			WHERE 
				`bid_comments`.`bid_id` = '$id' AND 
				status <> 'deleted'
			ORDER BY 
				`bid_comments`.`comment_id` ASC")->result();

		return (isset($result)) ? $result : FALSE;
	}

    public function clearNewComments($bid_id)
    {
        $id = intval($bid_id);

        $result = $this->db->query("
            UPDATE `bid_comments`
            SET new = 1
            WHERE bid_id = '$id'")->result();

        return (isset($result)) ? $result : FALSE;

    }
}
?>