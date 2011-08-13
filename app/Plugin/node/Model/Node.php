<?php
/**
 * Node Model
 *
 * PHP version 5
 *
 * @package  QuickApps.Plugin.Node.Model
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class Node extends NodeAppModel {
    public $name = 'Node';
    public $useTable = "nodes";
    public $order = array('Node.modified' => 'DESC');
    public $actsAs = array('Sluggable');
    public $validate = array(
        'title' => array('required' => true, 'allowEmpty' => false, 'rule' => 'notEmpty', 'message' => 'Node title can not be empty')
    );

	public $belongsTo = array(
		'NodeType' => array(
			'className' => 'Node.NodeType'
		)
	);

    public $hasAndBelongsToMany = array(
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

    public function afterFind($results, $primary) {
        if (empty($results) || !$primary ) {
            return $results;
        }

        foreach ($results as &$result) {
            if (empty($result['Node']['node_type_base'])) {
                continue;
            }
            
            $this->hook("{$result['Node']['node_type_base']}_afterFind", $result, array('collectReturn' => false, 'alter' => true));
        }
        
        return $results;
    }

    public function beforeValidate() {
        $r = isset($this->data['Node']['node_type_base']) ? $this->hook("{$this->data['Node']['node_type_base']}_beforeValidate", $this) : null;
        
        return ($r === null ? true : $r);
    }

    public function beforeSave($options) {
        // Creates terms cache
        if (isset($this->data['Term'])) {
            $terms_ids = $_terms_ids = array();
            $_terms_ids = Set::extract('Term.{s}', $this->data);
            $_terms_ids = Set::filter($_terms_ids);
            
            if (!empty($_terms_ids)) {
                foreach ($_terms_ids as $key => $ids) { $terms_ids = array_merge($terms_ids, $ids); }
            }
            
            $terms = $this->Term->find('all', array('fields' => array('slug', 'id'), 'conditions' => array('Term.id' => $terms_ids) ) );
            $terms_cache = array();

            foreach ($terms as $term) {
                $terms_cache[] = "{$term['Term']['id']}:{$term['Term']['slug']}";
            }
            
            $this->data['Node']['terms_cache'] = implode('|', $terms_cache);
            $this->data['Term']['Term'] = $terms_ids;
        }
        
        $roles = implode("|", Set::extract('/Role/Role', $this->data) );
        $this->data['Node']['roles_cache'] = !empty($roles) ? "|" . $roles . "|" : '';;
        
        if (isset($this->data['Node']['node_type_base'])) {
            $this->node_type_base = $this->data['Node']['node_type_base'];
        }

        $r = isset($this->data['Node']['node_type_base']) ? $this->hook("{$this->data['Node']['node_type_base']}_beforeSave", $this) : null;
        
        return ($r === null ? true : $r);
    }

    public function afterSave($created) {
        if (isset($this->data['Node']['slug'])) {
            Cache::delete("node_{$this->data['Node']['slug']}");
        }
        
        $r = isset($this->node_type_base) ? $this->hook("{$this->node_type_base}_afterSave", $this) : null;
        
        return ( $r === null ? true : $r);
    }

    public function beforeDelete($cascade) {
        # bind comments and delete them
        $this->bindComments();
        $this->recursive = -1;
        $n = $this->data = $this->read();
        $r = isset($n['Node']['node_type_base']) ? $this->hook("{$n['Node']['node_type_base']}_beforeDelete", $this) : null;
        $r = $r === null ? true : $r;
        
        return $r;
    }

    public function afterDelete() {
        if (isset($this->data['Node']['slug'])) {
            Cache::delete("node_{$this->data['Node']['slug']}");
        }
        
        $r = isset($this->data['Node']['node_type_base']) ? $this->hook("{$this->data['Node']['node_type_base']}_afterDelete", $this) : null;
        $r = is_array($r) ? (in_array(false, $r) ? false : $r) : $r;
        $this->unbindComments();
        
        return ($r === null ? true : $r);
    }

    public function bindComments() {
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

    public function unbindComments() {
        return $this->unbindModel(array('hasMany' => array('Comment')));
    }
}