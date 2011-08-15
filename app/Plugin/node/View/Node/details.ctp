<?php 
    // node
    $collect = $this->Layout->hook('beforeRenderNode', $this,
        array(
            'alter' => true, 
            'collectReturn' => true
        )
    );
    echo implode(' ', (array)$collect);

    echo $this->Layout->renderNode(); 

    $collect = $this->Layout->hook('afterRenderNode', $this,
        array(
            'alter' => true, 
            'collectReturn' => true
        )
    );
    echo implode(' ', (array)$collect);
    // end node
    
    // comments
    if ($Layout['node']['Node']['comment'] > 0) {
        $collect = $this->Layout->hook('beforeRenderNodeComments', $this, 
            array(
                'alter' => true, 
                'collectReturn' => true
            )
        );
        
        echo implode(' ', (array)$collect);

        $comments = $this->element('node_details_comments');
        
        if ($Layout['node']['Node']['comment'] == 2) {
            $comments .= $this->element('node_details_comments_form');
        }
 
        echo $this->Html->tag('div', $comments, array('id' => 'comments', 'class' => 'node-comments')); 
        
        $collect = $this->Layout->hook('afterRenderNodeComments', $this, 
            array(
                'alter' => true, 
                'collectReturn' => true
            )
        );
        echo implode(' ', (array)$collect);
    }
    // end comments
