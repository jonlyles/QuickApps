<?php 
/**
 * Node Model Hooks
 *
 * PHP version 5
 *
 * @package  QuickApps.Plugin.Node.Model.Behavior
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class NodeHookBehavior extends ModelBehavior {
    public $fieldData = null;

/*************************/
/* node type: Basic Page */
/*************************/
    public function node_content_beforeValidate($Model) {
        if (!isset($Model->data['FieldData'])) {
            return true;
        }
        
        $r = array();
        
        foreach ($Model->data['FieldData'] as $field_module => $fields) {
            foreach ($fields as $field_id => $info) {
                $info['field_id'] = $field_id;
                $info['model_name'] = $Model->name;
                $info['model_id'] = $Model->id;
                $info['Model'] =& $Model; 
                $r[] = $Model->hook("{$field_module}_beforeValidate", $info);
            }
        }
        
        $r = array_unique($r);
        
        return ( count($r) > 1 ? false : $r[0] );
    }

    public function node_content_beforeSave($Model) {
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

    public function node_content_beforeDelete($Model) {
        if (empty($Model->data)) {
            $Model->data = $Model->findById($Model->id);
        }

        $fields = ClassRegistry::init('Field.Field')->find('all',
            array(
                'conditions' => array(
                    'belongsTo' => "NodeType-{$Model->data['Node']['node_type_id']}"
                )
            )
        );
        
        $r = array();
        
        foreach ($fields as $field) {
            # hold for afterDelete
            $Model->tmpId = $Model->id;
            $Model->TMP_node_type_id = $Model->data['Node']['node_type_id'];
            $info['field_id'] = $field['Field']['id'];
            $info['model_name'] = $Model->name;
            $info['model_id'] = $Model->id;
            $info['Model'] =& $Model;
            $r[] = $Model->hook("{$field['Field']['field_module']}_beforeDelete", $info, array('collectReturn' => false));
        }
        
        return !in_array(false, $r, true);
    }

    public function node_content_afterFind($results) {
        if (!isset($results['Field'])) {
            return true;
        }
        
        $DummyModel = ClassRegistry::init('Dummy');
        
        foreach ($results['Field'] as $key => $field) {
            $DummyModel->hook("{$field['name']}_afterFind", $results['Field'][$key], array('collectReturn' => false));
        }
        
        return true;
    }

    public function node_content_afterSave($Model) {
        if (!empty($this->fieldData)) { # procced to save field data for new node
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

    public function node_content_afterDelete($Model) {
        $fields = ClassRegistry::init('Field.Field')->find('all',
            array(
                'conditions' => array(
                    'belongsTo' => "NodeType-{$Model->TMP_node_type_id}"
                )
            )
        );

        foreach ($fields as $field) {
            $info['field_id'] = $field['Field']['id'];
            $info['model_name'] = $Model->name;
            $info['model_id'] = $Model->tmpId;
            $info['Model'] =& $Model;
            $Model->hook("{$field['Field']['field_module']}_afterDelete", $info);
        }
    
        return true;
    }

/***************************/
/* node type: Custom types */
/***************************/
    public function node_beforeValidate(&$Model) {
        return $this->node_content_beforeValidate(&$Model);
    }

    public function node_beforeSave(&$Model) {
        return $this->node_content_beforeSave(&$Model);
    }

    public function node_beforeDelete(&$Model) {
        return $this->node_content_beforeDelete(&$Model);
    }

    public function node_afterFind(&$results) {
        return $this->node_content_afterFind(&$results);
    }

    public function node_afterSave(&$Model) {
        return $this->node_content_afterSave(&$Model);
    }

    public function node_afterDelete($Model) {
        return $this->node_content_afterDelete(&$Model);
    }
}