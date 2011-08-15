<?php
class FieldTermsHookHelper extends AppHelper {
    function field_terms_view($data) {
        return $this->_View->element('view', array('data' => $data), array('plugin' => 'FieldTerms'));
    }

    function field_terms_edit($data) {
        return $this->_View->element('edit', array('data' => $data), array('plugin' => 'FieldTerms'));
    }

    function field_terms_formatter($data) {
        $terms = ClassRegistry::init('Texonomy.Term')->find('all',
            array(
                'conditions' => array(
                    'Term.id' => explode("|", $data['content'])
                )
            )
        );

        if (isset($data['format']['type']) && $data['format']['type'] == 'hidden') {
            $data['content'] = '';
            return;
        }
        
        $data['content'] = array();

        foreach ($terms as $term) {
            switch($data['format']['type']) {
                case 'plain': 
                    default:
                        $data['content'][]= "{$term['Term']['name']}";
                break;
                
                case 'link-localized':
                    $data['content'][] = $this->_View->Html->link(__t($term['Term']['name']), "/s/term:{$term['Term']['slug']}");
                break;
                
                case 'plain-localized':
                    $data['content'][]= __t($term['Term']['name']);
                break;
            }
        }
        
        $data['content'] = implode(', ', $data['content']);
        
        return;
    }
}