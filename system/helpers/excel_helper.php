<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Excel
{
	public static function ExportOrder($order_id, $odetails, $fotos)
	{
		$ci = get_instance();
		$ci->load->library('PHPExcel');
		
		// настройки документа
		$document = new PHPExcel();
		
		$document
			->getProperties()
			->setCreator("Countrypost.ru ©")
			->setLastModifiedBy("Countrypost.ru ©")
			->setTitle("Заказ №$order_id")
			->setSubject("Заказ №$order_id")
			->setDescription("Заказ №$order_id")
			->setKeywords("")
			->setCategory("Confidential");

		$sheet = $document->setActiveSheetIndex(0);
		$sheet->setTitle("Заказ №$order_id");
		
		// заголовок
		$sheet
            ->setCellValue('A1', 'ID товара')
            ->setCellValue('B1', 'Наименование товара')
            ->setCellValue('C1', 'Цвет')
            ->setCellValue('D1', 'Размер')
            ->setCellValue('E1', 'Кол-во')
            ->setCellValue('F1', 'Ссылка на скриншот')
            ->setCellValue('G1', 'Ссылка на товар')
            ->setCellValue('H1', 'Цена')
            ->setCellValue('I1', 'Местная доставка');

		$headerStyle = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			)
		);

		$cellStyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			),
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			)
		);

		$sheet->getStyle('A1')->applyFromArray($headerStyle);
		$sheet->getStyle('B1')->applyFromArray($headerStyle);
		$sheet->getStyle('C1')->applyFromArray($headerStyle);
		$sheet->getStyle('D1')->applyFromArray($headerStyle);
		$sheet->getStyle('E1')->applyFromArray($headerStyle);
		$sheet->getStyle('F1')->applyFromArray($headerStyle);
		$sheet->getStyle('G1')->applyFromArray($headerStyle);
		$sheet->getStyle('H1')->applyFromArray($headerStyle);
		$sheet->getStyle('I1')->applyFromArray($headerStyle);
		$sheet->getStyle('A1:I1')->getAlignment()->setWrapText(true);
		$sheet->getRowDimension('1')->setRowHeight(40);
		
		$sheet->getColumnDimension('A')->setWidth(9);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(11);
		$sheet->getColumnDimension('E')->setWidth(8);
		$sheet->getColumnDimension('F')->setWidth(22);
		$sheet->getColumnDimension('G')->setWidth(22);
		$sheet->getColumnDimension('H')->setWidth(10);
		$sheet->getColumnDimension('I')->setWidth(10);
		
		// отрисовка товаров
		if (is_array($odetails) AND
			count($odetails))
		{
			$i = 1;
			
			foreach ($odetails as $odetail)
			{
				$i++;
				
				$sheet
					->setCellValue("A$i", $odetail->odetail_id)
					->setCellValue("B$i", $odetail->odetail_product_name)
					->setCellValue("C$i", $odetail->odetail_product_color)
					->setCellValue("D$i", $odetail->odetail_product_size)
					->setCellValue("E$i", $odetail->odetail_product_amount)
					->setCellValue("F$i", $odetail->odetail_img)
					->setCellValue("G$i", $odetail->odetail_link)
					->setCellValue("H$i", $odetail->odetail_price)
					->setCellValue("I$i", $odetail->odetail_pricedelivery);

				$sheet->getStyle("A$i")->applyFromArray($cellStyle);
				$sheet->getStyle("B$i")->applyFromArray($cellStyle);
				$sheet->getStyle("C$i")->applyFromArray($cellStyle);
				$sheet->getStyle("D$i")->applyFromArray($cellStyle);
				$sheet->getStyle("E$i")->applyFromArray($cellStyle);
				$sheet->getStyle("F$i")->applyFromArray($cellStyle);
				$sheet->getStyle("G$i")->applyFromArray($cellStyle);
				$sheet->getStyle("H$i")->applyFromArray($cellStyle);
				$sheet->getStyle("I$i")->applyFromArray($cellStyle);

				if ( ! empty($odetail->odetail_img))
				{
					$sheet
						->getCell("F$i")
						->getHyperlink()
						->setUrl($odetail->odetail_img);
				}

				if (parse_url($odetail->odetail_link))
				{
					$sheet
						->getCell("G$i")
						->getHyperlink()
						->setUrl($odetail->odetail_link);
				}
			}
		}
	
		// экспорт документа
		$excel_path = UPLOAD_DIR . "order$order_id.xlsx";
		$file_writer = PHPExcel_IOFactory::createWriter($document, 'Excel2007');
		$file_writer->save($excel_path);

		// архивирование
		$zip_folder = "Order#$order_id";
		$zip_path = UPLOAD_DIR . "order$order_id";
		
		$zip = new ZipArchive();
		$zip->open($zip_path, ZipArchive::CREATE);
		$zip->addEmptyDir($zip_folder); 
		
		// эксель
		$zip->addFile($excel_path, "$zip_folder/Order#$order_id.xlsx");
		
		// картинки
		foreach ($fotos as $file_name => $path)
		{
			$zip->addFile($path, "$zip_folder/$file_name");
		}
		
		$zip->close();
		
		// отдаем файл
		header("Content-Disposition: attachment; filename=\"Заказ №$order_id.zip\"");
		header("Content-Type: application/octet-stream");
		readfile($zip_path);
		
		// зачистка
		unlink($excel_path);
		unlink($zip_path);		
	}

	public static function ExportPackage($package_id, $pdetails, $fotos, $joint_fotos)
	{
		$ci = get_instance();
		$ci->load->library('PHPExcel');
		
		// настройки документа
		$document = new PHPExcel();
		
		$document
			->getProperties()
			->setCreator("Countrypost.ru ©")
			->setLastModifiedBy("Countrypost.ru ©")
			->setTitle("Посылка №$package_id")
			->setSubject("Посылка №$package_id")
			->setDescription("Посылка №$package_id")
			->setKeywords("")
			->setCategory("Confidential");

		$sheet = $document->setActiveSheetIndex(0);
		$sheet->setTitle("Посылка №$package_id");
		
		// заголовок
		$sheet
            ->setCellValue('A1', 'ID товара')
            ->setCellValue('B1', 'Наименование товара')
            ->setCellValue('C1', 'Цвет')
            ->setCellValue('D1', 'Размер')
            ->setCellValue('E1', 'Кол-во')
            ->setCellValue('F1', 'Ссылка на скриншот')
            ->setCellValue('G1', 'Ссылка на товар')
            ->setCellValue('H1', 'Убрать коробки')
            ->setCellValue('I1', 'Убрать инвойсы');

		$headerStyle = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			),
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			)
		);

		$cellStyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			),
			'borders' => array(
				'top' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'left' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			)
		);

		$sheet->getStyle('A1')->applyFromArray($headerStyle);
		$sheet->getStyle('B1')->applyFromArray($headerStyle);
		$sheet->getStyle('C1')->applyFromArray($headerStyle);
		$sheet->getStyle('D1')->applyFromArray($headerStyle);
		$sheet->getStyle('E1')->applyFromArray($headerStyle);
		$sheet->getStyle('F1')->applyFromArray($headerStyle);
		$sheet->getStyle('G1')->applyFromArray($headerStyle);
		$sheet->getStyle('H1')->applyFromArray($headerStyle);
		$sheet->getStyle('I1')->applyFromArray($headerStyle);
		$sheet->getStyle('A1:I1')->getAlignment()->setWrapText(true);
		$sheet->getRowDimension('1')->setRowHeight(40);
		
		$sheet->getColumnDimension('A')->setWidth(9);
		$sheet->getColumnDimension('B')->setWidth(16);
		$sheet->getColumnDimension('C')->setWidth(16);
		$sheet->getColumnDimension('D')->setWidth(11);
		$sheet->getColumnDimension('E')->setWidth(8);
		$sheet->getColumnDimension('F')->setWidth(22);
		$sheet->getColumnDimension('G')->setWidth(22);
		$sheet->getColumnDimension('H')->setWidth(10);
		$sheet->getColumnDimension('I')->setWidth(10);
		
		// отрисовка товаров
		if (is_array($pdetails) AND
			count($pdetails))
		{
			$i = 1;
			
			foreach ($pdetails as $pdetail)
			{
				$i++;
				
				$sheet
					->setCellValue("A$i", $pdetail->pdetail_id)
					->setCellValue("B$i", $pdetail->pdetail_product_name)
					->setCellValue("C$i", $pdetail->pdetail_product_color)
					->setCellValue("D$i", $pdetail->pdetail_product_size)
					->setCellValue("E$i", $pdetail->pdetail_product_amount)
					->setCellValue("F$i", $pdetail->pdetail_img)
					->setCellValue("G$i", $pdetail->pdetail_link)
					->setCellValue("H$i", $pdetail->pdetail_special_boxes ? 'да' : '')
					->setCellValue("I$i", $pdetail->pdetail_special_invoices ? 'да' : '');

				$sheet->getStyle("A$i")->applyFromArray($cellStyle);
				$sheet->getStyle("B$i")->applyFromArray($cellStyle);
				$sheet->getStyle("C$i")->applyFromArray($cellStyle);
				$sheet->getStyle("D$i")->applyFromArray($cellStyle);
				$sheet->getStyle("E$i")->applyFromArray($cellStyle);
				$sheet->getStyle("F$i")->applyFromArray($cellStyle);
				$sheet->getStyle("G$i")->applyFromArray($cellStyle);
				$sheet->getStyle("H$i")->applyFromArray($cellStyle);
				$sheet->getStyle("I$i")->applyFromArray($cellStyle);

				if ( ! empty($pdetail->pdetail_img))
				{
					$sheet
						->getCell("F$i")
						->getHyperlink()
						->setUrl($pdetail->pdetail_img);
				}

				if (parse_url($pdetail->pdetail_link))
				{
					$sheet
						->getCell("G$i")
						->getHyperlink()
						->setUrl($pdetail->pdetail_link);
				}
			}
		}
	
		// экспорт документа
		$excel_path = UPLOAD_DIR . "package$package_id.xlsx";
		$file_writer = PHPExcel_IOFactory::createWriter($document, 'Excel2007');
		$file_writer->save($excel_path);

		// архивирование
		$zip_folder = "Package#$package_id";
		$zip_path = UPLOAD_DIR . "package$package_id";
		
		$zip = new ZipArchive();
		$zip->open($zip_path, ZipArchive::CREATE);
		$zip->addEmptyDir($zip_folder); 
		
		// эксель
		$zip->addFile($excel_path, "$zip_folder/Package#$package_id.xlsx");
		
		// картинки
		foreach ($fotos as $pdetail_id => $pdetail_files)
		{
			$pdetail_folder = "$zip_folder/$pdetail_id";
			$zip->addEmptyDir($pdetail_folder);
			
			foreach ($pdetail_files as $file_name => $path)
			{
				$zip->addFile($path, "$pdetail_folder/$file_name");
			}
		}
		
		// фото объединенных товаров
		foreach ($joint_fotos as $pdetail_joint_id => $joint_files)
		{
			$joint_folder = "$zip_folder/joint_$pdetail_joint_id";
			$zip->addEmptyDir($joint_folder);
			
			foreach ($joint_files as $file_name => $path)
			{
				$zip->addFile($path, "$joint_folder/$file_name");
			}
		}
		
		$zip->close();
		
		// отдаем файл
		header("Content-Disposition: attachment; filename=\"Посылка №$package_id.zip\"");
		header("Content-Type: application/octet-stream");
		readfile($zip_path);
		
		// зачистка
		unlink($excel_path);
		unlink($zip_path);		
	}

	// импорт в кабинете админа
	public static function ImportOrder($order_id, $client_id, $manager_id, $country_id, $odetails, $fotos)
	{
		$path_template = UPLOAD_DIR."orders/$order_id/importOrder.".date('Y.m.d.H.i.s');
		$import_filename = $path_template . ".zip";
		$zip_folder = $path_template."/";

		// аплоудим файл
		$config['upload_path']			= UPLOAD_DIR."orders/$order_id/";
		$config['allowed_types']		= 'zip|ZIP';
		$config['remove_spaces'] 		= FALSE;
		$config['overwrite'] 			= TRUE;
		$config['encrypt_name'] 		= TRUE;
				
		if ( ! is_dir($config['upload_path']) && 
			!	(mkdir($config['upload_path'], 0777, true) || 
				chmod($config['upload_path'], 0777)))
		{
			throw new Exception('Ошибка файловой системы. Обратитесь к администратору.');
		}

		$ci = get_instance();
		$ci->load->library('upload', $config);
		$uploaded = false;
		
		if ($ci->upload->do_upload('importOrder'))	
		{
			$import_details = $ci->upload->data();
			
			if ( ! rename(
				$import_details['full_path'],
				$import_filename))
			{
				throw new Exception("Bad file name!");
			}
			
			$uploaded = TRUE;
		}
		
		if ( ! $uploaded)
		{
			return;
		}
		
		// распаковка
		$unzipped = FALSE;
		$zip = new ZipArchive();
		
		if ($zip->open($import_filename) === TRUE) 
		{
			$zip->extractTo($zip_folder);
			$zip->close();
			$unzipped = TRUE;
		}

		if ( ! $unzipped)
		{
			return;
		}

		// поиск документа
		$xls_path = FALSE;
		
		foreach (scandir($zip_folder . "Order#$order_id") as $filename)
		{
			if (self::endsWith($filename, '.xls') OR
				self::endsWith($filename, '.xlsx'))
			{
				$xls_path = $zip_folder . "Order#$order_id/" . $filename;
				break;
			}
		}
		
		if ( ! $xls_path)
		{
			return;
		}

		// парсим док
		$ci->load->library('PHPExcel');
		$document = PHPExcel_IOFactory::load($xls_path);
		$document->setActiveSheetIndex(0);
		$sheet = $document->getActiveSheet();

		// парсинг товаров
		$updated_details = array();
		$new_details = array();
		$imported_ids = array();
		$ci->load->model('OdetailModel', 'Odetails');
		
		for ($i = 2; $i <= $sheet->getHighestRow(); $i++)
		{
			$odetail_id	= $sheet->getCell("A$i")->getvalue(); 
			$import_detail = FALSE;
			$product_exists = FALSE;
			
			// подтягиваем продукт из базы
			if (is_numeric($odetail_id))
			{
				$import_detail = $ci->Odetails->getFilteredDetails(array(
					'odetail_id' => $odetail_id,
					'odetail_order' => $order_id,
					'odetail_client' => $client_id,
				));
				
				$import_detail = count($import_detail) ?
					$import_detail[0] :
					FALSE;
			}

			// либо генерируем новый
			if ( ! $import_detail)
			{
				if ( ! empty($odetail_id))
				{
					$user_ids[] = $odetail_id;
				}
				
				$import_detail = new OdetailModel();
				$import_detail->odetail_order				= $order_id;
				$import_detail->odetail_client				= $client_id;
				$import_detail->odetail_manager				= $manager_id;
				$import_detail->odetail_country				= $country_id;
				$import_detail->odetail_shop_name			= '';
				$import_detail->odetail_status				= 'processing';
				$import_detail->odetail_price_usd			= 0;
				$import_detail->odetail_pricedelivery_usd	= 0;
				$import_detail->odetail_joint_id			= 0;
			}
			else
			{
				$product_exists = TRUE;
			}

			// парсим данные из экселя
			$import_detail->odetail_product_name 	= strval($sheet->getCell("B$i")->getvalue());
			$import_detail->odetail_product_color 	= strval($sheet->getCell("C$i")->getvalue());
			$import_detail->odetail_product_size 	= strval($sheet->getCell("D$i")->getvalue());
			$import_detail->odetail_product_amount 	= strval($sheet->getCell("E$i")->getvalue());
			$import_detail->odetail_img			 	= strval($sheet->getCell("F$i")->getvalue());
			$import_detail->odetail_link		 	= strval($sheet->getCell("G$i")->getvalue());
			$import_detail->odetail_price		 	= strval($sheet->getCell("H$i")->getvalue());
			$import_detail->odetail_pricedelivery 	= strval($sheet->getCell("I$i")->getvalue());

			// добавляем в список
			if ($product_exists)
			{
				$updated_details[] = $import_detail;
				$imported_ids[] = $import_detail->odetail_id;
			}
			else
			{
				$new_details[] = $import_detail;
			}
		}
		
		$id_mapping = array();
		$i = 0;
		
		foreach ($new_details as $import_detail)
		{
			$new_detail = $ci->Odetails->addOdetail($import_detail);
			$imported_ids[] = $new_detail->odetail_id;
			
			// собираем маппинг новых номеров товаров и картинок
			$id_mapping[$new_detail->odetail_id] = $user_ids[$i];
			$i++;
		}
		
		foreach ($updated_details as $import_detail)
		{
			$updated_detail = $ci->Odetails->addOdetail($import_detail);
		}
		
		// удаление отсутствующих товаров
		$unimported_details = $ci->Odetails->getUnimportedDetails($order_id, $imported_ids);

		if ( ! empty($unimported_details))
		{
			foreach ($unimported_details as $detail)
			{
				$detail->odetail_status = 'deleted';
				$ci->Odetails->addOdetail($detail);
			}
		}
		
		// пересчитываем заказ
		$ci->load->model('OrderModel', 'Orders');
		$ci->load->model('OdetailJointModel', 'Joints');
		$ci->load->model('ConfigModel', 'Config');
		
		$order = $ci->Orders->getById($order_id);
		$order->order_status = $ci->Odetails->getTotalStatus($order->order_id);
		$order->order_status = $ci->Orders->calculateOrderStatus($order->order_status);
		
		$ci->Orders->recalculate($order, $ci->Odetails, $ci->Joints, $ci->Config);
		$ci->Orders->saveOrder($order);
		
		// переименование картинок согласно новым номерам товаров
		if (is_dir($zip_folder))
		{
			foreach (scandir("{$zip_folder}Order#$order_id/") as $filename)
			{
				if (self::endsWith($filename, '.jpg') AND
					$filename != '.' AND
					$filename != '..')
				{
					foreach ($id_mapping as $odetail_id => $user_id)
					{
						if ($filename == "$user_id.jpg")
						{
							rename("$zip_folder/Order#$order_id/$filename", "$zip_folder/Order#$order_id/$odetail_id.jpg");
							break;
						}					
					}
				}
			}
		}
		
		// копируем картинки
		if (is_dir($zip_folder))
		{
			foreach (scandir("{$zip_folder}Order#$order_id/") as $filename)
			{
				if (self::endsWith($filename, '.jpg') AND
					$filename != '.' AND
					$filename != '..')
				{
					rename("$zip_folder/Order#$order_id/$filename", UPLOAD_DIR."orders/$client_id/$filename");
				}
			}
		}
		
		// зачистка
		unlink($import_filename);
		self::deleteAll($zip_folder);
	}

	// импорт в кабинете клиента
	public static function ImportClientOrder($userfile, $client_id, $country_id)
	{
		try
		{
			$import_filename = UPLOAD_DIR."orders/$client_id/importOrder." . date('Y.m.d.H.i.s') . ".xls";
			//print_r($import_filename);die();
			// аплоудим файл
			$config['upload_path']			= UPLOAD_DIR."orders/$client_id/";
			$config['allowed_types']		= 'xls|xlsx|XLS|XLSX';
			$config['remove_spaces'] 		= FALSE;
			$config['overwrite'] 			= TRUE;
			$config['encrypt_name'] 		= TRUE;
					
			if ( ! is_dir($config['upload_path']) && 
				!	(mkdir($config['upload_path'], 0777, true) || 
					chmod($config['upload_path'], 0777)))
			{
				throw new Exception('Ошибка файловой системы. Обратитесь к администратору.');
			}

			$ci = get_instance();
			$ci->load->library('upload', $config);
			$uploaded = FALSE;
			
			if ($ci->upload->do_upload('importfile'))
			{
				$import_details = $ci->upload->data();
				
				if ( ! rename(
					$import_details['full_path'],
					$import_filename))
				{
					throw new Exception("Bad file name!");
				}
				
				$uploaded = TRUE;
			}
			
			if ( ! $uploaded)
			{
				throw new Exception("Файл не загружен.");
			}
			
			// парсим док
			$ci->load->library('PHPExcel');
			$document = PHPExcel_IOFactory::load($import_filename);
			$document->setActiveSheetIndex(0);
			$sheet = $document->getActiveSheet();

			// парсинг товаров
			$new_details = array();
			$ci->load->model('OdetailModel', 'Odetails');
			
			for ($i = 2; $i <= $sheet->getHighestRow(); $i++)
			{
				$import_detail = FALSE;
				
				$import_detail = new OdetailModel();
				$import_detail->odetail_order				= 0;
				$import_detail->odetail_client				= $client_id;
				$import_detail->odetail_manager				= 0;
				$import_detail->odetail_country				= $country_id;
				$import_detail->odetail_shop_name			= '';
				$import_detail->odetail_status				= 'processing';
				$import_detail->odetail_price_usd			= 0;
				$import_detail->odetail_pricedelivery_usd	= 0;
				$import_detail->odetail_joint_id			= 0;

				// парсим данные из экселя
				$import_detail->odetail_product_name 	= strval($sheet->getCell("A$i")->getvalue());
				$import_detail->odetail_product_color 	= strval($sheet->getCell("B$i")->getvalue());
				$import_detail->odetail_product_size 	= strval($sheet->getCell("C$i")->getvalue());
				$import_detail->odetail_product_amount 	= strval($sheet->getCell("D$i")->getvalue());
				$import_detail->odetail_img			 	= strval($sheet->getCell("E$i")->getvalue());
				$import_detail->odetail_link		 	= strval($sheet->getCell("F$i")->getvalue());
				$import_detail->odetail_price		 	= strval($sheet->getCell("G$i")->getvalue());
				$import_detail->odetail_pricedelivery 	= strval($sheet->getCell("H$i")->getvalue());
				
				// валидация
				$row = trim(
					$import_detail->odetail_product_name .
					$import_detail->odetail_product_color .
					$import_detail->odetail_product_size .
					$import_detail->odetail_product_amount .
					$import_detail->odetail_img .
					$import_detail->odetail_link .
					$import_detail->odetail_price .
					$import_detail->odetail_pricedelivery);
					
				// добавляем в список
				if ( ! empty($row))
				{
					$new_details[] = $import_detail;
				}
			}
			
			$anything_saved = FALSE;
			
			foreach ($new_details as $odetail)
			{
				if ($ci->Odetails->addOdetail($odetail))
				{
					$anything_saved = TRUE;
				}
			}
			
			if ( ! $anything_saved)
			{
				throw new Exception("Товары не найдены.");
			}
		}
		catch (Exception $ex)
		{PRINT_R($ex);DIE();
			if (is_file($import_filename))
			{
				unlink($import_filename);
				throw $ex;
			}
		}
	}

	private static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		$start  = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}
	
	private static function deleteAll($directory, $empty = false) 
	{ 
		if(substr($directory,-1) == "/") { 
			$directory = substr($directory,0,-1); 
		} 

		if(!file_exists($directory) || !is_dir($directory)) { 
			return false; 
		} elseif(!is_readable($directory)) { 
			return false; 
		} else { 
			$directoryHandle = opendir($directory); 
			
			while ($contents = readdir($directoryHandle)) { 
				if($contents != '.' && $contents != '..') { 
					$path = $directory . "/" . $contents; 
					
					if(is_dir($path)) { 
						self::deleteAll($path); 
					} else { 
						unlink($path); 
					} 
				} 
			} 
			
			closedir($directoryHandle); 

			if($empty == false) { 
				if(!rmdir($directory)) { 
					return false; 
				} 
			} 
			
			return true; 
		} 
	} 
}
?>
