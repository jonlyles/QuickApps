<?php 
    echo $this->Form->input("Field.settings.vocabulary", 
        array(
            'type' => 'select', 
            'options' => ClassRegistry::init('Taxonomy.Vocabulary')->find('list'), 
            'empty' => false,
            'label' => __d('field_list', __d('field_terms', 'Vocabulary *'))
        )
    );
?>
<em><?php echo __d('field_terms', 'The vocabulary which supplies the options for this field.'); ?></em>
