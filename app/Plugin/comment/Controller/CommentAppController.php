<?php 
/**
 * Comment Application Controller
 *
 * PHP version 5
 *
 * @category Comment.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class CommentAppController extends AppController {
    public $uses = array('Comment.Comment');
    
    public function countUnpublished() {
        $count = $this->Comment->find('count',
            array(
                'conditions' => array(
                    'Comment.status' => 0
                )
            )
        );
        
        $this->set('countUnpublished', $count);
        
        return $count;
    }
}