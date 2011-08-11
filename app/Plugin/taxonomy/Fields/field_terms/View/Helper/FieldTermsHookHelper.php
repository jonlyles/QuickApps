<?php
class FieldTermsHookHelper extends AppHelper {
    function field_terms_view($data) {
        return $this->_View->element('view', array('data' => $data), array('plugin' => 'FieldTerms') );
    }
    
    function field_terms_edit($data) {
        return $this->_View->element('edit', array('data' => $data), array('plugin' => 'FieldTerms') );
    }
    
    function field_terms_formatter($data) {
        $_options = $options = array();
        if (!empty($data['options'])) {
            $_options = explode("\n", $data['options']);
            foreach ($_options as $option) {
                $option = explode("|",$option);
                $value = $option[0];
                $label = isset($option[1]) ? $option[1] : $option[0];
                $options[$value] = $label;
            }
        }        
        
        $content = explode("|", $data['content']);

        $data['content'] = '';
        foreach ($content as $key) {
            switch($data['format']['type']) {
                case 'key': 
                    $data['content'] .= "{$key}<br/>";
                break;
                case 'default': #Label
                    default:
                        $data['content'] .= @"{$options[$key]}<br/>";
                break;
            }
        }
        return;
    }
}