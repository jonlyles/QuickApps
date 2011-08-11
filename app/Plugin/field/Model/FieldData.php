<?php
/**
 * Field Data Model
 *
 * PHP version 5
 *
 * @category Field.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class FieldData extends FieldAppModel {
    public $name = 'FieldData';
    public $useTable = 'field_data';
    
    public $belongsTo = array(
        'Field' => array(
            'className' => 'Field.Field',
            'dependent' => false
        )
    );
}