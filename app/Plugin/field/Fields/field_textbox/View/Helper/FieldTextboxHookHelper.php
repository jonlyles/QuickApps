<?php
class FieldTextboxHookHelper extends AppHelper {
    function field_textbox_view($data) {
        return $this->_View->element('view', array('data' => $data), array('plugin' => 'FieldTextbox'));
    }
    
    function field_textbox_edit($data) {
        return $this->_View->element('edit', array('data' => $data), array('plugin' => 'FieldTextbox'));
    }
    
    function field_textbox_formatter($data) {
        return;
    }
}