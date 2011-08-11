<?php
class FieldTextboxHookBehavior extends ModelBehavior {

    function field_textbox_beforeSave($info){
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

    function field_textbox_afterSave($info){
        if( empty($info) ) return true;
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

    function field_textbox_afterFind(&$results){
        return true;
    }

    function field_textbox_beforeValidate(&$info){
        $FieldInstance = ClassRegistry::init('Field.Field')->findById($info['field_id']);
        $errMsg = array();
        if ( isset($FieldInstance['Field']['settings']['max_len']) && 
             !empty($FieldInstance['Field']['settings']['max_len']) &&
             $FieldInstance['Field']['settings']['max_len'] > 0 && 
            strlen(trim($info['data'])) > $FieldInstance['Field']['settings']['max_len']
        ){
            $errMsg[] = __d('field_textbox', 'Max. %s characters length', $FieldInstance['Field']['settings']['max_len']);
        }

        if ( $FieldInstance['Field']['required'] == 1 ){
            $filtered = strip_tags($info['data']);
            if ( empty($filtered) )
                $errMsg[] = __d('field_textbox', 'Field required');
        }
        
        if ( isset($FieldInstance['Field']['settings']['validation_rule']) && !empty($FieldInstance['Field']['settings']['validation_rule']) ){
            if ( !preg_match($FieldInstance['Field']['settings']['validation_rule'], $info['data']) )
                $errMsg[] = __d('field_textbox', 'Invalid field');
        }        
        
        if ( !empty($errMsg) ) {
            ClassRegistry::init('Field.FieldData')->invalidate(
                "field_textbox.{$info['field_id']}.data",
                implode(", ", $errMsg)
            );
            return false;
        }
        return true;  
    }

    function field_textbox_beforeDelete($info) {
        return true;
    }

    function field_textbox_afterDelete($info){
        ClassRegistry::init('Field.FieldData')->deleteAll(
            array(
                'FieldData.belongsTo' => $info['model_name'],
                'FieldData.field_id' => $info['field_id'],
                'FieldData.foreignKey' => $info['model_id']
            )
        );
        return true;
    }
    
    function field_textbox_deleteInstance($field_id){
        ClassRegistry::init('Field.FieldData')->deleteAll(
            array(
                'FieldData.field_id' => $field_id
            )
        );
    }
}