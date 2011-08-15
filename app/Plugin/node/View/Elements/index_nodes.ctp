<?php if (!empty($Layout['node'])): # render nodes ?>
    <?php foreach ($Layout['node'] as $node): ?>
        <?php echo $this->element('node_list_block', array('node' => $node), array('plugin' => 'Node')); ?>

    <?php endforeach; ?>
<?php else: ?>
    <?php echo $front_page; ?>
<?php endif; ?>