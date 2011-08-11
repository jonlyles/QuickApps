<?php
/**
 * Node Model
 *
 * PHP version 5
 *
 * @category Node.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class Node extends NodeAppModel {
    var $name = 'Node';
    var $useTable = "nodes";
    var $order = array('Node.modified' => 'DESC');
	
	var $belongsTo = array(
		'NodeType' => array(
			'className' => 'Node.NodeType'
		)
	);
    
    var $hasAndBelongsToMany = array(
        'Term' => array(
            'joinTable' => 'nodes_terms',
            'className' => 'Taxonomy.Term',
            'foreignKey' => 'node_id',
            'associationForeignKey' => 'term_id',
            'unique' => true,
            'dependent' => false
        ),
        'Role' => array(
            'joinTable' => 'nodes_roles',
            'className' => 'User.Role',
            'foreignKey' => 'node_id',
            'associationForeignKey' => 'role_id',
            'unique' => true,
            'dependent' => false
        )
    );

    var $actsAs = array('Sluggable');
    
    var $validate = array(
        'title' => array( 'required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Node title can not be empty')
    );
    
    function afterFind($results, $primary){
        if ( empty($results) || !$primary ) return $results;
        # fetch Field => FieldData based on NodeType
        $Field      = ClassRegistry::init('Field.Field');
        $FieldData  = ClassRegistry::init('Field.FieldData');
        foreach ($results as &$result){
            $result['Field'] = array();
            if ( !isset($result['NodeType']) ) continue;
            $type_fields = $Field->find('all', 
                array(
                    'conditions' => array(
                        'Field.belongsTo' => "NodeType-{$result['NodeType']['id']}"
                    ),
                    'order' => array('Field.ordering' => 'ASC')
                )
            );
            $result['Field'] = Set::extract('/Field/.', $type_fields);
            foreach ( $result['Field'] as &$field){
                $field['FieldData'] = $FieldData->find('first', array('conditions' => array('FieldData.field_id' => $field['id'], 'FieldData.foreignKey' => $result['Node']['id'], 'FieldData.belongsTo' => 'Node') ) );
                $field['FieldData'] = Set::extract('/FieldData/.', $field['FieldData']);
                $field['FieldData'] = isset($field['FieldData'][0]) ? $field['FieldData'][0] : $field['FieldData'];                
            }
        }
        foreach ($results as &$result){
            if ( empty($result['Node']['node_type_base']) ) continue;
            $this->hook("{$result['Node']['node_type_base']}_afterFind", $result, array('collectReturn' => false, 'alter' => true));
        }
        return $results;
    }

    function beforeValidate(){
        $r = isset($this->data['Node']['node_type_base']) ? $this->hook("{$this->data['Node']['node_type_base']}_beforeValidate", $this) : null;
        $r = is_array($r) ? (in_array(false, $r) ? false : $r) : $r;
        return ($r === null ? true : $r);
    }

    function beforeSave($options){
        // Creates terms cache
        if ( isset($this->data['Term']) ){
            $terms_ids = $_terms_ids = array();
            $_terms_ids = Set::extract('Term.{s}', $this->data);
            $_terms_ids = Set::filter($_terms_ids);
            if ( !empty($_terms_ids) )
                foreach ( $_terms_ids as $key => $ids){ $terms_ids = array_merge($terms_ids, $ids); }
            $terms = $this->Term->find('all', array('fields' => array('slug', 'id'), 'conditions' => array('Term.id' => $terms_ids) ) );
            $terms_cache = array();

            foreach($terms as $term)
                $terms_cache[] = "{$term['Term']['id']}:{$term['Term']['slug']}";

            $this->data['Node']['terms_cache'] = implode('|', $terms_cache);
            $this->data['Term']['Term'] = $terms_ids;
        }
        
        $roles = implode("|", Set::extract('/Role/Role', $this->data) );
        $this->data['Node']['roles_cache'] = !empty($roles) ? "|" . $roles . "|" : '';;
        
        if ( isset($this->data['Node']['node_type_base']) ) 
            $this->node_type_base = $this->data['Node']['node_type_base'];
        $r = isset($this->data['Node']['node_type_base']) ? $this->hook("{$this->data['Node']['node_type_base']}_beforeSave", $this) : null;
        $r = is_array($r) ? (in_array(false, $r) ? false : $r) : $r;
        return ($r === null ? true : $r);
    }
    
    function afterSave($created){
        if ( isset($this->data['Node']['slug']) ) 
            Cache::delete("node_{$this->data['Node']['slug']}");
        $r = isset($this->node_type_base) ? $this->hook("{$this->node_type_base}_afterSave", $this) : null;
        $r = is_array($r) ? (in_array(false, $r) ? false : $r) : $r;
        return ( $r === null ? true : $r);
    }
    
    function beforeDelete($cascade){
        # bind comments and delete them
        $this->bindComments();
        $this->recursive = -1;
        $n = $this->data = $this->read();
        $r = isset($n['Node']['node_type_base']) ? $this->hook("{$n['Node']['node_type_base']}_beforeDelete", $this) : null;
        $r = is_array($r) ? (in_array(false, $r) ? false : $r) : $r;
        $r = $r === null ? true : $r;
        /*if ( $r )
            ClassRegistry::init('Field.FieldData')->deleteAll( array('FieldData.belongsTo' => 'Node', 'FieldData.foreignKey' => $n['Node']['id']) );
        */return $r;
    }

    function afterDelete(){
        if ( isset($this->data['Node']['slug']) )
            Cache::delete("node_{$this->data['Node']['slug']}");
        $r = isset($this->data['Node']['node_type_base']) ? $this->hook("{$this->data['Node']['node_type_base']}_afterDelete", $this) : null;
        $r = is_array($r) ? (in_array(false, $r) ? false : $r) : $r;
        $this->unbindComments();
        return ($r === null ? true : $r);
    }
    
    function bindComments(){
        return $this->bindModel(
            array(
                'hasMany' => array(
                    'Comment' => array(
                        'className' => 'Comment.Comment',
                        'dependent' => true
                    )
                )
            )
        );
    }
    
    function unbindComments(){
        return $this->unbindModel(array('hasMany' => array( 'Comment')));
    }
}