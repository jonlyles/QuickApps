<?php
/**
 * User View Hooks
 *
 * PHP version 5
 *
 * @category User.View/Helper
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class UserHookHelper extends AppHelper {
	
    // Toolbar Block
    function beforeLayout($layoutFile){
        $show_on = (
            Router::getParam('admin') &&
            $this->request->params['plugin'] == 'user' &&
            $this->request->params['action'] == 'admin_index'
        );
        $this->_View->Layout->blockPush( array('body' => $this->_View->element('toolbar') . '<!-- NodeHookHelper -->' ), 'toolbar', $show_on);
        return true;
    }
    
    // Block, last registered users
    function user_new($block){
        return array(
            'title' => __d('user', "Who's New"),
			'body' => $this->_View->element('user_new_block', array('block' => $block), array('plugin' => 'User') )
		);
    }
    
    function user_new_settings(){
        return $this->_View->element('user_new_block_settings', array(), array('plugin' => 'User') );
    }
    
    // Block
    function user_login(){
        return array(
            'title' => __d('user', 'Login'),
			'body' => $this->_View->element('user_login_block', array(), array('plugin' => 'User') )
		);
    }
}