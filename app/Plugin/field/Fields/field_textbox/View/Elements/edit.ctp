<?php
    $data['label'] = $data['required'] ? $data['label'] . ' *' : $data['label'];
    $data['label'] = $this->Layout->hookTags($data['label']);
    
    if (!isset($data['FieldData'])) {
        $options = array(
            'type' => 'text', 
            'label' => $data['label']
        );
        
        if ($data['required']) {
            $options['required'] = 'required';
        }

        echo $this->Form->input("FieldData.field_textbox.{$data['id']}.data", $options);
        echo $this->Form->hidden("FieldData.field_textbox.{$data['id']}.id", array('value' => null));
    } else {
        $options = array(
            'type' => 'text', 
            'label' => $data['label'], 
            'value' => @$data['FieldData']['data']
        );
        
        if ($data['required']) {
            $options['required'] = 'required';   
        }
        
        $data['FieldData'] = array_merge(array('id' => null, 'field_id' => null, 'foreignKey' => null, 'belongsTo' => null, 'data' => ''), $data['FieldData']);
        echo $this->Form->input("FieldData.field_textbox.{$data['id']}.data", $options);
        echo $this->Form->hidden("FieldData.field_textbox.{$data['id']}.id", array('value' => $data['FieldData']['id']));
    }

    if (!empty($data['description'])):
?>
    <em><?php echo $this->Layout->hookTags($data['description']); ?></em>
<?php endif; ?>