<?php
/**
 * BlockCustom Model
 *
 * PHP version 5
 *
 * @category Block.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class BlockCustom extends BlockAppModel {
    var $name = 'BlockCustom';
    var $useTable = "block_custom";
    var $primaryKey = "block_id";
    
	var $validate = array(
        'description' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Invalid description'),
        'body' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Invalid block body'),
	);
}