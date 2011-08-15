<?php 
    $data['label'] = $data['required'] ? $data['label'] . ' *' : $data['label'];
    $data['FieldData'] = !isset($data['FieldData']) ? array() : $data['FieldData'];
    $data['FieldData'] = array_merge(array('id' => null, 'field_id' => null, 'foreignKey' => null, 'belongsTo' => null, 'data' => ''), $data['FieldData']); 
    $options = array();
    
    if (isset($data['settings']['vocabulary']) && $data['settings']['vocabulary'] > 0) {
        $options = ClassRegistry::init('Taxonomy.Term')->generateTreeList(
            array(
                'Term.vocabulary_id' => $data['settings']['vocabulary']
            ), null, null, '&nbsp;&nbsp;&nbsp;|-&nbsp;'
        );
    }

    $selected = explode('|', (string)$data['FieldData']['data']);
    $data['settings']['type'] = empty($data['settings']['type']) ? 'checkbox' : $data['settings']['type'];
    
    echo $this->Form->input("FieldData.field_terms.{$data['id']}.data", array('escape' => false, 'type' => 'select', 'label' => $data['label'], 'multiple' => 'checkbox', 'options' => $options, 'value' => $selected));
    echo $this->Form->hidden("FieldData.field_terms.{$data['id']}.id", array('value' => $data['FieldData']['id']));
?>