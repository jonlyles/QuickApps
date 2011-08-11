<?php
/**
 * Menu Controller
 *
 * PHP version 5
 *
 * @category Menu.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class MenuController extends MenuAppController {
	var $name = 'Menu';
	var $uses = array();
	
	function admin_index(){
        $this->redirect('/admin/menu/manage');
	}
}