<?php
/**
 * Node Controller
 *
 * PHP version 5
 *
 * @category Node.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class NodeController extends NodeAppController {
	public $name = 'Node';
	public $uses = array('Node.Node');
	
	public function admin_index() {
		$this->redirect("/admin/node/contents");
	}
	
/**
 * Site frontpage
 */
    public function index() {
        $fp = Configure::read('Variable.site_frontpage');
        if (!empty($fp)) {
            $this->set('front_page', $this->requestAction($fp, array('return') ) );
        } else {
            # USE Node.roles_cache
            $this->Node->unbindModel(array('hasAndBelongsToMany' => array('Role'))); 
            $this->Node->unbindComments();
            
            $this->paginate = array(
                'limit' => Configure::read('Variable.default_nodes_main'), 
                'order' => array(
                    'Node.sticky' => 'DESC', 
                    'Node.modified' => 'DESC'
                )
            );
            
            $conditions = array(
                'Node.status' => 1,
                'Node.promote' => 1,
                'NodeType.status' => 1,
                'OR' => array(
                    array('Node.roles_cache = ' => null),
                    array('Node.roles_cache = ' => '')
                ),
                'Node.language' => array( '', Configure::read('Variable.language.code') )
            );
            
            $userRoles = $this->Auth->user('role_id') ? $this->Auth->user('role_id') : array(3);
            
            foreach ($userRoles as $role_id) {
                $conditions['OR'][] = array('Node.roles_cache LIKE' => "%|{$role_id}|%" );
            }
            
            if ($this->__isAdmin()) { #admin-> no role restrictions
                unset($conditions['OR']);
            }
            
            $this->Layout['node'] = $this->paginate('Node', $conditions);
            $this->Layout['feed'] = '/s/promote:1 language:any';
            $this->Layout['feed'] .= Configure::read('Variable.language.code') ? ',' . Configure::read('Variable.language.code')  : '';
            $this->Layout['feed'] .= '/feed';
        }
        
        $this->Layout['viewMode'] = 'list';
	}
	
