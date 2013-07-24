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
class AddressModel extends BaseModel implements IModel{

	protected 	$properties			= null;				// array of properties
	protected	$table				= 'addresses';		// table name
	protected	$PK					= 'address_id';	// primary key name

	/**
	 * конструктор
	 *
	 */
	function __construct()
    {
    	$this->properties = new stdClass();
    	$this->properties->address_id = '';
    	$this->properties->address_desc = '';
    	$this->properties->address_user = '';
        $this->properties->address_recipient = '';
        $this->properties->address_country = '';
    	$this->properties->address_town = '';
    	$this->properties->address_zip = '';
        $this->properties->address_address = '';
        $this->properties->address_phone = '';
        $this->properties->address_is_default = '';
        $this->properties->is_generated = '';

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

	public function addAddress($com_obj){

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

    public function updateAddress($address_obj) {
        $props = $this->getPropertyList();

        foreach ($props as $prop){
            if (isset($address_obj->$prop)){
                $this->_set($prop, $address_obj->$prop);
            }
        }

        $new_id = $this->save();

        if (!$new_id) return false;
        if ($new_id === true){
            $new_id = $this->properties->address_id;
        }
        return $this->getInfo(array($new_id));
    }

	public function deleteAddress($address_id)
	{
        return $this->db->delete($this->table, array('address_id' => $address_id));
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

    public function getAddressesByUserId($user_id)
    {
        $id = intval($user_id);

        $result = $this->db->query("
			SELECT `addresses`.*, `countries`.`country_name`, `countries`.`country_name_en`
			FROM `addresses`, `countries`
			WHERE
				`addresses`.`address_user` = '$user_id' AND
				`addresses`.`address_country` = `countries`.`country_id`
			ORDER BY
				address_id ASC")->result();

        return (isset($result)) ? $result : FALSE;
    }

    public function getAddressById($id)
    {
        $id = intval($id);

        $result = $this->db->query("
			SELECT `addresses`.*, `countries`.`country_name`, `countries`.`country_name_en`
			FROM `addresses`, `countries`
			WHERE
				`addresses`.`address_id` = '$id' AND
				`addresses`.`address_country` = `countries`.`country_id`
			ORDER BY
				address_id ASC")->result();

        return (isset($result)) ? $result : FALSE;
    }
}
?>