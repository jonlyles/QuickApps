    <?php echo "<h2>{$node['Node']['title']}</h2>"; ?>
    <?php echo $this->Layout->renderNode($node); ?>
    <?php echo $this->Html->link(__t('Read more'), "/d/{$node['Node']['slug']}", array('class' => 'read-mode') ); ?>
    <br/>