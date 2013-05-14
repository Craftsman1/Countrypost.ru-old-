<?php
require_once BASE_CONTROLLERS_PATH.'BaseController'.EXT;

class Main extends BaseController {
    var $data = null;
    var $root = '/static/images/';

	function __construct()
	{
		parent::__construct();

		$this->paging_base_url = '/main/showUnassignedOrders';	 
		View::$main_view	= '/main/index';
		Breadcrumb::setCrumb(array('/' => 'Главная'), 0);
	}

	function index()
	{
		try
		{
		    $this->load->model('OrderModel', 'Orders');
						
			// обработка фильтра
			$view['filter'] = $this->initFilter('UnassignedOrders');
			$view['filter']->order_types = $this->Orders->getOrderTypes();
			$view['filter']->requests_count = TRUE;
				
			$client_id = empty($this->user) ? 0 : $this->user->user_id;
			$view['orders'] = $this->
				Orders->
				getUnassignedOrders(
					$view['filter'], 
					$client_id);
			
			if ( ! $view['orders'])
			{
				$this->result->m = 'Заказы не найдены.';
				Stack::push('result', $this->result);
			}
			
			/* пейджинг */
			$this->init_paging();
			$this->paging_count = count($view['orders']);
			
			// счетчики
			$view['orders_count'] = empty($view['orders']) ? '0' : $this->paging_count;

			if ($view['orders'])
			{
				$view['orders'] = array_slice($view['orders'], $this->paging_offset, $this->per_page);
			}
			
			// Принудительно проставляем ссылку для паджинатора 
			$this->paging_base_url = "/main/showUnassignedOrders";
			$view['pager'] = $this->get_paging();
			
			// страны для фильтра
			$this->load->model('CountryModel', 'Country');
			$view['countries'] = $this->Country->getList();
			
			if (empty($view['countries']))
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}

			$view['order_types'] = $this->Orders->getOrderTypes();
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		View::showChild($this->viewpath.'/pages/main', array(
			'orders' => $view['orders'],
			'filter' => $view['filter'],
			'orders_count' => $view['orders_count'],
			'countries' => $view['countries'],
			'pager' => $view['pager'],
			'order_types' => $view['order_types'],
			'showSocialBox' => TRUE,
			'showBannerBox' => TRUE,
		));
	}
	
	function showUnassignedOrders()
	{
		try
		{
		    $this->load->model('OrderModel', 'Orders');
		    
			// обработка фильтра
			$view['filter'] = $this->initFilter('UnassignedOrders');
			$view['filter']->order_types = $this->Orders->getOrderTypes();
				
			$client_id = empty($this->user) ? 0 : $this->user->user_id;
			$view['orders'] = $this->
				Orders->
				getUnassignedOrders(
					$view['filter'], 
					$client_id);
			
			if ( ! $view['orders'])
			{
				$this->result->m = 'Заказы не найдены.';
				Stack::push('result', $this->result);
			}
			
			/* пейджинг */
			$this->init_paging();		
			$this->paging_count = count($view['orders']);
			$view['orders_count'] = empty($view['orders']) ? '0' : $this->paging_count;

			if ($view['orders'])
			{
				$view['orders'] = array_slice($view['orders'], $this->paging_offset, $this->per_page);
			}
			
						
			// Принудительно проставляем ссылку для паджинатора 
			$this->paging_base_url = "/main/showUnassignedOrders";
			$view['pager'] = $this->get_paging();
			
			// страны для фильтра
			$this->load->model('CountryModel', 'Country');
			$view['countries'] = $this->Country->getList();
			
			if (empty($view['countries']))
			{
				throw new Exception('Страны не найдены. Попробуйте еще раз.');
			}

			$view['order_types'] = $this->Orders->getOrderTypes();
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			
			Stack::push('result', $this->result);
		}
		
		$view['selfurl'] = BASEURL.$this->cname.'/';
		$view['viewpath'] = $this->viewpath;
		$this->load->view($this->viewpath."ajax/showUnassignedOrders", $view);
	}
	
	function refreshExchangeRates()
	{
		$this->load->library('OpenExchangeRates');

		if ($this->openexchangerates->getRates())
		{
			print("Курсы валют успешно обновлены.");			
		}
		else 
		{
			print("Ошибка обновления курсов валют через OpenExchangeRates. Попробуйте еще раз через несколько минут.");
		}

		/*
		$this->load->library('cbr');
		$this->load->model('CurrencyModel', 'Currencies');
		$this->load->model('PricelistModel', 'Pricelist');

		if ($this->cbr->getRates($this->Currencies))
		{
			print("Курсы валют успешно обновлены.");
			$this->Pricelist->convertAll();
			print("\nТарифы на доставку успешно обновлены.");
		}
		else
		{
			print("Ошибка обновления курсов валют. Попробуйте еще раз через несколько минут.");
		}*/
	}
	
	public function showPays()
	{
		
		View::showChild($this->viewpath.'/pages/pays');
	}
	
	public function showShopCatalog()
	{
		// получаем категории магазинов с числом магазинов в них		
		$this->load->model('CategoryModel', 'Category');
		$this->load->model('ShopModel', 'Shops');
		$view = array();		
		$view['Categories'] = $this->Category->getCategoriesWithShopsNum();
		$allShops = new stdClass();
		$allShops->scategory_id = '';
		$allShops->scategory_name = 'Все магазины';
		$allShops->scategory_details = 'Полный Список Магазинов';
		$allShops->count = $this->Shops->getCount();
		$view['Categories'][] = $allShops;
		$view['is_authorized'] = $this->user ? true : false;
		
		if (Stack::size('add_shop') > 0) {
			$view['is_added'] = 1;
			Stack::shift('add_shop');
		}

		View::showChild($this->viewpath.'pages/shop_catalog', $view);
	}
	
