<?php
/**
 * Role Model
 *
 * PHP version 5
 *
 * @category User.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class Role extends UserAppModel {
    var $name = 'Role';
    var $useTable = "roles";
    var $order = array('Role.ordering' => 'ASC');
    
    var $validate = array(
        'name' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',  
                'message' => 'Role name can not be empty'
            ),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Role name already in use'
			)
        )
    );
    
    function beforeDelete(){
        $Aro = ClassRegistry::init('Aro');
        $Permission = ClassRegistry::init('Permission');
        $Permission->deleteAll( array('aro_id' => $this->id) );
        return $Aro->deleteAll( array('model' => 'User.Role', 'foreign_key' => $this->id) );
    }
    
    function afterSave(){
        $Aro = ClassRegistry::init('Aro');
        $data = array(
            'model' => 'User.Role',
            'foreign_key' => $this->id
        );
        $Aro->save($data);
    }
}