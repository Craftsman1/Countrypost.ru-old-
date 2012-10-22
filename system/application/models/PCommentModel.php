<?
require_once(MODELS_PATH.'Base/BaseModel.php');
/**
 * @author tua
 * 
 * модель для комментариев к посылке
 * 1. в модели не делаем проверок на валидность i\o это должно делаться в контролере
 * 2. допустимы только ошибки уровня БД
 * 3. разрешатся передавать списки параметров функции, только в случает отсутствия публичного 
 * атрибута соответствующего объекта
 *
 */
class PCommentModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'pcomments';		// table name
	protected	$PK					= 'pcomment_id';	// primary key name	
	
	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties	= new stdClass();
    	$this->properties->pcomment_id				='';
    	$this->properties->pcomment_user			='';
    	$this->properties->pcomment_package			='';
    	$this->properties->pcomment_comment			='';
    	$this->properties->pcomment_time			='';
    	$this->properties->package_manager_login	='';
    	$this->properties->pcomment_admin_notification_sent	='';
		
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

	
	public function delComment($comment_id){
		
		$this->_set($this->getPK(), $comment_id);
		
		return $this->delete();
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
	 * Get package's comments
	 *
	 * @return array
	 */
	public function getCommentsByPackageId($id){
		$result = $this->db->query('
			SELECT `pcomments`.*, `users`.`user_login`  as `package_manager_login`
			FROM `pcomments`
			INNER JOIN `packages` on `pcomments`.`pcomment_package` = `packages`.`package_id`
			INNER JOIN `users` on `users`.`user_id` = `packages`.`package_manager`
			WHERE `pcomments`.`pcomment_package` = '.intval($id).'
			ORDER BY `pcomments`.`pcomment_id`
		')->result();

		return (isset($result)) ? $result : FALSE;
	}

	public function getUnansweredComments($hours = 15)
	{
		$result = $this->db->query(
			"SELECT 
				`pcomments`.`pcomment_id`, 
				`pcomments`.`pcomment_user`, 
				`pcomments`.`pcomment_package`
			FROM
				(SELECT MAX(`pcomments`.`pcomment_id`) AS 'pcomment_id'
				FROM `pcomments` 
				GROUP BY `pcomments`.`pcomment_package`) last_comments
			INNER JOIN
				`pcomments` ON `pcomments`.`pcomment_id` = last_comments.`pcomment_id`
			INNER JOIN  
				`clients` ON `pcomments`.`pcomment_user` = `clients`.`client_user`
			WHERE
				`pcomments`.`pcomment_admin_notification_sent` = 0 AND 
				HOUR(TIMEDIFF(NOW(), `pcomments`.`pcomment_time`)) > $hours
			ORDER BY
				`pcomments`.`pcomment_package`"
			)->result();

		return (isset($result) AND count($result)) ? $result : FALSE;
	}
}
?>