	public function showAddShop()
	{
		if (!$this->user) {
			header('Location: '.BASEURL.'main/showShopCatalog');			
			return true;
		}
		$view = array();
		
		$this->load->model('CountryModel', 'Country');		
		$view['countries'] = $this->Country->getList();
		Stack::push('countries', $view['countries']);
		
		$this->load->model('CategoryModel', 'Category');
		$view['categories'] = $this->Category->getList();
		Stack::push('categories', $view['categories']);
		
		View::showChild($this->viewpath.'/pages/add_shop', $view);
	}
	
	public function addShop()
	{
		
		if (!$this->user) {
			header('Location: '.BASEURL.'main/showShopCatalog');			
			return true;
		}
		
		$countries = '';
		if (Stack::size('countries')>0){
			$countries	= Stack::last('countries');
		}else{
			$this->load->model('CountryModel', 'Country');
			$countries	= $this->Country->getList();			
		}
		
		$categories = '';
		if (Stack::size('categories')>0){
			$categories	= Stack::last('categories');
		}else{
			$this->load->model('CategoryModel', 'Category');
			$categories	= $this->Category->getList();			
		}
		
		Check::reset_empties();
		$shop					= new stdClass();
		$shop->shop_name		= Check::str('sname', 100, 7);
		$shop->shop_desc		= Check::str('sdescription',1000,0);
		$shop->shop_country		= Check::int('scountry');
		$shop->shop_scategory	= Check::int('scategory');
		$shop->shop_user		= $this->user->user_id;
		$empties				= Check::get_empties();
		
		$result		= new stdClass();
		$result->e	= 0;
		$result->m	= '';	// сообщение
		$result->d	= '';	// возвращаемые данные
		
		try {
			if ($empties){
				throw new Exception('Одно или несколько полей не заполнено.', -10);
			}
			
			if (!Check::url($shop->shop_name))
				throw new Exception('Адрес сайта некорректен.', -7);
			
			$counties_ids = array();
			foreach ($countries as $country)
				$counties_ids[] = $country->country_id;
			if (!in_array($shop->shop_country, $counties_ids))
				throw new Exception('Страна не выбрана.', -5);
				
			$categories_ids = array();
			foreach ($categories as $category)
				$categories_ids[] = $category->scategory_id;
			if (!in_array($shop->shop_scategory, $categories_ids))
				throw new Exception('Категория не выбрана.', -6);
			
			$this->load->model('ShopModel', 'Shop');
			
			$s = $this->Shop->addShop($shop);
			if (!$s) {
				throw new Exception('Добавление магазина временно невозможно.',-12);
			}
			
			Stack::push('add_shop', 1);
			Func::redirect(BASEURL.'main/showShopCatalog');			
			return true;
		} catch (Exception $e){
			
			$result->e	= $e->getCode();			
			$result->m	= $e->getMessage();

			switch ($result->e){
				case -1:
				case -7:
					$shop->shop_name = '';
					break;
				case -5:
					$shop->shop_country = 0;
					break;
				case -6:
					$shop->shop_scategory = 0;
					break;
			}
			
			$result->d	= $shop;
		}
		
		$view = array(
			'result'		=> $result,
			'categories'	=> $categories,
			'countries'		=> $countries
		);
		
		View::showChild($this->viewpath.'pages/add_shop', $view);
	}
	
	public function saveShop($id)
	{
		//проверка прав на доступ администратора
		try
		{
			$check = ($this->user ? ($this->user->user_group == 'admin') : false);
			if ( ! $check ) 
			{
				throw new Exception('Для удаления нужны права администратора');
				return ;
			}
			
			
			Check::reset_empties();

			$shop_name	    = Check::txt('shop_name',	128,1);
			$shop_country	= Check::int('country');
			$shop_scategory	= Check::int('scategory');
			$shop_desc	    = Check::txt('shop_desc',	8096);
			
			// fild all fields
			if (!Check::get_empties())
			{
				$this->load->model('ShopModel', 'ShopModel');
				
				$this->ShopModel->_set('shop_name',      $shop_name);
				$this->ShopModel->_set('shop_country',   $shop_country);
				$this->ShopModel->_set('shop_scategory', $shop_scategory);
				$this->ShopModel->_set('shop_desc',      $shop_desc);
				$this->ShopModel->_set('shop_user',      $this->user->user_id);
				
				if ($id) 
					$this->ShopModel->_set('shop_id',	$id);			
				
				if (!$this->ShopModel->save()){
					$this->result->e	= -1;
					$this->result->m	= 'Невозожно добавить запись.';
				}else{
					$this->result->e	= 1;
					$this->result->m	= 'Запись успешно добавлна.';
				}
			}
			else
			{
				$this->result->e	= -1;
				$this->result->m	= 'Невозожно добавить запись. Возможно незаполнено одно или несколько полей.';
			}
		}
		catch(Exception $e)
		{
			$this->result->e	= $e->getCode();			
			$this->result->m	= $e->getMessage();
		}
		Stack::push('result', $this->result);
		
		Func::redirect(BASEURL.'main/showCategory/'.$shop_scategory);
		
		
		
	}
	
	public function showEditShop($id=null)
	{
		//проверка прав на доступ администратора
		$check = ($this->user ? ($this->user->user_group == 'admin') : false);
		if ( ! $check ) 
		{
			throw new Exception('Для удаления нужны права администратора');
			return ;
		}
		//загрузка модели
		$this->load->model('ShopModel', 'ShopModel');		
		if ( ! ($Shop = $this->ShopModel->select(array('shop_id' => intval($id))) ) ) 
		{
			header('Location: '.BASEURL.'main/showShopCatalog');			
			return true;
		}
		
		$this->load->model('CountryModel', 'Country');
		$Countries	= $this->Country->getList();
		
		$this->load->model('CategoryModel', 'SCategory');
		$SCategory	= $this->SCategory->getList();

		$view = array(
			'scategories'  => $SCategory,
			'shop'         => $Shop[0],
			'countries'    => $Countries
		);

		View::showChild($this->viewpath.'pages/edit_shop', $view);
		
	}
	
