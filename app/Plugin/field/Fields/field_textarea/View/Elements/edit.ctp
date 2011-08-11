<?php 
    $data['label'] = $data['required'] ? $data['label'] . ' *' : $data['label']; 
    $data['label'] = $this->Layout->hookTags($data['label']); 
    $class = isset($data['settings']['text_format']) ? $data['settings']['text_format'] : 'plain'; 
    if ( !isset($data['FieldData']) ):
        $options = array('type' => 'textarea', 'class' => $class, 'label' => $data['label']);
        if ($data['required']) $options['required'] = 'required';
        echo $this->Form->input("FieldData.field_textarea.{$data['id']}.data", $options);
        echo $this->Form->hidden("FieldData.field_textarea.{$data['id']}.id", array('value' => null) );
    else: 
        $data['FieldData'] = array_merge(array('id' => null, 'field_id' => null, 'foreignKey' => null, 'belongsTo' => null, 'data' => ''), $data['FieldData']);
        $options = array('type' => 'textarea', 'class' => $class, 'label' => $data['label'], 'value' => $data['FieldData']['data']);
        if ($data['required']) $options['required'] = 'required';
        echo $this->Form->input("FieldData.field_textarea.{$data['id']}.data", $options );
        echo $this->Form->hidden("FieldData.field_textarea.{$data['id']}.id", array('value' => $data['FieldData']['id']) );
    endif;
    
    if ( !empty($data['description']) ):
?>
    <em><?php echo $this->Layout->hookTags($data['description']); ?></em>
<?php endif; ?>