<?php
/**
 * Field Handler Controller
 *
 * PHP version 5
 *
 * @category Field.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class HandlerController extends FieldAppController {
	var $name = 'Handler';
	var $uses = array('Field.Field');
    
    function admin_delete($id){
        $field = $this->Field->findById($id) or $this->redirect($this->referer());
        $this->Field->hook("{$field['Field']['field_module']}_deleteInstance", $id, array('alter' => false));
        $this->Field->delete($id);
        $this->redirect($this->referer());
    }
    
    function admin_move($id, $dir, $view_mode = false){
        $this->Field->move($id, $dir, $view_mode);
        $this->redirect($this->referer());
    }
}