	public function showCategory($id = 0, $order = 0) 
	{				
		$this->load->model('CategoryModel', 'CategoryModel');
		if (is_numeric($id) &&
			$id &&
			!($Category = $this->CategoryModel->select(array('scategory_id' => intval($id)))))
		{
			header('Location: '.BASEURL.'main/showShopCatalog');			
			return true;
		}
		
		if (!$id)
		{
			$allShops = new stdClass();
			$allShops->scategory_id = '0';
			$allShops->scategory_name = 'Все магазины';
			$allShops->scategory_details = 'Полный Список Магазинов';
			$Category = array();
			$Category[0] = $allShops;
		}
		
		$this->load->model('ShopModel', 'ShopModel');
		
		$avail_orders = array('id', 'country', 'comments', 'name');
		$orders_by = array('id' => 'shop_id', 'country' => 'country_name', 'comments' => 'count', 'name' => 'shop_name');
		$orders_addon = array('id' => null, 'country' => 'INNER JOIN `countries` ON `countries`.`country_id` = `'.$this->ShopModel->getTable().'`.`shop_country`', 'comments' => null, 'name' => null);
		$orders_order = array('id' => 'ASC', 'country' => 'ASC', 'comments' => 'DESC', 'name' => 'ASC');
		$order = (in_array($order, $avail_orders)) ? $order : $avail_orders[0];
		
		if (!$order) $order = 'id';
		
		$Shops = $this->ShopModel->getShopsByCategory($Category[0]->scategory_id, array('by' => $orders_by[$order], 'addon' => $orders_addon[$order], 'order' => $orders_order[$order]));

		/* пейджинг */
		$this->init_paging();		
		$this->paging_count = count($Shops);
		
		if ($Shops)
		{
			$Shops = array_slice($Shops, $this->paging_offset, $this->per_page);
		}
		
		$this->load->model('CountryModel', 'Country');
		$Countries	= $this->Country->getList();
		$countries = array();
		foreach ($Countries as $Country)
			$countries[$Country->country_id] = $Country->country_name;	
		
		$view = array(
			'category'		=> $Category[0],
			'shops'			=> $Shops,
			'is_authorized' => $this->user ? true : false,
			'countries'		=> $countries,
			'pager' 		=> $this->get_paging(),
			'is_admin'   => $this->user ? ($this->user->user_group == 'admin') : false
		);
		
		// парсим шаблон
		if ($this->uri->segment(6) == 'ajax')
		{
        	$view['selfurl'] = BASEURL.$this->cname.'/';
			$view['viewpath'] = $this->viewpath;
			$this->load->view($this->viewpath."ajax/showCategory", $view);
		}
		else
		{
			View::showChild($this->viewpath.'pages/showCategory', $view);
		}
	}
	
