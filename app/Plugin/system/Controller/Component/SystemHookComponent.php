<?php
/**
 * System Controller Hooks
 *
 * PHP version 5
 *
 * @category System.Controller/Component
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class SystemHookComponent extends Component {
	
	var $Controller = null;
	var $components = array('Hook');

	function initialize(&$Controller){
		$this->Controller = $Controller;
	}
}