/**
 * node details
 *
 */
	public function details($slug) {
        $result = Cache::read("node_{$slug}");
        if (!$result) {
            # USE Node.roles_cache
            $this->Node->unbindModel(array('hasAndBelongsToMany' => array('Role'))); 
            $this->Node->unbindComments();
            
            $conditions = array(
                'Node.slug' => $slug,
                'Node.status' => 1,
                'NodeType.status' => 1,
                'OR' => array(
                    array('Node.roles_cache = ' => null),
                    array('Node.roles_cache = ' => '')
                ),
                'Node.language' => array('', Configure::read('Variable.language.code'))
            );
            
            $userRoles = $this->Auth->user('role_id') ? $this->Auth->user('role_id') : array(3);
            
            foreach ($userRoles as $role_id) {
                $conditions['OR'][] = array('Node.roles_cache LIKE' => "%|{$role_id}|%" );
            }
            
            if ($this->__isAdmin()) { #admin-> no role restrictions
                unset($conditions['OR']);
            }
                
            $this->Node->recursive = 2;
            $result = $this->Node->find('first', array('conditions' => $conditions) );
            
            if (isset($result['Node']['cache']) && !empty($result['Node']['cache'])) { #in seconds
                Cache::config('node_cache', array('engine' => 'File', 'duration' => $result['Node']['cache'] ));
                Cache::write("node_{$slug}", $result, 'node_cache');
            }
		}
        
        if (!$result) {
            throw new NotFoundException(__t('Page not found') );
        }
        
        if (isset($result['Node']['description']) && !empty($result['Node']['description'])) {
            $this->Layout['meta']['description'] = $result['Node']['description'];
        }
        
        $this->loadModel('Comment.Comment');
        # comment reply
        if (isset($this->data['Comment']) && $result['Node']['comment'] == 2) {
            $data = $this->data;
            $data['Comment']['node_id'] = $result['Node']['id'];
            
            if ($this->Comment->save($data)) {
                if (!$this->Auth->user()) {
                    $this->flashMsg(__t('Your comment has been queued for review by site administrators and will be published after approval.'), 'success');
                } else {
                    $this->flashMsg(__t('Your comment has been posted.'), 'success');
                }
                $this->redirect($this->referer());
            } else {
               $this->flashMsg(__t('Comment could not be saved. Please try again.'), 'error');
            }
        }
        
        $this->paginate = array(
            'Comment' => array(
                'order' => array('Comment.created' => 'DESC'),
                'limit' => $result['NodeType']['comments_per_page']
            )
        );
        
        $comments = $this->paginate('Comment', 
            array(
                'Comment.node_id' => $result['Node']['id'],
                'Comment.status' => 1
            )
        );
        
        $result['Comment'] = $comments;
        $this->Layout['viewMode'] = 'full';
        $this->Layout['node']     = $result;
	}
    
    public function search($criteria = false, $rss = false) {
        $keys = array(
            'type' => null,
            'term' => null,
            'language' => null,
            'or' => null,
            'negative' => null,
            'phrase' => null
        );

        $this->Node->unbindModel(
            array(
                'hasAndBelongsToMany' => array('Role'), # USE Node.roles_cache
                'hasMany' => array('Comment')                
            )
        );

        if (isset($this->request->query['criteria']) && !empty($this->request->query['criteria'])) {
            $criteria = $this->request->query['criteria'];
        }

        if ($criteria) {
            $criteria = urldecode($criteria);
            $scope = array();
            $data = array();
            $data['Search']['criteria'] = $criteria;
            $this->data = $data;

            if ($promote = $this->__search_expression_extract($criteria, 'promote')) {
                $criteria = str_replace("promote:{$promote}", '', $criteria);
                $scope['Node.promote'] = intval($promote);
            }

            if ($type = $this->__search_expression_extract($criteria, 'type')) {
                $criteria = str_replace("type:{$type}", '', $criteria);
                $scope['Node.node_type_id'] = explode(',', $type);
            }

            if ($term = $this->__search_expression_extract($criteria, 'term')) {
                $criteria = str_replace("term:{$term}", '', $criteria);
                $term = explode(',', $term);
                
                foreach ($term as $t) {
                    $t = trim($t);
                    
                    if (empty($t)) {
                        continue;
                    }
                    
                    $scope['OR'][] = array('Node.terms_cache LIKE' => "%:{$t}%" );
                }
            }

            if ($language = $this->__search_expression_extract($criteria, 'language')) {
                $criteria = str_replace("language:{$language}", '', $criteria);
                $scope['Node.language'] = explode(',', strtolower($language));
                if (in_array('any', $scope['Node.language'])) {
                    $scope['Node.language'][] = '';
                    unset($scope['Node.language'][array_search('any', $scope['Node.language'])]);
                }
            }

            preg_match_all('/(^| )\-[a-z0-9]+/i', $criteria, $negative);
            if (isset($negative[0])) {
                $criteria = str_replace(implode('', $negative[0]), '', $criteria);
                $criteria = trim(preg_replace('/ {2,}/', ' ',  $criteria));
                
                foreach ($negative[0] as $n) {
                    $n = trim(str_replace('-', '', $n));
                    if (empty($n) ) continue;
                    $scope['NOT']['OR'][] = array('Node.title LIKE' => "%{$n}%");
                    $scope['NOT']['OR'][] = array('Node.slug LIKE' => "%{$n}%");
                    $scope['NOT']['OR'][] = array('Node.description' => "%{$n}%");
                }
            }

            preg_match('/\"(.+)\"/i', $criteria, $phrase);
            if (isset($phrase[1])) {
                $criteria = str_replace($phrase[0], '', $criteria);
                $criteria = trim(preg_replace('/ {2,}/', ' ',  $criteria));
                $phrase = trim($phrase[1]);
                $scope['AND']['OR'][] = array('Node.title LIKE' => "%{$phrase}%");
                $scope['AND']['OR'][] = array('Node.slug LIKE' => "%{$phrase}%");
                $scope['AND']['OR'][] = array('Node.description' => "%{$phrase}%");
            }

            $criteria = explode('OR', trim($criteria));
            foreach ($criteria as $or) {
                $or = trim($or);
                
                if (empty($or)) {
                    continue;
                }
                
                $scope['AND']['OR'][] = array('Node.title LIKE' => "%{$or}%");
                $scope['AND']['OR'][] = array('Node.slug LIKE' => "%{$or}%");
                $scope['AND']['OR'][] = array('Node.description' => "%{$or}%");
            }

            # pass scoping params to modules
            $this->hook('node_search_scope_alter', $scope);

        } elseif (isset($this->data['Search'])) {
            # node types
            if (isset($this->data['Search']['type']) && !empty($this->data['Search']['type'])) {
                $keys['type'] = $this->__search_expression($keys['type'], 'type', implode(',', $this->data['Search']['type']) );
            }

            # taxonomy terms
            if (isset($this->data['Search']['term']) && is_array($this->data['Search']['term']) && !empty($this->data['Search']['term'])) {
                $keys['term'] = $this->__search_expression($keys['term'], 'term', implode(',', $this->data['Search']['term']));
            }

            # node language
            if (isset($this->data['Search']['language']) && is_array($this->data['Search']['language'])) {
                $languages = array_filter($this->data['Search']['language']);
                if (count($languages)) {
                    $keys['language'] = $this->__search_expression($keys['language'], 'language', implode(',', $languages));
                }
            }
    
            if (trim($this->data['Search']['or']) != '') {
                if (preg_match_all('/ ("[^"]+"|[^" ]+)/i', ' ' . $this->data['Search']['or'], $matches)) {
                    $keys['or'] = ' ' . implode(' OR ', $matches[1]);
                }
            }

            if (trim($this->data['Search']['negative']) != '') {
                if (preg_match_all('/ ("[^"]+"|[^" ]+)/i', ' ' . $this->data['Search']['negative'], $matches)) {
                    $keys['negative'] = ' -' . implode(' -', $matches[1]);
                }
            }

            if (trim($this->data['Search']['phrase']) != '') {
                $keys['phrase'] = ' "' . str_replace('"', ' ', $this->data['Search']['phrase']) . '"';
            }

            $keys = Set::filter($keys);
            # pass search keys to modules
            $this->hook('node_search_keys_alter', $keys);
            
            if (!empty($keys)) {
                $keys = implode(' ', $keys);
                $this->redirect('/s/' . urldecode(trim($keys)));
            }
        }

        $languages = array();
        
        foreach (Configure::read('Variable.languages') as $l) {
            $languages[$l['Language']['code']] = $l['Language']['native'];
        }

        $this->set('nodeTypes', 
            $this->Node->NodeType->find('list',
                array(
                    'conditions' => array(
                        'NodeType.status' => 1
                    )
                )
            )
        );

        $this->set('languages', $languages);

        # prepare content
        if (isset($scope)) {
            $scope['Node.status'] = 1; # only published content!
            $this->paginate = array('order' => array('Node.sticky' => 'DESC', 'Node.modified' => 'DESC') );
            $this->Layout['node'] = $this->paginate('Node', $scope);
            $this->Layout['feed'] = "/s/{$data['Search']['criteria']}/feed";
        } else {
            $this->Layout['node'] = array();
        }

        if ($rss) {
            $this->layout = 'rss';
            $this->helpers[] = 'Rss';
            $this->helpers[] = 'Text';
            $this->Layout['viewMode'] = 'rss';
        } else {
            $this->Layout['viewMode'] = 'list';
        }
    }

    private function __search_expression($expression, $option, $value = null) {
        $expression = trim(preg_replace('/(^| )' . $option . ':[^ ]*/i', '', $expression));
        if (isset($value)) {
            $expression .= ' ' . $option . ':' . trim($value);
        }
        return $expression;
    }

    private function __search_expression_extract($expression, $option) {
        if (preg_match('/(^| )' . $option . ':([^ ]*)( |$)/i', $expression, $matches)) {
            return $matches[2];
        }
    }
}