	public function deleteShop($id)
	{
		try
		{
			if (isset($id) &&
				is_numeric($id))
			{
				$this->load->model('ShopModel', 'Shops');
				$result = $this->Shops->delete($id);
				
				if (!$result)
				{
					throw new Exception('Не удалось удалить магазин. Попробуйте еще раз.');
				}

				$this->result->e = 1;
				$this->result->m = 'Магазин успешно удален.';
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		Stack::push('shop_deleted', $this->result);
		Func::redirect(BASEURL.'main/showShopCatalog');
	}
	
	public function deleteSComment($id, $shop_id)
	{
		try
		{
			if (isset($id) &&
				is_numeric($id))
			{
				$this->load->model('SCommentModel', 'Comments');
				$result = $this->Comments->delete($id);
				
				if (!$result)
				{
					throw new Exception('Не удалось удалить комментарий. Попробуйте еще раз.');
				}

		//		$this->result->e = 1;
			//	$this->result->m = 'Комментарий успешно удален.';
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
		}
		
		//Stack::push('shop_deleted', $this->result);
		Func::redirect(BASEURL."main/showShop/{$shop_id}");
	}
	
	public function showShop($id=null) {
		
		$this->load->model('ShopModel', 'ShopModel');		
		if (!($Shop = $this->ShopModel->select(array('shop_id' => intval($id))))) {
			header('Location: '.BASEURL.'/main/showShopCatalog');			
			return true;
		}
		
		$this->load->model('CountryModel', 'Country');
		$Country	= $this->Country->select(array('country_id' => $Shop[0]->shop_country));
		
		$this->load->model('SCommentModel', 'SCommentModel');		
		
		// Добавляем коммент		
		$comment					= new stdClass();
		$comment->scomment_id		= Check::int('comment_id');
		
		// у админа позволяем изменять текст
		if ($comment->scomment_id  
			&& $this->user
			&& $this->user->user_group == 'admin')
		{
			$comment = $this->SCommentModel->getById($comment->scomment_id);
			$comment->scomment_comment	= Check::str('comment_update', 1000, 1);

			if ($comment->scomment_comment)
			{
				$this->SCommentModel->addComment($comment);				
			}
		}
		else
		{
			unset($comment->scomment_id);
			$comment->scomment_comment	= Check::str('comment', 1000, 1);
			if ($comment->scomment_comment && $this->user) {
				
				$comment->scomment_user	= $this->user->user_id;
				$comment->scomment_shop	= intval($id);
				
				$this->SCommentModel->addComment($comment);				
			}
		}			
		
		######### вытаскиваем данные о коментах, вместе с данными пользователя, который этот комент оставил ###########
		$Comments	= $this->SCommentModel->select(array('scomment_shop' => intval($id)));
		$users		= array();
		
		if ($Comments){
			foreach ($Comments as $comment){
				if (!in_array($comment->scomment_user, $users)){
					array_push($users, $comment->scomment_user);
				}
			}
		}
		
		$SUsers	= null;
		if (!empty($users)){
			$this->load->model('UserModel', 'User');
			
			foreach ($this->User->getInfo($users,null,null,null,null,false) as $user){
				$SUsers[$user->user_id] = $user;
			}
		}
		###############################################################################################################
		
		$view = array(
			'shop'			=> $Shop[0],
			'country'		=> $Country[0]->country_name,
			'comments'		=> $Comments,
			'susers'		=> $SUsers,
		);
		
		View::showChild($this->viewpath.'pages/show_shop', $view);
	}
	
	public function showHowItWork(){
		
		View::showChild($this->viewpath.'/pages/how_it_work');
	}
	
	public function showContacts(){
		
		View::showChild($this->viewpath.'/pages/contacts');
	}
	
	public function showCollaboration(){
		
		View::showChild($this->viewpath.'/pages/collaboration');
	}		
	
	public function showPricelist()
	{
		try
		{
			// обработка фильтра
			$view['filter'] = $this->initFilter('pricelist'); 
			$this->load->model('CountryModel', 'Countries');
			
			$country_from = 
				empty($view['filter']->pricelist_country_from) ? 
				0 :
				(int)$view['filter']->pricelist_country_from;
			
			$view['from_countries'] = $this->Countries->getFromCountries();
			$view['to_countries'] = $this->Countries->getToCountriesFrom($country_from);
	
			// отображаем наши тарифы
			if (empty($view['filter']->pricelist_country_from) OR
				empty($view['filter']->pricelist_country_to))
			{
				if ($view['filter']->our_pricelist)
				{
					$this->load->model('CountryPricelistModel', 'Pricelist');
					$view['our_pricelist'] = $this->Pricelist->getById($view['filter']->our_pricelist);
				}
			}
			// отображаем тарифы
			else
			{
				$this->load->model('PricelistModel', 'Pricelist');
				$view['pricelist'] = $this->Pricelist->getPricelist($view['filter']);
				
				// отображаем описание тарифа
				$this->load->model('PricelistDescriptionModel', 'PricelistDescription');
				$view['pricelist_description'] = $this->PricelistDescription->getDescription(
					$view['filter']->pricelist_country_from,
					$view['filter']->pricelist_country_to);
			}
		}
		catch (Exception $e) 
		{
			$this->result->e = $e->getCode();			
			$this->result->m = $e->getMessage();
			Stack::push('result', $this->result);
		}

		View::showChild($this->viewpath.'/pages/showPricelist', $view);
	}
	
	public function filterPricelist()
	{
		$this->filter('pricelist', 'showPricelist');
	}

	public function filterOurPricelist()
	{
		$this->filter('pricelist', 'showPricelist');
	}

	function getFile($dir, $id)
	{
		try
		{
			$this->data->dir = $dir;
			$this->data->id = $id;
			
			if (isset($this->data->id) &&
				$this->data->id !== '0' && 
				$this->data->id !== 0)
			{
				$this->load->model('FileModel', 'File');
				$data = $this->File->getById((int)$this->data->id);

				if (!$data)
				{
					$this->data->id = '0';
				}
				else
				{
					$this->data = $data;
				}
			}

			$this->data->err = 'NULL';
		}
		catch (Exception $e) 
		{
			$this->data->err = $e->getMessage();
		}

		View::show($this->viewpath.'/pages/upload', array('data' => $this->data));
	}

	function uploadFile($dir, $id)
	{
		try
		{
			// валидация пользовательского ввода
			$this->data->dir = $dir;
			$this->data->id = $id;

			// загрузка файла
			$this->data->upload_path = BASEURL.'static/images/'.$this->data->dir.'/';
			$config['upload_path'] = BASEPATH.'static/images/'.$this->data->dir.'/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '4096';
			$config['max_width']  = '2048';
			$config['max_height']  = '2048';
			
			var_dump($config['upload_path']);
			
			$this->load->library('upload', $config);

			if (!$this->upload->do_upload())
			{
				throw new Exception(strip_tags(trim($this->upload->display_errors())));
			} 
			else
			{
				// сохраняем файл в базе
				$this->data->err = '';
				$this->update($this->upload->data());

				Func::redirect(BASEURL.'main/getFile/'.$this->data->dir.'/'.$this->data->id);
			}
		}
		catch (Exception $e) 
		{
			$this->data->err = $e->getMessage();
			View::show($this->viewpath.'/pages/upload', array('data' => $this->data));
		}
	}

    private function update($f)
    {
		$this->load->model('FileModel', 'File');

		// удаляем старый файл
        if ($this->data->id)
        {
			$data = $this->File->getById($this->data->id);
          
			if (!$data)
			{
				$this->data->id = 0;
			}
			else
			{
				$old_file = str_replace($f['file_name'], '', $f['full_path']).$data->name;
				@unlink($old_file);
			}
        }

		// сохраняем результат
		$this->data->name = $f['file_name'];
        $this->data->fullpath = $this->data->upload_path.$f['file_name'];
        $this->data->ext = $f['file_ext'];
        $this->data->size = $f['file_size'];
        $this->data->width = $f['image_width'];
        $this->data->height = $f['image_height'];

    	$data = $this->File->addFile($this->data);

		if (!$data)
		{
			throw new Exception('Файл не загружен. Попробуйте еще раз.');
		}
		
		$this->data->id = $data->id;
	}
	
	public function showCurrencyCalc()
	{
		$this->load->library('cbr');
		
		$curencies	= $this->cbr->getAllCurrencyInfo(); //не смотря на название, функция получает полную информацию по волютам
		
		$currencies = array();
		foreach ($curencies as $currency)
		{
			$currency->Vcurs = str_replace(",",".",$currency->Vcurs) * CBR::addpercent;
			$currencies[(string) $currency->VchCode]	= $currency;
		}
		
		View::show($this->viewpath.'/pages/showCurrencyCalc',array(
			'currencies'	=> $currencies,
		));
	}
	
	public function showFAQ()
	{
		$this->load->model('FaqModel', 'Faq');
		$this->load->model('FaqSectionModel', 'FaqSections');
			
		$faq_sections = $this->FaqSections->getList();
		
		foreach ($faq_sections as $faq_section)
		{
			$faq_section->questions = $this->Faq->getBySectionId($faq_section->faq_section_id);
		}
		
		View::showChild($this->viewpath.'/pages/faq', 
			array('faq_sections' => $faq_sections));
	}

	/**
	 * Отправка письма админу
	 */
	public function contactUs()
	{
		$user						= new stdClass();
		$user->message				= Check::str('message',1024,0);
		
		if (!$this->user) 
		{
			$user->fio				= Check::str('fio',128,0);
			$user->email			= Check::str('email',128,0);
			$user->phone			= Check::str('phone',20,0);
			$details				= "
ФИО: {$user->fio}
Email: {$user->email}
Телефон: {$user->phone}";
		}
		else
		{
			$details				= "
Пользователь №{$this->user->user_id} ({$this->user->user_login})
Email: {$this->user->user_email}";
		}
		
		try
		{
			$this->load->library('alcaptcha');

			if (!$this->alcaptcha->check($this->input->post('captchacode'))) 
			{
				throw new Exception('Проверочный код введен не верно. Попробуйте еще раз.', -18);
			}
			
			$headers = 'From: info@countrypost.ru' . "\r\n" .
				'Reply-To: info@countrypost.ru' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

			$body = "Сообщение от:
{$details}

{$user->message}

С наилучшими пожеланиями,
Команда Countrypost.ru";

			mail('at3@yandex.ru',//'info@countrypost.ru', 
				"=?cp1251?B?" . base64_encode("Новое сообщение от Countrypost.ru") . "?=",
				$body, 
				$headers);
			
			$this->result->e	= 0;
			$this->result->m	= 'Поздравляем! Ваше сообщение успешно отправлено администрации Countrypost.ru.';
		}
		catch (Exception $e)
		{
			$this->result->e	= 1;			
			$this->result->m	= $e->getMessage();
		}

		Stack::push('contactResult', $this->result);
		Func::redirect(BASEURL.'main/showContacts');
	}
	
	public function filterUnassignedOrders()
	{
		$this->filter('UnassignedOrders', '/showUnassignedOrders');
	}
	
	public function processUnassignedOrdersFilter($filter)
	{
		$filter->order_id = '';
		$filter->country_from = '';
		$filter->country_to = '';
		$filter->order_type = '';
		
		// сброс фильтра
		if (isset($_POST['resetFilter']) && $_POST['resetFilter'] == '1')
		{
			return $filter;
		}
		
		$filter->order_id = Check::int('order_number');
		$filter->country_from = Check::int('country_from');
		$filter->country_to = Check::int('country_to');
		$filter->order_type = Check::txt('order_type', 8, 6);
	
		if (isset($filter->order_type) AND
				$filter->order_type != "online" AND
				$filter->order_type != "offline" AND
				$filter->order_type != "service" AND
				$filter->order_type != "delivery" AND
				$filter->order_type != "mail_forwarding")
		{
			$filter->order_type = NULL;
		}
	
		return $filter;
	}

	private function showOrderTypeSelector()
	{
		try
		{
			Breadcrumb::setCrumb(array(
				'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] => 'Добавление нового заказа'),
				1, TRUE);

			View::showChild($this->viewpath . '/pages/showOrderSelector');
		}
		catch (Exception $e)
		{
			Func::redirect(BASEURL);
		}
	}

	public function createorder($order_type = NULL)
	{
		try
		{
			// роли
			if (isset($this->user->user_group) AND
				$this->user->user_group == 'manager')
			{
				Func::redirect(BASEURL);
			}

			// показываем выбор вида заказа
			if (empty($order_type))
			{
				return $this->showOrderTypeSelector();
			}

			// погнали
			$this->load->model('CountryModel', 'Country');
			$this->load->model('OrderModel', 'Orders');
            $this->load->model('OdetailModel', 'Odetails');
            $this->load->model('ManagerModel', 'Managers');

			$view['order_types'] = $this->Orders->getOrderTypes();
			$view['countries'] = $this->Country->getList();
			$view['dealers'] = $this->Managers->getMailForwardingManagers();
            $view['currencies'] = array();
			$view['order'] = NULL;
			$view['odetails'] = NULL;
			$view['order_type'] = $order_type;

			$view['editable_statuses'] = $this->Orders->getEditableStatuses('client');

			if ($order = $this->getCreatingOrder($order_type))
			{
				$view['order'] = $order;
				$view['odetails'] = $this->Odetails->getOrderDetails($order->order_id);
				$view['order_currency'] = $this->Orders->getOrderCurrency($order->order_id);
			}
			else
			{
				$view['order'] = $this->initEmptyOrder($order_type);
				$view['order_currency'] = '';
			}

			// рендер
			Breadcrumb::setCrumb(array(
				'http://'.$_SERVER['SERVER_NAME'].$this->viewpath.'createorder' => 'Добавление нового заказа'),
				1, TRUE);

			Breadcrumb::setCrumb(array(
				'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] => $view['order_types'][$order_type]),
				2, TRUE);

			View::showChild($this->viewpath . '/pages/createorder', $view);
		}
		catch (Exception $e) 
		{
		}
	}

	private function initEmptyOrder($order_type)
	{
		$order = new stdClass();
		$order->order_type = $order_type;
		$order->order_id = 0;
		$order->order_manager = 0;
		$order->order_client = $this->getOrderClient();
		$order->is_creating = 1;
		$order->order_status = 'pending';
		$order->order_country_from = '';
		$order->order_country_to = '';
		$order->order_city_to = '';

		$this->load->model('OrderModel', 'Orders');
		$order = $this->Orders->patchEmptyOrder($order);

		return $this->Orders->addOrder($order);
	}

	private function getCreatingOrder($order_type)
    {
		$this->load->model('OrderModel', 'Orders');
        return $this->Orders->getCreatingOrder($order_type, $this->getOrderClient());
    }

	public function addProduct()
	{
		parent::addProductManualAjax();
	}

    public function deleteProduct($odid)
    {
        parent::deleteProduct($odid);
    }

    public function deleteNewProduct($oid, $odid)
    {
        parent::deleteNewProduct($oid, $odid);
    }

    public function joinNewProducts($order_id)
    {
        parent::joinNewProducts($order_id);
    }

    public function removeNewJoint($order_id, $joint_id)
    {
        parent::removeNewJoint($order_id, $joint_id);
    }
	
	public function checkout($order_id)
	{
		try 
		{
			// 1. безопасность
			if (empty($this->user->user_group) OR
				$this->user->user_group != 'client')
			{				
				throw new Exception('Пожалуйста, войдите или зарегистрируйтесь на сайте.');
			}

			// 2. если после добавления товаров кто-то изменил заказ, сохраняем эти изменения
			$order = $this->updateOrder($order_id);

			// 3. заполняем заказ
			$order->order_client = $this->user->user_id;
			$order->order_status = ($order->order_manager ? 'processing' : 'pending');
			$order->order_date = date('Y-m-d H:i:s');			
			$order->is_creating = 0;

			// 4. пересчитываем и сохраняем заказ
			if ( ! $this->Orders->recalculate($order))
			{
				throw new Exception('Невозможно пересчитать стоимость заказа. Попоробуйте еще раз.');
			}

			$this->Orders->saveOrder($order);

            // 5. если выбран посредник, генерируем его предложение
            if ($order->order_manager)
            {
                $this->load->model('BidModel', 'Bids');

                $bid = new stdClass();
                $bid->order_id = $order->order_id;
                $bid->manager_id = $order->order_manager;
                $bid->client_id = $order->order_client;
                $bid->delivery_name = $order->preferred_delivery;
                $bid->status = 'active';
                $bid->created = $order->order_date;

				$this->Orders->prepareNewBidView($order, $order->order_manager);

				$bid->total_cost = $order->order_total_cost;
				$bid->manager_tax = $order->manager_tax;
				$bid->foto_tax = $order->foto_tax;
				$bid->delivery_cost = $order->order_delivery_cost;

                if ( ! ($bid = $this->Bids->addBid($bid)))
                {
                    throw new Exception('Ошибка создания предложения. Обратитесь к администрации сервиса.');
                }
            }
		}
		catch (Exception $e)
		{
			print($e->getMessage());
		}
	}
	
	public function order($order_id)
	{
		$order = $this->getPrivilegedOrder($order_id, FALSE);

		// залогиненных отправляем в личный кабинет
		if ($order AND
			isset($this->user->user_group))
		{
			Func::redirect("/{$this->user->user_group}/order/$order_id");
		}

		// чужой заказ ищем среди публичных заказов
		$order = $this->getPublicOrder($order_id, FALSE);

		if ($order)
		{
			parent::showPublicOrder();
			return;
		}

		// если заказа нет, уходим на главную
		if (empty($order))
		{
			Func::redirect(BASEURL);
		}

		// если вдруг чтото не сработало, уходим на главную
		Func::redirect(BASEURL);
	}

	protected function showOrderBreadcrumb($order)
	{
		Breadcrumb::setCrumb(array(
			"/main/order/{$order->order_id}" => "Заказ №{$order->order_id}"
		), 1, TRUE);
	}

    public function updateNewProduct($order_id, $odetail_id)
    {
        try
        {
            if ( ! is_numeric($order_id) OR
                ! is_numeric($odetail_id))
            {
                throw new Exception('Доступ запрещен.');
            }

            // роли и разграничение доступа
            $order = $this->getNewOrder(
                $order_id,
                "Заказ недоступен.");

            $this->load->model('OrderModel', 'Orders');
            $this->load->model('OdetailModel', 'Odetails');

            // Находим пользователя
            if (!empty($this->user))
            {
                if ($this->user->user_group != 'client')
                {
                    throw new Exception('Доступ запрещен.');
                }
                $client_id = $this->user->user_id;

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);

                if (empty($odetail))
                {
                    $client_id = UserModel::getTemporaryKey();

                    // находим товар
                    $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
                }
            }
            else
            {
                $client_id = UserModel::getTemporaryKey();

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
            }

            if (empty($odetail))
            {
                throw new Exception('Товар не найден.');
            }

            // парсим пользовательский ввод
            Check::reset_empties();
            $odetail->odetail_link				= Check::str('link', 500, 1);
            $odetail->odetail_shop				= Check::str('shop', 255, 0);
            $odetail->odetail_volume			= Check::str('volume', 255, 0);
            $odetail->odetail_tnved				= Check::str('tnved', 255, 0);
            $odetail->odetail_tracking		    = Check::str('odetail_tracking', 255, 0);
            $odetail->odetail_insurance		    = Check::chkbox('insurance');
            $odetail->odetail_foto_requested    = Check::chkbox('foto_requested');
            $odetail->odetail_search_requested  = Check::chkbox('search_requested');
            $odetail->odetail_product_name		= Check::str('name', 255, 0, '');
            $odetail->odetail_product_color		= Check::str('color', 255, 0, '');
            $odetail->odetail_product_size		= Check::str('size', 255, 0, '');
            $odetail->odetail_product_amount	= Check::int('amount');
            $odetail->odetail_comment			= Check::str('comment', 255, 1, '');

            // проверяем, загружается картинка или ссылка
            $img_selector = Check::str('img_selector', 4, 4, '');
            $is_file_uploaded = ($img_selector == 'file') ? TRUE : FALSE;
            $is_img_link = ($img_selector == 'link') ? TRUE : FALSE;

            if ($is_file_uploaded)
            {
                $userfile = isset($_FILES['userfile']) && !$_FILES['userfile']['error'];
                $odetail->odetail_img = NULL;
            }
            elseif ($is_img_link)
            {
                $userfile = FALSE;
                $odetail->odetail_img = Check::str('img', 4096, 1, NULL);
            }
            else
            {
                $userfile = FALSE;
            }

            // Валидация
            switch ($order->order_type)
            {
                case 'online' :
                    $this->onlineProductCheck($odetail);
                    break;
                case 'offline' :
                    $this->offlineProductCheck($odetail);
                    break;
                case 'delivery' :
                    $this->deliveryProductCheck($odetail);
                    break;
                case 'service' :
                    $this->serviceProductCheck($odetail);
                    break;
                case 'mail_forwarding' :
                    $this->mailforwardProductCheck($odetail);
                    break;
            }

            if ($is_file_uploaded AND
                empty($userfile))
            {
                if (isset($_FILES['userfile']) &&
                    $_FILES['userfile']['error'] == 1)
                {
                    throw new Exception('Максимальный размер картинки 3MB.');
                }
                else
                {
                    throw new Exception('Загрузите или добавьте ссылку на скриншот.');
                }
            }

            $client_id = $order->order_client;

            // открываем транзакцию
            $this->Odetails->updateOdetail($odetail);

            // загружаем файл
            if (isset($userfile) && $userfile)
            {
                $old = umask(0);
                // загрузка файла
                if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id")){
                    mkdir($_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id",0777);
                }

                $config['upload_path']			= $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id";
                $config['allowed_types']		= 'gif|jpeg|jpg|png|GIF|JPEG|JPG|PNG';
                $config['max_size']				= '3072';
                $config['encrypt_name'] 		= TRUE;
                $max_width						= 1024;
                $max_height						= 768;
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
                    throw new Exception(strip_tags(trim($this->upload->display_errors())));
                }

                $uploadedImg = $this->upload->data();

                // на сервере - '/upload/orders/'
                $filename = $_SERVER['DOCUMENT_ROOT']."/upload/orders/$client_id/{$odetail->odetail_id}.jpg";

                if (file_exists($filename))
                {
                    unlink($filename);
                }

                if (!rename($uploadedImg['full_path'], $filename))
                {
                    throw new Exception("Bad file name!");
                }

                $imageInfo = getimagesize($filename);

                if ($imageInfo[0]>$max_width || $imageInfo[1]>$max_height){

                    $config['image_library']	= 'gd2';
                    $config['source_image']		= $filename;
                    $config['maintain_ratio']	= TRUE;
                    $config['width']			= $max_width;
                    $config['height']			= $max_height;

                    $this->load->library('image_lib', $config); // загружаем библиотеку

                    $this->image_lib->resize(); // и вызываем функцию
                }
            }

            // отправляем cообщение на страницу
            $response['order_id'] = $order->order_id;
            $response['odetail_id'] = $odetail->odetail_id;
            $response['odetail_img'] = $odetail->odetail_img;
            $response['message'] = "Описание товара №{$odetail->odetail_id} сохранено.";
        }
        catch (Exception $e)
        {
			print_r($e);
            $response['is_error'] = TRUE;
            $response['message'] = $e->getMessage();
        }

        print(json_encode($response));
    }

    public function update_new_odetail_weight($order_id, $odetail_id, $weight)
    {
        try
        {
            if ( ! is_numeric($order_id) OR
                ! is_numeric($odetail_id) OR
                ! is_numeric($weight))
            {
                throw new Exception('Доступ запрещен.');
            }

            // роли и разграничение доступа
            $order = $this->getNewOrder(
                $order_id,
                "Заказ недоступен.");

            $this->load->model('OrderModel', 'Orders');
            $this->load->model('OdetailModel', 'Odetails');
            $this->load->model('OdetailJointModel', 'Joints');

            if (!empty($this->user))
            {
                $client_id = $this->user->user_id;

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);

                if (empty($odetail))
                {
                    $client_id = UserModel::getTemporaryKey();

                    // находим товар
                    $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
                }
            }
            else
            {
                $client_id = UserModel::getTemporaryKey();

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
            }

            if (empty($odetail))
            {
                throw new Exception('Товар не найден.');
            }

            $odetail->odetail_weight = $weight;

            // сохранение результатов
            $this->Odetails->addOdetail($odetail);

            // пересчитываем заказ
            if ( ! $this->Orders->recalculate($order))
            {
                throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
            }

            $this->Orders->saveOrder($order);

            // отправляем пересчитанные детали заказа
            $response = $this->prepareOrderUpdateJSON($order);
        }
        catch (Exception $e)
        {
            $response['is_error'] = TRUE;
            $response['message'] = $e->getMessage();
        }

        print(json_encode($response));
    }

    public function update_new_odetail_price($order_id, $odetail_id, $price)
    {
        try
        {
            if ( ! is_numeric($order_id) OR
                ! is_numeric($odetail_id) OR
                ! is_numeric($price))
            {
                throw new Exception('Доступ запрещен.');
            }

            // роли и разграничение доступа
            $order = $this->getNewOrder(
                $order_id,
                "Заказ недоступен.");

            $this->load->model('OrderModel', 'Orders');
            $this->load->model('OdetailModel', 'Odetails');
            $this->load->model('OdetailJointModel', 'Joints');

            if (!empty($this->user))
            {
                $client_id = $this->user->user_id;

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);

                if (empty($odetail))
                {
                    $client_id = UserModel::getTemporaryKey();

                    // находим товар
                    $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
                }
            }
            else
            {
                $client_id = UserModel::getTemporaryKey();

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
            }

            if (empty($odetail))
            {
                throw new Exception('Товар не найден.');
            }

            $odetail->odetail_price = $price;

            // сохранение результатов
            $this->Odetails->addOdetail($odetail);

            // пересчитываем заказ
            if ( ! $this->Orders->recalculate($order))
            {
                throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
            }

            $this->Orders->saveOrder($order);

            // отправляем пересчитанные детали заказа
            $response = $this->prepareOrderUpdateJSON($order);
        }
        catch (Exception $e)
        {
            $response['is_error'] = TRUE;
            $response['message'] = $e->getMessage();
        }

        print(json_encode($response));
    }

    public function update_new_odetail_pricedelivery($order_id, $odetail_id, $pricedelivery)
    {
        try
        {
            if ( ! is_numeric($order_id) OR
                ! is_numeric($odetail_id) OR
                ! is_numeric($pricedelivery))
            {
                throw new Exception('Доступ запрещен.');
            }

            // роли и разграничение доступа
            $order = $this->getNewOrder(
                $order_id,
                "Заказ недоступен.");

            $this->load->model('OrderModel', 'Orders');
            $this->load->model('OdetailModel', 'Odetails');
            $this->load->model('OdetailJointModel', 'Joints');

            if (!empty($this->user))
            {
                $client_id = $this->user->user_id;

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);

                if (empty($odetail))
                {
                    $client_id = UserModel::getTemporaryKey();

                    // находим товар
                    $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
                }
            }
            else
            {
                $client_id = UserModel::getTemporaryKey();

                // находим товар
                $odetail = $this->Odetails->getClientOdetailById($order_id, $odetail_id, $client_id);
            }

            if (empty($odetail))
            {
                throw new Exception('Товар не найден.');
            }

            $odetail->odetail_pricedelivery = $pricedelivery;

            // сохранение результатов
            $this->Odetails->addOdetail($odetail);

            // пересчитываем заказ
            if ( ! $this->Orders->recalculate($order))
            {
                throw new Exception('Невожможно пересчитать стоимость заказа. Попоробуйте еще раз.');
            }

            $this->Orders->saveOrder($order);

            // отправляем пересчитанные детали заказа
            $response = $this->prepareOrderUpdateJSON($order);
        }
        catch (Exception $e)
        {
            $response['is_error'] = TRUE;
            $response['message'] = $e->getMessage();
        }

        print(json_encode($response));
    }

