<?php $this->Layout->hook('beforeRenderNode', $this); ?>
    <?php echo $this->Layout->renderNode(); ?>
<?php $this->Layout->hook('afterRenderNode', $this); ?>

<?php if ($Layout['node']['Node']['comment'] > 0): ?>
    <?php $this->Layout->hook('beforeRenderNodeComments', $this, array('alter' => true, 'collectReturn' => false)); ?>
    <?php
        $comments = $this->element('node_details_comments');
        if ( $Layout['node']['Node']['comment'] == 2 )
            $comments .= $this->element('node_details_comments_form');
    ?>    
    <?php echo $this->Html->tag('div', $comments, array('id' => 'comments', 'class' => 'node-comments') ); ?>
    <?php $this->Layout->hook('afterRenderNodeComments', $this, array('alter' => true, 'collectReturn' => false)); ?>
<?php endif; ?>