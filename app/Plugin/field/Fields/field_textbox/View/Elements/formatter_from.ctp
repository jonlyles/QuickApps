<!-- Textbox Formatter Form -->
<?php 
    $actualType = @$this->data['Field']['settings']['display'][$view_mode]['type'];
    echo $this->Form->input("Field.settings.display.{$view_mode}.type", 
        array(
            'type' => 'select', 
            'options' => array('full' => __t('Full'), 'plain' => __t('Plain'), 'trimmed' => __t('Trimmed'), 'hidden' => __t('Hidden')), 
            'empty' => false,
            'escape' => false,
            'onChange' => "if (this.value == 'trimmed') { $('#trimmed').show(); }else{ $('#trimmed').hide(); };"
        )
    );
?>

<div id="trimmed" style="<?php echo $actualType !== 'trimmed' ? 'display:none;' : ''; ?>">
    <?php 
        echo $this->Form->input("Field.settings.display.{$view_mode}.trim_length", 
            array(
                'type' => 'text', 
                'label' => __d('field_textarea', 'Trim length')
            )
        );
    ?>
</div>