    public function update_new_joint_pricedelivery($order_id, $joint_id, $cost)
    {
        parent::update_joint_pricedelivery($order_id, $joint_id, $cost);
    }

    public function update_new_order_dealer_id($order_id, $dealer_id)
    {
        try
        {
            if ( ! is_numeric($order_id) OR
                ! is_numeric($dealer_id))
            {
                throw new Exception('Доступ запрещен.');
            }

            // роли и разграничение доступа
            $order = $this->getNewOrder(
                $order_id,
                "Заказ недоступен.");

			$this->load->model('ManagerModel', 'Managers');

			$dealer = $this->Managers->getById($dealer_id);
			$dealer_country = $dealer->manager_country;

            $this->load->model('OrderModel', 'Orders');

            $order->order_manager = $dealer_id;
            $order->order_country_from = $dealer_country;

            $this->Orders->saveOrder($order);

            // отправляем пересчитанные детали заказа
            $response = $this->prepareOrderUpdateJSON($order);
        }
        catch (Exception $e)
        {
            $response['is_error'] = TRUE;
            $response['message'] = $e->getMessage();
        }

        print(json_encode($response));
        exit();
    }

	public function showScreen($oid)
	{
		try
		{
			if (isset($this->user->user_group))
			{
				Func::redirect(BASEURL . $this->user->user_group . '/showScreen/' . $oid);
			}
			else
			{
				$this->showTempScreenshot($oid);
			}
		}
		catch (Exception $e)
		{}
	}

