<?php

/**
* localizator module main file.
*
* @author 0RS <admin@prestalab.ru>
* @link http://prestalab.ru/
* @copyright Copyright &copy; 2009-2012 PrestaLab.Ru
* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version 2.1
*/

if (!defined('_PS_VERSION_'))
	exit;

class localizator extends Module
{
	private $_html = '';
	private $_postErrors = array();

	function __construct()
	{
		$this->name = 'localizator';
		$this->tab = 'i18n_localization';
		$this->version = '2.2';
		$this->author = 'PrestaLab.Ru';
		$this->need_instance = 0;
		//Ключик из addons.prestashop.com
		$this->module_key='';

		parent::__construct();

		$this->displayName = $this->l('Localizator');
		$this->description = $this->l('DataBase translation, import countries and clean database');
	}

	public function install()
	{
		self::rcopy(dirname(__FILE__).'/override', _PS_OVERRIDE_DIR_);
		return (parent::install()
			&& $this->registerHook('displayBackOfficeHeader')
		);
	}

	private static function rcopy($src, $dst)
	{
		if (is_dir($src))
		{
			$files = scandir($src);
			foreach ($files as $file)
			{
				$file=str_replace('php_', 'php', $file);
				if ($file != "." && $file != "..") self::rcopy("$src/$file", "$dst/$file"); 
			}
		}
			else if (file_exists($src)) copy($src, $dst);
	}

	public function getContent()
	{
		$this->_postProcess();
		$this->_displayForm();
		return $this->_html;
	}

