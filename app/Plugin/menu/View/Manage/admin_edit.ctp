<?php echo $this->Form->create(); ?>
    <?php echo $this->Html->useTag('fieldsetstart', __t('Add menu') ); ?>
        <?php echo $this->Form->input('Menu.title', array('required' => 'required', 'type' => 'text', 'label' => __t('Title *'))); ?>
        <?php echo $this->Form->input('Menu.description', array('type' => 'textarea', 'label' => __t('Description'))); ?>
            <?php
                $langs = array();
                foreach (Configure::read('Variable.languages') as $lang) $langs[$lang['Language']['code']] = $lang['Language']['name'];
            ?>
            <?php echo $this->Form->input('Menu.locale', array('options' => $langs, 'type' => 'select', 'multiple' => 'checkbox', 'label' => __t('Show this menu for these languages') ) ); ?>
            <em><?php echo __t('If no language is selected, menu will show regardless of language.'); ?></em>
    <?php echo $this->Html->useTag('fieldsetend'); ?>
    <?php echo $this->Form->submit(__t('Save')); ?>
<?php echo $this->Form->end(); ?>