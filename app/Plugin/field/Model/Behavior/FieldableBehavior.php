<?php 
/**
 * Fieldable Behavior
 *
 * PHP version 5
 *
 * @package  QuickApps.Plugin.Field.Model.Behavior
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class FieldableBehavior extends ModelBehavior {
    public $settings = array('belongsTo' => null);
    public $fieldData = null;
/**
 * Initiate Fieldable behavior
 *
 * @param object $Model instance of model
 * @param array $config array of configuration settings.
 * @return void
 * @access public
 */
	public function setup($Model, $settings = array()) {
        $this->settings['belongsTo'] = $Model->alias;
		$this->settings = array_merge($this->settings, $settings);
        $this->Field = ClassRegistry::init('Field.Field');
        $this->Field->FieldData = ClassRegistry::init('Field.FieldData');
        
        $Model->bindModel(
            array(
                'hasMany' =>  array(
                    'Field' => array(
                        'className' => 'Field.Field',
                        'foreignKey' => false,
                        'order' => array('Field.ordering' => 'ASC'),
                        'conditions' => array('Field.belongsTo' => "{$Model->name}")
                    )
                )
            )
        );
        
        return true;
    }
    
    public function beforeFind(&$Model) {
        if (isset($Model->fieldsNoFetch) && $Model->fieldsNoFetch) {
            $Model->unbindModel(
                array(
                    'hasMany' => array('Field')
                )
            );
        }
        return true;
    }
    
    public function beforeSave(&$Model) {
        $r = array();
        if (isset($Model->data['FieldData']) && $Model->id) { # save only id already exists
            foreach ($Model->data['FieldData'] as $field_module => $fields) {
                foreach ($fields as $field_id => $info) {
                    $info['field_id'] = $field_id;
                    $info['model_name'] = $Model->name;
                    $info['model_id'] = $Model->id;
                    $info['Model'] =& $Model;
                    $r[] = $Model->hook("{$field_module}_beforeSave", $info, array('collectReturn' => false));
                }
            }
        } elseif (isset($Model->data['FieldData'])) {
            $this->fieldData = $Model->data['FieldData']; # hold data for new nodes 
        }
        return !in_array(false, $r, true);
    }
    
    public function afterSave(&$Model, $created) {
        if (!empty($this->fieldData) && $created) { # procced to save field data for new node
            foreach ($this->fieldData as $field_module => $fields) {
                foreach ($fields as $field_id => $info) {
                    $info['field_id'] = $field_id;
                    $info['model_name'] = $Model->name;
                    $info['model_id'] = $Model->id;
                    $info['Model'] =& $Model;
                    $Model->hook("{$field_module}_afterSave", $info);
                }
            }
        }
        return true;
    }
    
    public function beforeDelete(&$Model) {
       return $this->__beforeAfterDelete(&$Model, 'before');
    }
    
    public function afterDelete(&$Model) {
        return $this->__beforeAfterDelete(&$Model, 'after');
    }
    
    private function __beforeAfterDelete(&$Model, $type = 'before') {
        $model_id = $Model->id ? $Model->id : $Model->tmpId;
        $fields = ClassRegistry::init('Field.Field')->find('all',
            array(
                'conditions' => array(
                    'belongsTo' => "{$Model->name}"
                )
            )
        );
        
        $r = array();
        
        foreach ($fields as $field) {
            if ($type == 'before') {
                $Model->tmpId = $Model->id;
            }
            
            $info['field_id'] = $field['Field']['id'];
            $info['model_name'] = $Model->name;
            $info['model_id'] = $model_id;
            $info['Model'] =& $Model;
            $r[] = $Model->hook("{$field['Field']['field_module']}_{$type}Delete", $info);
        }
        return !in_array(false, $r, true);
    }

    public function prepareFields() {
        $results =  $this->Field->find('all', 
            array(
                'conditions' => array(
                    'Field.belongsTo' => $this->settings['belongsTo']
                ),
                'order' => array('Field.ordering' => 'ASC')
            )
        );
        
        return $results = Set::extract('/Field/.', $results);
    }
    
    public function beforeValidate(&$Model) {
        if (!isset($Model->data['FieldData'])) {
            return true;
        }
        
        $DummyModel = ClassRegistry::init('Dummy');
        $r = array();
        
        foreach ($Model->data['FieldData'] as $field_module => $fields) {
            foreach ($fields as $field_id => $info) {
                $info['field_id'] = $field_id;
                $info['model_name'] = $Model->name;
                $info['model_id'] = $Model->id;
                $info['Model'] =& $Model;
                $r[] = $DummyModel->hook("{$field_module}_beforeValidate", $info);
            }
        }
        return !in_array(false, $r, true);
    }
    
    public function unbindFields(&$Model) {
        $Model->fieldsNoFetch = true;
    }
    
    public function bindFields(&$Model) {
        $Model->fieldsNoFetch = false;
    }
    
    public function afterFind(&$Model, $results, $primary) {
        if (empty($results) || !$primary || (isset($Model->fieldsNoFetch) && $Model->fieldsNoFetch)) {
            return $results;
        }
        
        # fetch model instance Fields
        foreach ($results as &$result) {
            if (!isset($result[$Model->alias])) {
                continue;
            }
            
            $result['Field'] = array();
            $modelFields = $this->Field->find('all', 
                array(
                    'order' => array('Field.ordering' => 'ASC'), 
                    'conditions' => array(
                        'Field.belongsTo' => $this->settings['belongsTo']
                    )
                )
            );
            
            $result['Field'] = Set::extract('/Field/.', $modelFields);
            
            foreach ($result['Field'] as $key => &$field) {
                /*
                 * Attempt to find basic data (FieldData).
                 * Remember: fields can define their own storage tables (or 'storage system' in general), 
                 * but FieldData is the basic common
                 */
                $field['FieldData'] = $this->Field->FieldData->find('first', 
                    array(
                        'conditions' => array(
                            'FieldData.field_id' => $field['id'], 
                            'FieldData.foreignKey' => $result[$Model->alias][$Model->primaryKey]
                        )
                    )
                );
                
                $field['FieldData'] = Set::extract('/FieldData/.', $field['FieldData']);
                $field['FieldData'] = isset($field['FieldData'][0]) ? $field['FieldData'][0] : $field['FieldData'];
                $Model->hook("{$field['name']}_afterFind", $result['Field'][$key]);
            }
        }
        return $results;
    }
}