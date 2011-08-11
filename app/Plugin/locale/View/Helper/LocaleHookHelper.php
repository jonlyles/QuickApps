<?php
/**
 * Locale View Hooks
 *
 * PHP version 5
 *
 * @category Locale.View/Helper
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class LocaleHookHelper extends AppHelper {
	
    //Toolbar Block
    function beforeLayout($layoutFile){
        $show_on = (
            Router::getParam('admin') &&
            $this->request->params['plugin'] == 'locale'
        );
        $this->_View->Layout->blockPush( array('body' => $this->_View->element('toolbar') . '<!-- LocaleHookHelper -->' ), 'toolbar', $show_on);
        return true;
    }
    
    /* Block */
	function locale_language_switcher($block){
		return array(
            'title' => false,
			'body' => $this->_View->element('locale_language_switcher', array('block' => $block), array('plugin' => 'locale') )
		);
	}
    
    function locale($data){
        if ( !isset($data['lang']) || $data['lang'] != Configure::read('Variable.language') )
            return '';
        return __t($data['text']);
    }
    
    function locale_language_switcher_settings($data){
        return $this->_View->element('locale_language_switcher_settings', array('block' => $data), array('plugin' => 'Locale') );
    }
}