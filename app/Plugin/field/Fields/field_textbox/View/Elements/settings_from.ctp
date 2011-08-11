<!-- Textbox Settings Form -->
<?php 
    echo $this->Form->input("Field.settings.max_len", 
        array(
            'type' => 'text', 
            'label' => __d('field_textbox', 'Max lenght')
        )
    );
    
    echo $this->Form->input("Field.settings.validation_rule", 
        array(
            'type' => 'text', 
            'label' => __d('field_textbox', 'Validation rule')
        )
    );
?>
<em><?php echo __t('Enter your custom regular expression. i.e.: "/^[a-z0-9]{3,}$/i" (Only letters and integers, min 3 characters)'); ?></em>