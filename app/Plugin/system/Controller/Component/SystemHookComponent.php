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
	public $Controller = null;
	public $components = array('Hook');

	public function initialize(&$Controller) {
		$this->Controller = $Controller;
	}
}