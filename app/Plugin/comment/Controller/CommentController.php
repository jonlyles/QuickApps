<?php
/**
 * Comment Controller
 *
 * PHP version 5
 *
 * @category Comment.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class CommentController extends CommentAppController {
	var $name = 'Comment';
	var $uses = array();
    
    function admin_index(){
        $this->redirect('/admin/comment/published');
    }	
}