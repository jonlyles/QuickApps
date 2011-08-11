<?php echo $this->Html->useTag('fieldsetstart', __t('Content')); ?>
    <?php foreach ($data['Field'] as $field): ?>
        <?php echo $this->Layout->hook("{$field['field_module']}_edit", $field, array('collectReturn' => false)); ?>
    <?php endforeach; ?>
<?php echo $this->Html->useTag('fieldsetend'); ?>