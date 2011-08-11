<?php
/**
 * Variable Model
 *
 * PHP version 5
 *
 * @category System.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class Variable extends SystemAppModel {
    var $name       = 'Variable';
    var $useTable   = "variables";
	var $primaryKey = 'name';
    var $actsAs     = array('Serialized' => array('value'));
    
    function save($data = null, $validate = true, $fieldList = array()){
        if( # saving data array of type: array('var_name' => 'value')
            !isset($data['Variable']['name']) && 
            !isset($data['Variable']['value']) && 
            !empty($data['Variable'])
        ){ 
            $rows = array();
            foreach($data['Variable'] as $name => $value){
                $rows['Variable'][] = array(
                    'name' => $name,
                    'value' => $value
                );
            }
            return $this->saveAll($rows['Variable'], array('validate' => $validate) );
        } else {
            return parent::save($data, $validate, $fieldList);
        }
    }
   
	function afterSave(){
        Cache::delete('Variable');
		$this->writeCache();
		return true;
	}    
    
    function writeCache(){
        $variables = $this->find('all', array('fields' => array('name', 'value') ) );
        foreach ($variables as $v)
            Configure::write('Variable.' . $v['Variable']['name'] , $v['Variable']['value']);
        Cache::write('Variable', Configure::read('Variable') );
    }
}