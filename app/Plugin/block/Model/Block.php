<?php
/**
 * Block Model
 *
 * PHP version 5
 *
 * @category Block.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class Block extends BlockAppModel {

    var $name       = 'Block';
    var $useTable   = 'blocks';
	var $primaryKey = 'id';
	
	var $hasOne = array(
		'BlockCustom' => array(
			'className' => 'Block.BlockCustom',
			'dependent' => true
		)
	);

	var $hasMany = array(
		'BlockRegion' => array(
			'className' => 'Block.BlockRegion',
			'dependent' => true
		)
	);

	var $belongsTo = array(
		'Menu' => array(
			'className' => 'Menu.Menu',
			'foreignKey' => 'delta',
            'conditions' => 'Block.module = "menu"',
			'dependent' => false
		)	
	);
    
    var $hasAndBelongsToMany = array(
        'Role' => array(
            'joinTable' => 'block_roles',
            'className' => 'User.Role',
            'foreignKey' => 'block_id',
            'associationForeignKey' => 'user_role_id',
            'unique' => true,
            'dependent' => false
        )
    );
    
    var $actsAs = array('Serialized' => array('locale', 'settings') );
    
    function beforeSave(){
        /* get New delta */
        if ( !isset($this->data['Block']['id']) ){ # new record
            if ( $this->data['Block']['module'] == 'menu' || isset($this->data['Block']['delta']) )
                return true;
            $max_delta = $this->find('first', array('conditions' => array('Block.module' => 'block'), 'fields' => array('delta'), 'order' => array('delta' => 'DESC')  ) );
            $max_delta = !empty($max_delta) ? $max_delta['Block']['delta'] + 1 : 1;
            $this->data['Block']['delta'] = $max_delta;
        }
        return true;
    }    
}