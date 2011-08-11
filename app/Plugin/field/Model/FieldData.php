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
    var $name       = 'FieldData';
    var $useTable   = 'field_data';
    var $belongsTo = array(
        'Field' => array(
            'className' => 'Field.Field',
            'dependent' => false
        )
    );
}