<?php
/**
 * Fancy statusbar.
 * 
 * @author Paul Bukowski <pbukowski@telaxus.com>
 * @copyright Copyright &copy; 2006, Telaxus LLC
 * @version 1.0
 * @licence SPL
 * @package epesi-base-extra
 */
defined("_VALID_ACCESS") || die('Direct access forbidden');

/**
 * @package epesi-base-extra
 * @subpackage statusbar
 */
class Base_StatusBar extends Module {
	
	public function body() {
		$this->load_js();
		$theme = & $this->pack_module("Base/Theme");
		$theme->assign('statusbar_id','statusbar');
		$theme->assign('text_id','statusbar_text');
		$theme->display();
		on_exit(array($this, 'messages'));
	}
	
	public function messages() {
		eval_js("wait_while_null('statusbar_message','statusbar_message(\'".addslashes(escapeJS(implode('<br>',Base_StatusBarCommon::$messages)))."\')')");
	}
	
	private function load_js() {
		eval_js_once('var statusbar_message_t=\'\';' .
				'statusbar_message=function(text){statusbar_message_t=text;};' .
				'statusbar_fade=function(){wait_while_null(\'document.getElementById(\\\'statusbar\\\')\',\'Effect.Fade(\\\'statusbar\\\', {duration:1.0});$(\\\'overlay\\\').style.display=\\\'none\\\';\');};' .				
				'updateSajaIndicatorFunction=function(){' .
					'saja.indicator=\'statusbar_text\';' .
					'document.getElementById(\'sajaStatus\').style.visibility=\'hidden\';' .
					'statbar = document.getElementById(\'statusbar\');' .
					'statbar.style.display=\'none\';' .
					'statbar.onclick = statusbar_fade;' .
					'overlay = document.createElement(\'div\');' .
					'overlay.style.display = \'none\';' .
					'overlay.id = \'overlay\';' .
					'if(navigator.appName.indexOf(\'Explorer\') != -1 ) {' .
						'overlay.style.position = \'absolute\';' .
						'overlay.className = \'statusbar_overlay_ie\';' .
						'overlay.style.height = (document.documentElement.clientHeight < document.body.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight) + \'px\';' .
					'} else {' .
						'$(\'overlay\').style.position = \'fixed\';' .
						'overlay.className = \'statusbar_overlay\';' .
					'};' .
					'saja.updateIndicator=function(){' .
						'if(saja.procOn){' .
							'Effect.Appear(\'statusbar\', {duration:0.0});' .
							//'Effect.Appear(\'overlay\', {duration:0.0});' .
							'$(\'overlay\').style.display=\'block\';' .
						'}else{' .
							'if(statusbar_message_t!=\'\') {' .
								't=document.getElementById(\'statusbar_text\');' .
								'if(t)t.innerHTML=statusbar_message_t;' .
								'statusbar_message(\'\');' .
								'setTimeout(\'statusbar_fade()\',3000);' .
							'}else{' .
								'statusbar_fade();' .
							'};' .
						'};' .
					'};' .
				'};' .
				'wait_while_null(\'Effect\',\'updateSajaIndicatorFunction()\')');
	}
}
?>