	public function showPublicScreenshot($oid)
	{
		try
		{
			if (empty($oid) OR
				! is_numeric($oid))
			{
				throw new Exception('Товар не найден.');
			}

			$this->load->Model('OdetailModel', 'Odetails');
			$odetail = $this->Odetails->getById($oid);

			if (empty($odetail))
			{
				throw new Exception('Товар не найден.');
			}

			$order = $this->getPublicOrder($odetail->odetail_order, 'Заказ не найден.');

			if (empty($order))
			{
				throw new Exception('Заказ не найден.');
			}

			$this->showScreenshot($oid, $order->order_client);
		}
		catch (Exception $e)
		{}
	}

	protected function showTempScreenshot($oid)
	{
		header('Content-type: image/jpg');
		$this->load->model('OdetailModel', 'OdetailModel');
		$user_id = UserModel::getTemporaryKey();

		if ($Detail = $this->OdetailModel->getInfo(array('odetail_client' => $user_id, 'odetail_id' => intval($oid))))
		{
			readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/$user_id/$oid.jpg");
		}
	}

	private function showScreenshot($oid, $user_id)
	{
		header('Content-type: image/jpg');
		readfile("{$_SERVER['DOCUMENT_ROOT']}/upload/orders/$user_id/$oid.jpg");
	}

	public function update_odetail_weight($order_id, $odetail_id, $weight)
	{
		parent::update_odetail_weight($order_id, $odetail_id, $weight);
	}

	public function update_odetail_price($order_id, $odetail_id, $price)
	{
		parent::update_odetail_price($order_id, $odetail_id, $price);
	}

	public function update_odetail_tracking($order_id, $odetail_id, $tracking)
	{
		parent::update_odetail_tracking($order_id, $odetail_id, $tracking);
	}

	public function update_odetail_pricedelivery($order_id, $odetail_id, $pricedelivery)
	{
		parent::update_odetail_pricedelivery($order_id, $odetail_id, $pricedelivery);
	}

	public function update_joint_pricedelivery($order_id, $joint_id, $cost)
	{
		parent::update_joint_pricedelivery($order_id, $joint_id, $cost);
	}
}