<?php if (!empty($Layout['node'])): # render nodes ?>
    <?php foreach ($Layout['node'] as $node): ?>
        <?php echo $this->Layout->renderNode($node); ?>

    <?php endforeach; ?>
<?php else: ?>
    <?php echo $front_page; ?>
<?php endif; ?>