	private function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Перевод')
		);
		$this->toolbar_btn['new'] = array(
            'href' => 'index.php?controller=AdminModules&submitTransGen&configure=' . $this->name . '&token=' . Tools::getAdminToken('AdminModules' . (int)(Tab::getIdFromClassName('AdminModules')) . (int)($this->context->cookie->id_employee)),
			'desc' => $this->l('Генерация')
		);
		return $this->toolbar_btn;
	}

	protected function _displayForm()
	{
		$this->_display = 'index';
		

		$this->fields_form[0]['form'] = array(
				'legend' => array(
				'title' => $this->l('Перевод'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
				'description'=>'Импорт переводов в базу данных. Осуществляется перевод вкладок, стран, статусов заказов и прочих данных, которые нельзя перевести на вкладке перевод.<br/>
											Порядок импорта перевода:<br/>
											1. Английский язык (язык с id=1) не должен быть удален, иначе ничего хорошего не выйдет<br/>
											2. Должен быть установлен языковой пакет и/или добавлен русский язык<br/>
											3. В настройках профиля администратора должен быть установлен Русский язык интерфейса<br/>
											4. Нажмите кнопку "Генерация". Будут созданы файлы tab_lang, order_state_lang, country_lang, contact_lang, discount_type_lang, profile_lang, quick_access_lang, order_return_state_lang, meta_lang, carrier_lang, order_message_lang, group_lang с актуальными значениями полей. Если перевод изменился, то вы можете самостоятельно добавить новый перевод на вкладке Локализаци/Перевод в меню Перевод модулей<br/>
											5. Выберите в выпадающем списке Язык перевода (В локализатор включен русский перевод, но можно добавить свой)<br/>
											6. Нажмите кнопку "Перевод"',
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Язык'),
					'desc' => $this->l('Импорт перевода для выбранного языка.'),
					'name' => 'localizator_lng',
					'options' => array(
						'query' => Language::getLanguages(),
						'id' => 'id_lang',
						'name' => 'name'
					)
				),
			),
			
			'submit' => array(
				'name' => 'submitTransLate',
				'title' => $this->l('Перевод'),
				'class' => 'button'
			)
		);

		
		$this->fields_form[1]['form'] = array(
				'legend' => array(
				'title' => $this->l('Региональные настройки'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Язык'),
					'desc' => $this->l(''),
					'name' => 'localizator_lang',
					'options' => array(
						'query' => Language::getLanguages(),
						'id' => 'id_lang',
						'name' => 'name'
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Очистка регионов'),
					'desc' => $this->l(''),
					'name' => 'localizator_drop_states',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_states_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_states_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Импорт регионов'),
					'desc' => $this->l(''),
					'name' => 'localizator_import_states',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_import_states_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_import_states_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Импорт регионов 2'),
					'desc' => $this->l('Импорт городов и почтовых кодов (они были в версии 1.4, но в версии 1.5 пропали)'),
					'name' => 'localizator_import_states2',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_import_states2_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_import_states2_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Очистка стран'),
					'desc' => $this->l('Кроме выбранной'),
					'name' => 'localizator_drop_countries',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_countries_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_countries_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Очистка налогов'),
					'desc' => $this->l(''),
					'name' => 'localizator_drop_taxes',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_taxes_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_taxes_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
/*
				array(
					'type' => 'radio',
					'label' => $this->l('Импорт налогов'),
					'desc' => $this->l(''),
					'name' => 'localizator_import_taxes',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_import_taxes_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_import_taxes_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
*/
			),
			
			'submit' => array(
				'name' => 'submitRegional',
				'title' => $this->l('Выполнить'),
				'class' => 'button'
			)
		);

		$this->fields_form[2]['form'] = array(
				'legend' => array(
				'title' => $this->l('Очистка базы'),
				'image' => _PS_ADMIN_IMG_.'information.png'
			),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Товары'),
					'desc' => $this->l('Очистка продуктов, категорий, комбинаций, свойств, тегов, картинок, сцен'),
					'name' => 'localizator_drop_products',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_products_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_products_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Заказы'),
					'desc' => $this->l('Очистка заказов, клиентов, корзин, гостей, сообщений'),
					'name' => 'localizator_drop_orders',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_orders_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_orders_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Поставщики и производители'),
					'desc' => $this->l('Очистка поставщиков, производителей'),
					'name' => 'localizator_drop_mansup',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_mansup_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_mansup_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Разное'),
					'desc' => $this->l('Очистка соединений, статистики, поискового индекса, магазинов'),
					'name' => 'localizator_drop_other',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'localizator_drop_other_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'localizator_drop_other_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
			
			),
			
			'submit' => array(
				'name' => 'submitPrepare',
				'title' => $this->l('Выполнить'),
				'class' => 'button'
			)
		);

		$this->fields_value['localizator_lng'] = Tools::getValue('localizator_lng');
		$this->fields_value['localizator_lang'] = Tools::getValue('localizator_lang');
		$this->fields_value['localizator_drop_states'] = Tools::getValue('localizator_drop_states');
		$this->fields_value['localizator_import_states'] = Tools::getValue('localizator_import_states');
		$this->fields_value['localizator_import_states2'] = Tools::getValue('localizator_import_states2');
		$this->fields_value['localizator_drop_countries'] = Tools::getValue('localizator_drop_countries');
		$this->fields_value['localizator_drop_taxes'] = Tools::getValue('localizator_drop_taxes');
		$this->fields_value['localizator_import_taxes'] = Tools::getValue('localizator_import_taxes');
		$this->fields_value['localizator_drop_products'] = Tools::getValue('localizator_drop_products');
		$this->fields_value['localizator_drop_orders'] = Tools::getValue('localizator_drop_orders');
		$this->fields_value['localizator_drop_mansup'] = Tools::getValue('localizator_drop_mansup');
		$this->fields_value['localizator_drop_other'] = Tools::getValue('localizator_drop_other');

		$helper = $this->initForm();
		$helper->submit_action = '';
		
		$helper->title = $this->displayName;
		
		$helper->fields_value = $this->fields_value;
		$this->_html .= $helper->generateForm($this->fields_form);
		return;
	}

	private function initForm()
	{
		$helper = new HelperForm();
		
		$helper->module = $this;
		$helper->name_controller = 'localizator';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->toolbar_scroll = true;
		$helper->tpl_vars['version'] = $this->version;
		$helper->tpl_vars['author'] = $this->author;
		$helper->tpl_vars['this_path'] = $this->_path;
		$helper->toolbar_btn = $this->initToolbar();
		
		return $helper;
	}

	public function hookdisplayBackOfficeHeader($params)
	{
		return '<script type="text/javascript" src="http://'.Tools::getHttpHost(false, true)._MODULE_DIR_.'/'.$this->name.'/js/admin.js"></script>';
	}








	public function lng($string, $id_lang=1, $table)
	{
		global $_MODULES, $_MODULE;

		$file = _PS_MODULE_DIR_.$this->name.'/translations/'.Language::getIsoById($id_lang).'.php';
		if (Tools::file_exists_cache($file) AND include_once($file))
			$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

		if (!is_array($_MODULES))
			return (str_replace('"', '&quot;', $string));

		$string2 = str_replace('\'', '\\\'', $string);
		$currentKey = '<{'.$this->name.'}'._THEME_NAME_.'>'.$table.'_'.md5($string2);
		$defaultKey = '<{'.$this->name.'}prestashop>'.$table.'_'.md5($string2);
		if (key_exists($currentKey, $_MODULES))
			$ret = stripslashes($_MODULES[$currentKey]);
		elseif (key_exists($defaultKey, $_MODULES))
			$ret = stripslashes($_MODULES[$defaultKey]);
		else
			$ret = $string;
		return str_replace('"', '&quot;', $ret);
	}


	protected function _ExecuteSQL($file, $id_lng=0)
	{
		if (!file_exists(dirname(__FILE__).'/sql/'.$file))
			return false;
		elseif (!$sql = file_get_contents(dirname(__FILE__).'/sql/'.$file))
			return false;
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE', 'ID_LNG'), array(_DB_PREFIX_, _MYSQL_ENGINE_, $id_lng), $sql);		
		$sql = preg_split("/;\s*[\r\n]+/", trim($sql));

		foreach ($sql as $query)
      Db::getInstance()->Execute(trim($query));
	}
	
	protected function _locTransGen($table, $fields=array('name'))
	{
      $f=implode(',',$fields);
		if($strs=Db::getInstance()->ExecuteS('SELECT '.$f.' FROM `'._DB_PREFIX_.''.$table.'` WHERE `id_lang`=1'))
	{
      $fh=fopen(dirname(__FILE__).'/views/templates/front/'.$table.'.tpl', 'w');
      foreach ($strs as $str)
        foreach ($fields as $field)
		if($str[$field])
          		fwrite($fh,'{l s=\''.$str[$field].'\' mod=\'localizator\'}');
      fclose($fh);
	}
	}
	
	protected function _locTransLate($table, $id_lng, $fields=array('name'))
	{		
		  $strs=Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.''.$table.'` WHERE `id_lang`=1');
      Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.''.$table.'` WHERE `id_lang`='.$id_lng);
      foreach ($strs as $str){
        $str['id_lang']=$id_lng;
          foreach ($fields as $field)
		if($str[$field])
            		$str[$field]=$this->lng($str[$field], $id_lng, $table);
        Db::getInstance()->autoExecute(_DB_PREFIX_.$table, $str, 'INSERT');
      }
	}


	protected function _postProcess()
	{
    require_once(dirname(__FILE__).'/fields.php');
		if (Tools::isSubmit('submitRegional'))
		{
      $id_lng=(int)Tools::GetValue('localizator_lang');
      $lng=Language::getIsoById($id_lng);
      //Очистка штатов
	self::_ExecuteSQL($lng.'_units.sql');
      if (Tools::GetValue('localizator_drop_states'))
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'state`');
      if (Tools::GetValue('localizator_import_states')){
        //Импорт регионов
        self::_ExecuteSQL($lng.'_states.sql');
        //Включение регионов у страны
        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'country` SET `contains_states`=1 WHERE `iso_code`=\''.$lng.'\'');
      }
      if (Tools::GetValue('localizator_import_states2')){
        //Импорт регионов 2
        self::_ExecuteSQL($lng.'_states2.sql');
      }
      //Очистка стран
      if (Tools::GetValue('localizator_drop_countries')){
        $id_country=Country::getByIso($lng);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'country` WHERE `id_country`<>'.$id_country);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'country_lang` WHERE `id_country`<>'.$id_country);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'country_shop` WHERE `id_country`<>'.$id_country);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'address_format` WHERE `id_country`<>'.$id_country);
        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'country_shop` WHERE `id_country`<>'.$id_country);
      }
      //Очистка налогов
      if (Tools::GetValue('localizator_drop_taxes')){
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_lang`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_rule`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_rules_group`');
        Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'tax_rules_group_shop`');
      }
      //Добавление налогов
      if (Tools::GetValue('import_taxes')){
        self::_ExecuteSQL($lng.'_taxes.sql', $id_lng);
      }
			$this->_html .= $this->displayConfirmation($this->l('Региональные настройки применены'));
		}
		
		elseif (Tools::isSubmit('submitPrepare'))
		{
      //Очистка продуктов, категорий, комбинаций, свойств, тегов, картинок, сцен
      if (Tools::GetValue('localizator_drop_products')){
        self::_ExecuteSQL('drop_products.sql');
        $langs=Language::getLanguages();
        foreach ($langs as $lang)
          Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."category_lang` (`id_category`, `id_shop`, `id_lang`, `name`, `description`, `link_rewrite`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(1, 1, ".$lang['id_lang'].", 'Root', '', 'root', '', '', ''),
(2, 1, ".$lang['id_lang'].", 'Home', '', 'home', '', '', '')");
				//Категории
				foreach (scandir(_PS_CAT_IMG_DIR_) as $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_CAT_IMG_DIR_.$d);
				//Продукты
				Image::deleteAllImages(_PS_PROD_IMG_DIR_);
				if (!file_exists(_PS_PROD_IMG_DIR_))
					mkdir(_PS_PROD_IMG_DIR_);
				Image::clearTmpDir();
      }
      //Очистка заказов, клиентов, корзин, гостей, сообщений
      if (Tools::GetValue('localizator_drop_orders')){
        self::_ExecuteSQL('drop_orders.sql');
      }
      //Очистка поставщиков, производителей
      if (Tools::GetValue('localizator_drop_mansup')){
        self::_ExecuteSQL('drop_mansup.sql');
				foreach (scandir(_PS_MANU_IMG_DIR_) as $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_MANU_IMG_DIR_.$d);
				foreach (scandir(_PS_SUPP_IMG_DIR_) as $d)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $d))
						unlink(_PS_SUPP_IMG_DIR_.$d);
				Image::clearTmpDir();
      }
      //Очистка соединений, статистики, поискового индекса, магазинов
      if (Tools::GetValue('localizator_drop_other')){
        self::_ExecuteSQL('drop_other.sql');
      }
			$this->_html .= $this->displayConfirmation($this->l('Очистка завершена'));
		}
		elseif (Tools::isSubmit('submitTransGen'))
		{
      foreach($locarray as $table=>$fields)
        self::_locTransGen($table,$fields);
      
			$this->_html .=$this->displayConfirmation($this->l('Файлы для перевода созданы'));
		}
		elseif (Tools::isSubmit('submitTransLate'))
		{
		$lang_en=new Language(1);
		if(!$lang_en->id)
		{
			$this->_html .=$this->displayError($this->l('Удален английский язык'));
			return;
		}
      $id_lng=(int)Tools::GetValue('localizator_lng');
      foreach($locarray as $table=>$fields)
        self::_locTransLate($table,$id_lng,$fields);
			$this->_html .= $this->displayConfirmation($this->l('Перевод завершен'));
		}
	}

}