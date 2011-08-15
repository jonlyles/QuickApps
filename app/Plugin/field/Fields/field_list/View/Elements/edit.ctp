<?php 
    $data['label'] = $data['required'] ? $data['label'] . ' *' : $data['label'];
    $data['label'] = $this->Layout->hookTags($data['label']);
    $data['FieldData'] = !isset($data['FieldData']) ? array() : $data['FieldData'];
    $data['FieldData'] = array_merge(array('id' => null, 'field_id' => null, 'foreignKey' => null, 'belongsTo' => null, 'data' => ''), $data['FieldData']);
    $_options = $options = array();
    
    if (!empty($data['settings']['options'])) {
        $_options = explode("\n", $data['settings']['options']);
        
        foreach ($_options as $option) {
            $option = explode("|",$option);
            $value = $option[0];
            $label = isset($option[1]) ? $option[1] : $option[0];
            $options[$value] = $label;
        }
    }

    $selected = explode('|', (string)$data['FieldData']['data']);
    $data['settings']['type'] = empty($data['settings']['type']) ? 'checkbox' : $data['settings']['type'];
    
    if ($data['settings']['type'] === 'checkbox') {
        echo $this->Form->input("FieldData.field_list.{$data['id']}.data", array('type' => 'select', 'label' => $data['label'], 'multiple' => 'checkbox', 'options' => $options, 'value' => $selected));
    } else {
        echo $this->Form->input("FieldData.field_list.{$data['id']}.data", array('type' => 'radio', 'separator' => '<br/>', 'options' => $options, 'legend' => $data['label'], 'value' => @$selected[0]));
    }
    
    echo $this->Form->hidden("FieldData.field_list.{$data['id']}.id", array('value' => $data['FieldData']['id']));
?>

<?php if (!empty($data['description'])): ?>
    <em><?php echo $this->Layout->hookTags($data['description']); ?></em>
<?php endif; ?>