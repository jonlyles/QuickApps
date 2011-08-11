<div id="search-advanced">
    <?php echo $this->Form->create('Search', array('url' => '/s/') ); ?>
        <!-- Criteria -->
        <div class="verticalLayout">
            <?php echo $this->Html->useTag('fieldsetstart', "<span id=\"toggle-search_advanced\">" . __t('Search') . "</span>" ); ?>
                <div id="search_advanced">
                    <div class="text-criterias">
                        <?php echo $this->Form->input('or', array('label' => __t('Containing any of the words'))); ?>
                        <?php echo $this->Form->input('phrase', array('label' => __t('Containing the phrase'))); ?>
                        <?php echo $this->Form->input('negative', array('label' => __t('Containing none of the words'))); ?>
                    </div>
                    <div class="check-criterias">
                        <?php echo $this->Form->input('type', array('label' => __t('Only of the type(s)'), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $nodeTypes) ); ?>
                        <?php echo $this->Form->input('language', array('label' => __t('Languages'), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $languages) ); ?>
                    </div>
                    <!-- Submit -->
                    <?php echo $this->Form->submit(__d('node', 'Search')); ?>
                </div>
            <?php echo $this->Html->useTag('fieldsetend'); ?>
        </div>
    <?php echo $this->Form->end(); ?>
</div>

<?php 
    foreach ($Layout['node'] as $node)
        echo $this->Layout->renderNode($node);
    
    if (count($Layout['node'])):
?>
    <div class="nodes-pagination paginator">
        <?php $this->Paginator->options( array( 'url'=> $this->passedArgs ) ); ?>
        <?php echo $this->Paginator->prev(__d('node', '«'), null, null, array('class' => 'disabled') ); ?>
        <?php echo $this->Paginator->numbers( array('separator' => ' ') ); ?>
        <?php echo $this->Paginator->next(__d('node', '»'), null, null, array('class' => 'disabled')); ?>
    </div>
<?php endif; ?>