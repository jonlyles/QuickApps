<?php
class FieldTextareaHookBehavior extends ModelBehavior {

    function field_textarea_beforeSave($info) {
        $info['id'] =  empty($info['id']) || !isset($info['id']) ? null : $info['id'];
        $data['FieldData'] = array(
            'id' => $info['id'], # update or create
            'field_id' => $info['field_id'],
            'data' => $info['data'],
            'belongsTo' => $info['model_name'],
            'foreignKey' => $info['model_id']
        );
        ClassRegistry::init('Field.FieldData')->save($data);
        return true;
    }

    function field_textarea_afterSave($info) {
        if (empty($info) ) return true;
        $info['id'] =  empty($info['id']) || !isset($info['id']) ? null : $info['id'];
        $data['FieldData'] = array(
            'id' => $info['id'], # update or create
            'field_id' => $info['field_id'],
            'data' => $info['data'],
            'belongsTo' => $info['model_name'],
            'foreignKey' => $info['model_id']
        );
        ClassRegistry::init('Field.FieldData')->save($data);
        return true;
    }

    function field_textarea_afterFind(&$results) {
        return true;
    }

    function field_textarea_beforeValidate($info) {
        $FieldInstance = ClassRegistry::init('Field.Field')->findById($info['field_id']);
        if ($FieldInstance['Field']['required'] == 1) {
            $filtered = html_entity_decode(strip_tags($info['data']));
            if (empty($filtered)) {
                ClassRegistry::init('Field.FieldData')->invalidate(
                    "field_textarea.{$info['field_id']}.data",
                    __d('field_textarea', 'This field can not be empty.')
                );
                return false;
            }
        }
        return true;
    }

    function field_textarea_beforeDelete($info) {
        return true;
    }

    function field_textarea_afterDelete($info) {
        ClassRegistry::init('Field.FieldData')->deleteAll(
            array(
                'FieldData.belongsTo' => $info['model_name'],
                'FieldData.field_id' => $info['field_id'],
                'FieldData.foreignKey' => $info['model_id']
            )
        );    
        return true;
    }
    
    function field_textarea_deleteInstance($field_id) {
        ClassRegistry::init('Field.FieldData')->deleteAll(
            array(
                'FieldData.field_id' => $field_id
            )
        );
    }
}