<?php 
    $view_mode = isset($data['settings']['display'][$Layout['viewMode']]) ? $Layout['viewMode'] : 'default';
    
    if ($data['settings']['display'][$view_mode]['type'] != 'hidden') {
        $label = $data['settings']['display'][$view_mode]['label'];
        
        switch($label) {
            case 'hidden': 
                default: 
                    echo ''; 
            break;
            
            case 'inline': 
                echo "<h4 style=\"display:inline;\">{$data['label']}:</h4> "; 
            break;
            
            case 'above': 
                echo "<h4>{$data['label']}</h4> ";
             break;
        }
        
        $fieldData = isset($data['FieldData']['data']) ? $data['FieldData']['data'] : '';

        $data = array(
            'content' => $fieldData, 
            'format' => $data['settings']['display'][$view_mode]
        );

        $this->Layout->hook('field_terms_formatter', $data, array('alter' => true, 'collectReturn' => false));
        
        echo $data['content'];
    }