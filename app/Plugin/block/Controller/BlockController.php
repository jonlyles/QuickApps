<?php
/**
 * Block Controller
 *
 * PHP version 5
 *
 * @category Block.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class BlockController extends BlockAppController {

	var $name = 'Block';
	var $uses = array('Block.Block');
	
	function admin_index(){
        $this->redirect('/admin/block/manage');
	}
}