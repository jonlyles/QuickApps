<?php if (!empty($this->data['NodeType']['id'])): ?>
    <?php echo $this->Form->create('Node', array('url' => "/admin/node/contents/edit/{$this->data['Node']['slug']}")); ?>
        <!-- Content -->
        <?php echo $this->Html->useTag('fieldsetstart', __t('Editing') . " \"{$this->data['Node']['title']}\" ({$this->data['NodeType']['name']})"   ); ?>
            <?php echo !empty($this->data['NodeType']['description']) ? $this->data['NodeType']['description'] : ''; ?>
            <?php echo $this->Form->hidden('node_type_id'); ?>
            <?php echo $this->Form->hidden('node_type_base'); ?>
            <?php echo $this->Form->hidden('id'); ?>
            <?php echo $this->Form->input('Node.title', array('required' => 'required', 'label' => __t($this->data['NodeType']['title_label']) . ' *' )); ?>
            <em><?php echo __t('Slug: %s', $this->data['Node']['slug']); ?></em>
            
            <p>
                <?php echo $this->Form->input('regenerate_slug', array('type' => 'checkbox', 'label' => __t('Regenerate slug'))); ?>
                <em><?php echo __t('If this option is checked and title has changed then a new slug is generated.'); ?></em>
            </p>
            <?php echo $this->Form->input('Node.description', array('type' => 'textarea', 'label' => __t('Description'), 'rows' => 2)); ?>
            <em><?php echo __t('A short description (255 chars. max.) about this content. Will be used as page meta-description when rendering this content node.'); ?></em>
        <?php echo $this->Html->useTag('fieldsetend'); ?>
        
        <!-- NodeType Form -->
        <?php $refData = $this->data; ?>
        <?php echo $this->Layout->hook("{$this->data['NodeType']['base']}_form", $refData); ?>
        
        <!-- Settings -->
        <?php echo $this->Html->useTag('fieldsetstart', __t('Settings')); ?>
            <?php echo $this->Html->useTag('fieldsetstart', __t('Comments')); ?>
                <?php echo $this->Form->input('comment', array('type' => 'radio', 'legend' => false, 'separator' => '<br>', 'options' => array(2 => __t('Open'), 0 => __t('Closed'), 1 => __t('Read Only')))); ?>
            <?php echo $this->Html->useTag('fieldsetend'); ?>
            
            <?php echo $this->Html->useTag('fieldsetstart', __t('Language')); ?>
                <?php echo $this->Form->input('language', array('empty' => __t('-- Any --'), 'type' => 'select', 'label' => 'Language', 'options' => $languages)); ?>
                <em><?php echo __t('If no language is selected (-- Any --), node will show regardless of language'); ?></em>
            <?php echo $this->Html->useTag('fieldsetend'); ?>
            
            <?php echo $this->Html->useTag('fieldsetstart', __t('Publishing')); ?>
                <?php echo $this->Form->input('status', array('type' => 'checkbox', 'label' => __t('Published'), 'value' => 1)); ?>
                <?php echo $this->Form->input('promote', array('type' => 'checkbox', 'label' => __t('Promoted to front page'), 'value' => 1)); ?>
                <?php echo $this->Form->input('sticky', array('type' => 'checkbox', 'label' => __t('Sticky at top of lists'), 'value' => 1)); ?>
            <?php echo $this->Html->useTag('fieldsetend'); ?>
            
            <?php echo $this->Html->useTag('fieldsetstart', __t('Cache this node for')); ?>
                <?php 
                    echo $this->Form->input('cache', 
                        array(
                            'type' => 'select', 
                            'label' => __t('Cache'), 
                            'options' => array(
                                '' => __t('-- No Cache --'),
                                '+1 hour' => __t('%s Hours', 1),
                                '+2 hours' => __t('%s Hours', 2),
                                '+4 hours' => __t('%s Hours', 3),
                                '+7 hours' => __t('%s Hours', 7),
                                '+11 hours' => __t('%s Hours', 11),
                                '+16 hours' => __t('%s Hours', 16),
                                '+22 hours' => __t('%s Hours', 22),
                                '+1 day' => __t('%s Days', 1),
                                '+3 day' => __t('%s Days', 3),
                                '+5 day' => __t('%s Days', 5),
                                '+1 week' => __t('%s Weeks', 1),
                                '+2 weeks' => __t('%s Weeks', 2),
                                '+3 weeks' => __t('%s Weeks', 3),
                                '+1 month' => __t('%s Months', 1),
                                '+3 months' => __t('%s Months', 3),
                                '+6 months' => __t('%s Months', 6),
                                '+9 months' => __t('%s Months', 9),
                                '+1 year' => __t('Year')
                            )
                        )
                    );
                ?>
           <?php echo $this->Html->useTag('fieldsetend'); ?>
            
            <?php echo $this->Html->useTag('fieldsetstart', __t('Roles')); ?>
                <?php echo $this->Form->input('Role', array('options' => $roles, 'type' => 'select', 'selected' => Set::extract('/Role/id', $this->data), 'multiple' => 'checkbox', 'label' => __t('Show content for specific roles'))); ?>
                <em><?php echo __t("Show this content only for the selected role(s). If you select no roles, the content will be visible to all users."); ?></em>
            <?php echo $this->Html->useTag('fieldsetend'); ?>
        <?php echo $this->Html->useTag('fieldsetend'); ?>
        
        <?php if (!empty($vocabularies)) : ?>
        <!-- Terms -->
        <?php echo $this->Html->useTag('fieldsetstart', __t('Terms')); ?>
            <?php foreach ($vocabularies as $vocabulary => $terms): ?>
                <?php if (empty($terms)) continue; ?>
                <?php $s =  $this->Form->input("Term.{$vocabulary}", array('name' => "data[Term][{$vocabulary}]", 'selected' => Set::extract('/Term/id', $this->data), 'escape' => false, 'options' => $terms, 'type' => 'select', 'multiple' => true, 'label' => __t($vocabulary))); ?>
                <?php echo $this->Html->div(null, $s, array('style' => 'width:auto; float:left; margin-right:20px;')); ?>
            <?php endforeach; ?>
        <?php echo $this->Html->useTag('fieldsetend'); ?>
        <?php endif; ?>
        
        <!-- Submit -->
        <?php echo $this->Form->input(__t('Save content'), array('type' => 'submit')); ?>
    <?php echo $this->Form->end(); ?>
<?php else: ?>
<!-- NodeType not found -->
<?php endif; ?>
