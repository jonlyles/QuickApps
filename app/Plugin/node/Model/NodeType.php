<?php
/**
 * Node Type Model
 *
 * PHP version 5
 *
 * @category Node.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class NodeType extends NodeAppModel {
    var $name       = 'NodeType';
    var $useTable   = "node_types";
	var $primaryKey = 'id';

    var $hasAndBelongsToMany = array(
        'Vocabulary' => array(
            'joinTable' => 'types_vocabularies',
            'className' => 'Taxonomy.Vocabulary',
            'foreignKey' => 'node_type_id',
            'associationForeignKey' => 'vocabulary_id',
            'unique' => true,
            'dependent' => false
        )
    );
    
    var $validate = array(
        'name' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Type name can not be empty'),
        'title_label' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Title field label can not be empty')
    );

    var $actsAs = array( 'Sluggable' => array('overwrite' => false, 'slug' => 'id', 'label' => 'name', 'separator' => '_') );
}