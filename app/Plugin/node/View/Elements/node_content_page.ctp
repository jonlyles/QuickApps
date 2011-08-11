<?php echo $this->Html->tag('h2', $node['Node']['title'], array('class' => 'node-title') ); ?>

<div class="meta submitter">
    <?php if ( $node['NodeType']['node_show_author'] || $node['NodeType']['node_show_date']): ?>
        <span>
            <?php $node_time_zone = $this->Layout->loggedIn() ? $this->Session->read('Auth.User.timezone') : Configure::read('Variable.date_default_timezone');  ?>
            <?php echo $node['NodeType']['node_show_author'] ? __d('node', 'published by <a href="%s">%s</a>', $this->Html->url("/user/profile/{$node['CreatedBy']['username']}"), $node['CreatedBy']['username']) : '';; ?>
            <?php echo $node['NodeType']['node_show_date'] ? __d('node', ' on %s',  $this->Time->format(__t('M d, Y H:i'), $node['Node']['created'], null, $node_time_zone) ) : '';; ?>
        </span>
    <?php endif; ?>
</div>

<?php foreach($node['Field'] as $field): ?>
    <?php echo $this->Layout->hook("{$field['field_module']}_view", $field, array('collectReturn' => false)); ?>
<?php endforeach; ?>

<?php
    $links = array();
    $tags = '';
    if ( !empty($node['Term']) ){
        foreach($node['Term'] as $term)
            $links[] = $this->Html->link("<span>{$term['name']}</span>", "/s/term:{$term['slug']}", array('escape' => false, 'id' => 'term-' . $term['id'], 'class' => 'term') );
        $this->Layout->hook('node_tags_alter', $links, array('alter' => true, 'collectReturn' => false));
        if ( !empty($links) )
            $tags = __d('node', 'Posted in %s', implode(', ', $links));
    }
?>

<?php if ( !empty($tags) ): ?>
    <div class="node-tags" id="node-tags-<?php echo $node['Node']['id']; ?>"><?php echo $tags; ?></div>
<?php endif; ?>

<?php if ( $Layout['viewMode'] != 'full'): ?>
    <div class="link-wrapper">
        <?php echo $this->Html->link('<span>' . __d('node', 'Read More') . '</span>', "/d/{$node['Node']['slug']}", array('class' => 'read-more', 'escape' => false) ); ?>
    </div>
<?php endif; ?>
