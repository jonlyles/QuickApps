<?php
class FieldListHookBehavior extends ModelBehavior {

    function field_list_beforeSave($info) {
        return true;
    }

    function field_list_afterSave($info) {
        if (empty($info)) {
            return true;
        }
        
        $info['id'] =  empty($info['id']) || !isset($info['id']) ? null : $info['id'];
        $data['FieldData'] = array(
            'id' => $info['id'], # update or create
            'field_id' => $info['field_id'],
            'data' => implode('|', (array)$info['data']),
            'belongsTo' => $info['model_name'],
            'foreignKey' => $info['model_id']
        );
        
        ClassRegistry::init('Field.FieldData')->save($data);
        
        return true;
    }

    function field_list_afterFind($Model) {
        return true;
    }

    function field_list_beforeValidate($info) {
        $FieldInstance = ClassRegistry::init('Field.Field')->findById($info['field_id']);
        
        if ($FieldInstance['Field']['required'] == 1) {
            $info['data'] = is_array($info['data']) ? implode('', $info['data']) : $info['data'];
            $filtered = strip_tags($info['data']);
            
            if (empty($filtered)) {
                ClassRegistry::init('Field.FieldData')->invalidate(
                    "field_list.{$info['field_id']}.data",
                    __d('field_list', 'You must select at least on option')
                );
                
                return false;
            }
        }
        
        return true;    
    }
    
    function field_list_beforeDelete($Model) {
        return true;
    }
    
    function field_list_afterDelete($info) {
        ClassRegistry::init('Field.FieldData')->deleteAll(
            array(
                'FieldData.belongsTo' => $info['model_name'],
                'FieldData.field_id' => $info['field_id'],
                'FieldData.foreignKey' => $info['model_id']
            )
        );
        
        return true;
    }
    
    function field_list_deleteInstance($field_id) {
        ClassRegistry::init('Field.FieldData')->deleteAll(
            array(
                'FieldData.field_id' => $field_id
            )
        );
    }
}