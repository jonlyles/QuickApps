<?php 
    echo $this->Form->input("Field.settings.text_format", 
        array(
            'type' => 'select', 
            'options' => array('plain' => __d('field_textarea', 'Filtered'), 'full' => __d('field_textarea', 'Full HTML') ), 
            'empty' => false,
            'label' => __d('field_textarea', 'Text Format')
        )
    );