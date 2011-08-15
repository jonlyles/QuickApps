<?php
class FieldTextareaHookHelper extends AppHelper {
    function field_textarea_view($data) {
        return $this->_View->element('view', array('data' => $data), array('plugin' => 'FieldTextarea'));
    }
    
    function field_textarea_edit($data) {
        return $this->_View->element('edit', array('data' => $data), array('plugin' => 'FieldTextarea'));
    }
    
    function field_textarea_formatter($data) {
        switch($data['format']['type']) {
            case 'full': default: break;
            case 'plain': 
                $data['content'] = $this->__filterText($data['content']);
            break;
            case 'trimmed':
                $len = @$data['format']['trim_length'];
                $data['content'] = $this->__trimmer($data['content'], $len);
            break;
        }
        return;
    }
    
    private function __filterText($text) {
        return $this->_View->Layout->removeHookTags(html_entity_decode(strip_tags($text)));
    }
    
    private function __trimmer($text, $len = false) {
        $len = !$len || !is_numeric($len) || $len === 0 ? 600 : $len;
        $text = $this->__filterText($text);
        $textLen = strlen($text);
        if ($textLen > $len )
            return substr($text, 0, $len) . ' ...';
        return $text;
    }
}