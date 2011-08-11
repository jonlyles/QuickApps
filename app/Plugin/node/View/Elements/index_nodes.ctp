<?php if (isset($results) ): # render nodes ?>
    <?php foreach ($results as $node): ?>
        <?php echo $this->element('node_list_block', array('node' => $node) ); ?>

    <?php endforeach; ?>
<?php else: ?>
    <?php echo $front_page; ?>
<?php endif; ?>