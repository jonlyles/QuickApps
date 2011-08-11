<?php
/**
 * Taxonomy View Hooks
 *
 * PHP version 5
 *
 * @category Taxonomy.View/Helper
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class TaxonomyHookHelper extends AppHelper {
    /* TODO: hookTag, return nested list of terms */
	public function taxonomy_vocabulary($options) {
        $Vocabulary = Classregistry::init('Taxonomy.Vocabulary');
        $Term = Classregistry::init('Taxonomy.Term');
        
        $voc = $Vocabulary->find('first',
            array(
                'conditions' => array('Vocabulary.slug' => $options['slug']),
                'recursive' => 2
            )
        );
        
        $terms = $Term->find('all', 
            array(
                'conditions' => array('Term.vocabulary_id' => $voc['Vocabulary']['id']),
                'order' => array('Term.lft' => 'ASC')
            )
        );
    }
    
    public function beforeLayout($layoutFile) {
        $show_on = (
            Router::getParam('admin') && 
            $this->request->params['plugin'] == 'taxonomy' &&
            $this->request->params['controller'] == 'vocabularies' && 
            $this->request->params['action'] == 'admin_index' 
        );
        
        $this->_View->Layout->blockPush( array('body' => $this->_View->element('toolbar') . '<!-- TaxonomyHookHelper -->' ), 'toolbar', $show_on);
        
        return true;
    }    
}