<?php 
/**
 * Menu Link Model
 *
 * PHP version 5
 *
 * @category Menu.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class MenuLink extends MenuAppModel {
    var $name = 'MenuLink';
    var $useTable = "menu_links";
	var $primaryKey = 'id';
    var $displayField = 'link_title';
	var $order = array('MenuLink.lft' => 'ASC');
	
	var $actsAs = array(
		'Serialized' => array('options'),
		'Tree'
	);
    
	var $validate = array(
        'link_title' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Menu link title can not be empty'),
        'router_path' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'validatePath', 'message' => 'Invalid link path')
	);
    
    function beforeSave(){
        if ( isset($this->data['MenuLink']['router_path']) && $this->data['MenuLink']['router_path'] !== '/' ) # fix: paths must never end with '/'
            $this->data['MenuLink']['router_path'] = preg_replace('/\/{2,}/', '',  "{$this->data['MenuLink']['router_path']}//");
        
        if ( isset($this->data['MenuLink']['id']) && $this->data['MenuLink']['status'] == 0 ){
            $this->Behaviors->detach('Tree');
            $root = $this->findById($this->data['MenuLink']['id']);
            $this->Behaviors->attach('Tree', 
                array(
                    'parent' => 'parent_id', 
                    'left' => 'lft', 
                    'right' => 'rght', 
                    'scope' => "MenuLink.menu_id = '{$root['MenuLink']['menu_id']}'"
                )
            );
            $children = $this->children($this->data['MenuLink']['id']);
            $children = Set::extract('/MenuLink/id', $children);
            $this->Behaviors->detach('Tree');
            $this->updateAll(
                array('MenuLink.status' => 0),
                array('MenuLink.id' => $children)
            );
        }
        
        return true;
    }
    
    function validatePath($check){
        $value = array_values($check);
        $value = $value[0];
        
        if ( empty($value) )
            return false;

        if ( $value[0] !== '/' ){
            $this->data['MenuLink']['link_path'] = $value;
            $this->data['MenuLink']['router_path'] = null;
            return Validation::url($value);
        }
        
        $validChars = '([' . preg_quote('!"$&\'()*+,-.@_:;=~') . '\/0-9a-z\p{L}\p{N}]*)';
        return preg_match('/^' . $validChars . '$/iu', $value);
    }
}