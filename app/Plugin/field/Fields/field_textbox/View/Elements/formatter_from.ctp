<!-- Textbox Formatter Form -->
<?php 
    echo $this->Form->input("Field.settings.display.{$view_mode}.type", 
        array(
            'type' => 'select', 
            'options' => array('full' => __t('Full'), 'plain' => __t('Plain'), 'trimmed' => __t('Trimmed'), 'hidden' => __t('Hidden')), 
            'empty' => false,
            'onChange' => 'alert("load form");'
        )
    );