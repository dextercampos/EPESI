<?php
/**
 * Search class.
 * 
 * Provides for search functionality in a module. 
 * 
 * @author Arkadiusz Bisaga <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 0.9
 * @licence SPL
 * @package epesi-base-extra
 * @subpackage search
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

class Base_Search extends Module {
	private $lang;
	
	public function body() {
		global $base;
	
		
		$qs_keyword = isset($_REQUEST['quick_search'])?$_REQUEST['quick_search']:null;
		
				
		$this->lang = & $this->init_module('Base/Lang');
		
		$form = & $this->init_module('Libs/QuickForm',$this->lang->ht('Searching'));
		$theme =  & $this->pack_module('Base/Theme');
		
		$modules_with_search = array();
		foreach(ModuleManager::$modules as $name=>$obj) {
			if(method_exists($obj['name'].'Common', 'search'))
				$modules_with_search[$name] = $obj;
			if(method_exists($obj['name'], 'advanced_search'))
				if(!method_exists($obj['name'], 'advanced_search_access') 
					|| ModuleCommon::check_access($obj['name'],'advanced_search'))
						$modules_with_adv_search[$name] = $this->lang->ht(str_replace('_',': ',$name));
		}
		ksort($modules_with_search);

		$form->addElement('header', 'quick_search_header', $this->lang->t('Quick search'));
		$form->addElement('text', 'quick_search',  $this->lang->ht('Keyword'), array('id'=>'quick_search_text'));
		$form->addRule('quick_search', $this->lang->t('Field required'), 'required');
		$form->addElement('submit', 'quick_search_submit',  $this->lang->ht('Search'), array('class'=>'submit','onclick'=>'var elem=getElementById(\''.$form->getAttribute('name').'\').elements[\'advanced_search\'];if(elem)elem.value=0;'));

		if (!empty($modules_with_adv_search)) {
			$modules_with_adv_search[0] = '('.$this->lang->ht('Select module').')'; 
			ksort($modules_with_adv_search);
			$form->addElement('static', 'advanced_search_header', $this->lang->t('Advanced search'));
			$adv = true;
			$form->addElement('select', 'advanced_search', 'Module:', $modules_with_adv_search, array('onChange'=>$form->get_submit_form_js(false),'id'=>'advanced_search_select'));
			$advanced_search = $form->exportValue('advanced_search');
		}
		
		$defaults = array();

		$defaults['quick_search']=$qs_keyword;
		if (!$qs_keyword) {
			if (!isset($advanced_search)) $advanced_search = $this->get_module_variable('advanced_search');
			$defaults['advanced_search'] = $advanced_search;
		} else {
			$this->unset_module_variable();
		}
		
		$form->setDefaults($defaults);
		
		$form->assign_theme('form', $theme);
		$theme->assign('form_mini', 'no');
		$theme->display('Search');
		
		if (($form->validate() || $qs_keyword) && !$advanced_search) {
			if ($form->exportValue('submited')==1)
				$keyword = $form->exportValue('quick_search');
			elseif($_POST['qs_keyword'])
				$keyword = $_POST['qs_keyword'];
			elseif($qs_keyword)
				$keyword = $qs_keyword;
			if($keyword) {
				$links = array();
				$this->set_module_variable('quick_search',$keyword);
				foreach($modules_with_search as $k=>$v) {
					$results = call_user_func(array($v['name'].'Common','search'),$keyword);
					if (!empty($results))
						foreach ($results as $rk => $rv)
							$links[] = '<a '.$this->create_href(array_merge($rv,array('box_main_module'=>$k))).'>'.$rk.'</a>';
				}
				$qs_theme =  & $this->pack_module('Base/Theme');
				$qs_theme->assign('header', $this->lang->t('Search results'));
				$qs_theme->assign('links', $links);
				$qs_theme->display('Results');
				if($adv)
					eval_js('var elem=document.getElementById(\'advanced_search_select\');for(i=0; i<elem.length; i++) if(elem.options[i].value==\'0\') {elem.options[i].selected=true;break;};');
				return;
			}
		}
		if ($advanced_search) {
			$qs_theme =  & $this->pack_module('Base/Theme');
			$qs_theme->assign('header', $this->lang->t('Advanced search'));
			$qs_theme->display('Results');
			$this->pack_module($advanced_search,null,'advanced_search');
			$this->set_module_variable('advanced_search',$advanced_search);
		}
		
	}
	
/*	
	public static function search_menu(){
		return '<form action="javascript:load_page(\'href=Base_Search&qs_keyword=\'+document.getElementById(\'qs_keyword\').value);" method=POST><input type=text name=qs_keyword /><input type=submit value=Search /></form>';
	}
	*/
	public function mini() {
		$this->lang = & $this->init_module('Base/Lang');
		$form = & $this->init_module('Libs/QuickForm',$this->lang->ht('Searching'));
		
		$form->addElement('text', 'quick_search', $this->lang->t('Quick search'));
		$form->addElement('submit', 'quick_search_submit', $this->lang->ht('Search'), array('class'=>'mini_submit'));
		
		$theme =  & $this->pack_module('Base/Theme');
		$form->assign_theme('form', $theme);
		$theme->assign('form_mini', 'yes');
		$theme->display('Search');
		
		if($form->validate()) {
			$search = $form->exportValues();
			location(array('box_main_module'=>'Base_Search','quick_search'=>$search['quick_search'],'advanced_search'=>0));
		}
	}
}

?>
