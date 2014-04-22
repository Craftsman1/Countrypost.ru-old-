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
class MoneysendModel extends BaseModel implements IModel{

    protected 	$properties			= null;			// array of properties
    protected	$table				= 'money_transfers';		// table name
    protected	$PK					= 'id';		// primary key name

    /**
     * конструктор
     *
     */
    function __construct()
    {
        $this->properties	= new stdClass();
        $this->properties->id		='';
        $this->properties->name		='';
        $this->properties->percent  ='';
        $this->properties->type		='';
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

    public function getBySectionId($section_id)
    {
        $result = $this->db->query("
			SELECT `faq`.*
			FROM `faq`
			WHERE faq_section_id = " . intval($section_id) . "
			ORDER BY faq_id")
            ->result();

        return $result ? $result : FALSE;
    }
}
?>