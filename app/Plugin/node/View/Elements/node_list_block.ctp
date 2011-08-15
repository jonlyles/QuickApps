<?php echo $this->Layout->renderNode($node); ?>
<?php echo $this->Html->link(__t('Read more'), "/d/{$node['Node']['slug']}", array('class' => 'read-mode') ); ?>
<br/>