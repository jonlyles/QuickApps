<?php
/**
 * Layout Helper
 *
 * PHP version 5
 *
 * @category View/Helper
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
App::uses('AppHelper', 'View/Helper'); 
class LayoutHelper extends AppHelper {

/*
 * Used by some methods to cache data in order to improve 
 * comunication between them, for example see blocksInRegion()
 */
    private $tmp = array(); 
    
/**
 * Render css files links
 *
 * @param array $stylesheets Asociative array:
 * 	array(
 *		'print' => array(file1, file2),
 *		'all' => array(file3, file4),
 *      ....
 *	);
 *
 * @return string HTML style tags
 */
    public function stylesheets($stylesheets = false) {
		$output = $embed = '';
		$stylesheets = !$stylesheets ? $this->_View->viewVars['Layout']['stylesheets'] : $stylesheets;
        $this->hook('stylesheets_alter', $stylesheets);	# pass css list array to modules
        foreach ($stylesheets as $media => $files){
            foreach ( $files as $file ){
                if ( $media !== 'embed' ){
                    $output .= "\n". $this->_View->Html->css($file, 'stylesheet', array('media' => $media ) );
                } else {
                    $embed .= "{$file}\n\n";
                }
            }
        }
        $output = !empty($embed) ? $output . "\n<style>\t\n {$embed} \n</style>\n" : $output;
        return $output;
    }
    
/**
 * Render js files links
 *
 * @param array $javascripts Asociative array:
 * 	array(
 *		'code' => array("code1", "code2"),
 *		'files' => array("path_to_file1", "path_to_file2")
 *	) 
 *
 * @return string
 */
    public function javascripts($javascripts = false) {
	
		$output = '';
		$javascripts = !$javascripts ? $this->_View->viewVars['Layout']['javascripts'] : $javascripts;
		
		$this->hook('javascripts_alter', $javascripts);	# pass javascripts list to modules if they need to alter them
		
		# js files first
        $javascripts['file'] = array_unique($javascripts['file']);
		foreach ($javascripts['file'] as $file) {
			$output .= "\n" . $this->_View->Html->script($file);
		}
		
		# js embed blocks after
		$c_blocks = "\n";
        $javascripts['embed'] = array_unique($javascripts['embed']);
		foreach ($javascripts['embed'] as $block)
			$c_blocks .=  $block . "\n\n";
		
		$output .= "\n" . $this->_View->Html->scriptBlock($c_blocks);
        return "\n" . $output . "\n";
    }
    
/**
 * Render extra code for header
 *
 * @return string HTML code
 */
    public function header(){
        if ( is_string($this->_View->viewVars['Layout']['header']) )
            return $this->_View->viewVars['Layout']['header'];
        if ( is_array($this->_View->viewVars['Layout']['header']) ){
            $out = '';
            foreach ( $this->_View->viewVars['Layout']['header'] as $code )
                $out .= "{$code}\n";
        }
        return "\n" . $out;
    }
    
/**
 * Return title_for_layout
 *
 * @return string page title
 */
	public function title(){
        $title = isset($this->_View->viewVars['Layout']['node']['Node']['title']) ? __t($this->_View->viewVars['Layout']['node']['Node']['title']) : Configure::read('Variable.site_name');
		$title = $this->_View->viewVars['title_for_layout'] != Inflector::camelize($this->_View->params['controller']) || Router::getParam('admin') ? $this->_View->viewVars['title_for_layout'] : $title;
        $this->hook('title_for_layout_alter', $title);	# pass title_for_layout to modules
		return $this->hookTags(__t($title));
	}
        
/**
 * Return content_for_layout
 *
 * @return string HTML content
 */
	public function content(){
		$content = $this->_View->viewVars['content_for_layout'];
		$this->hook('content_for_layout_alter', $content);	# pass content_for_layout to modules
		return $content;
	}    
    
/**
 * Render extra code for footer (just before </body>)
 *
 * @return string HTML code
 */
    public function footer(){
        if ( is_string($this->_View->viewVars['Layout']['footer']) )
            return $this->_View->viewVars['Layout']['header'];
        if ( is_array($this->_View->viewVars['Layout']['footer']) ){
            $out = '';
            foreach ( $this->_View->viewVars['Layout']['footer'] as $code )
                $out .= "{$code}\n";
        }
        return "\n" . $out;
    }
	
/**
 * return rendered meta tags
 *
 * @param array $metaForLayout optional asociative array of aditional metas to merge with Layout metas meta_name => content.
 *
 * @return string HTML formatted meta tags
 */
    public function meta($metaForLayout = array()) {
		if ( !is_array($metaForLayout) || empty($metaForLayout) )
			$metaForLayout = Set::merge($this->_View->viewVars['Layout']['meta'], $metaForLayout);
        $output = '';
        foreach ($metaForLayout as $name => $content)
            $output .= $this->_View->Html->meta($name, $content) . "\n";
        return $output;
    }
    
/**
 * Returns node type of the current node's page
 * (Valid only when rendering a single node)
 *
 * @return string
 */
    public function getNodeType(){
        if ( !isset($this->_View->viewVars['Layout']['node']['NodeType']['id']) )
            return false;
        return $this->_View->viewVars['Layout']['node']['NodeType']['id'];
    }

/**
 * Returns content of current node for content_for_layout, render based on NodeType
 * This function must be used only from Node Module
 *
 * @param array $node optional node's asociative array to render. 
 *                             If no data is pass current node will be rendered
 *
 * @return string HTML formatted node
 */
    public function renderNode($node = false){
        $node = !$node ? $this->_View->viewVars['Layout']['node'] : $node;
        if ( empty($node) ) return '';
        $content = '';
        
        $view_mode = $this->_View->viewVars['Layout']['viewMode'];
        foreach ( $node['Field'] as $key => &$data) # no viewMode defined -> use default
            if ( !isset($data['settings']['display'][$view_mode]) && isset($data['settings']['display']['default']) )
                $data['settings']['display'][$view_mode] = $data['settings']['display']['default'];
        
        $node['Field'] = Set::sort($node['Field'], "{n}.settings.display.{$view_mode}.ordering", 'asc');
        $sufix = $node['NodeType']['module'] == 'node' ? 'render' : $node['NodeType']['id'];
        $callback = "{$node['NodeType']['base']}_{$sufix}";
        $content .= implode('', (array)$this->hook('beforeRenderNode', $node, array('alter' => true, 'collectReturn' => true))); 
        $content .= $this->hook($callback, $node, array('collectReturn' => false));
        if ( empty($content) )  $content = $this->default_renderNode($node);
        $content = $this->hookTags($content);
        $content .= implode('', (array)$this->hook('afterRenderNode', $node, array('alter' => true, 'collectReturn' => true))); 
        
        $nodeClasses = array();
        $nodeClasses[] = 'node';
        $nodeClasses[] = "node-{$node['NodeType']['id']}";
        $nodeClasses[] = "node-" . ($node['Node']['promote'] ? "promoted" : "demote");
        $nodeClasses[] = "node-" . ($node['Node']['sticky'] ? "sticky" : "nosticky");
        $nodeClasses[] = "node-{$this->_View->viewVars['Layout']['viewMode']}";
        
        return "\n<div id=\"node-{$node['Node']['id']}\" class=\"" . implode(' ', $nodeClasses) . "\">\n\t" . $this->hookTags($content). "\n</div>\n";
    }
    
/**
 * Removes all hookTags from given string (except special tags)
 * Useful for plain text converting
 *
 * @param string $string Text where to remove all tags
 *
 * @return string
 */
    public function removeHookTags($string){
        $string = $this->specialTags($string);
        $tags = implode('|', $this->events);
        return preg_replace('/(.?)\[(' . $tags . ')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', '', $string);
    }
    
/**
 * Get value of Node's field
 *
 * @param string $field name of the field to get from Node
 *
 * @return string
 */
    public function nodeField($field = 'id') {
        if (isset($this->_View->viewVars['Layout']['node']['Node'][$field]))
            return $this->_View->viewVars['Layout']['node']['Node'][$field];
        return false;
    }
    
/**
 * Wrapper for field rendering hook
 *
 * @return string HTML formatted field
 */
    public function renderField($field){
        return 
            "\n<div class=\"field-container {$field['name']}\">\n" . 
                $this->Layout->hook("{$field['field_module']}_render", $field, array('collectReturn' => false)) . 
            "\n</div>\n";
    }
    
/**
 * Show flash message
 *
 * @return string
 */
    public function sessionFlash() {
        $messages = $this->Session->read('Message');
        if( is_array($messages) ) {
            $out = '';
            foreach(array_keys($messages) as $key)
                $out .= $this->Session->flash($key);
            return $out;
        } elseif ( is_string($messages) ) {
            return $messages;
        }
        return false;
    }
    
/**
 * return rendered breadcrumb. Data is passed to themes for formatting the crumbs.
 * If is no response from theme then default formatting is fired.
 *
 * @return string HTML formatted breadcrumb
 */
	public function breadCrumb(){
		$b = $this->_View->viewVars['breadCrumb'];
        $crumbs = $this->hook('theme_breadcrumb', $b, array('collectReturn' => false));
        $crumbs = empty($crumbs) ? $this->default_theme_breadcrumb($b) : $crumbs;
		return $crumbs;
	}
    
/**
 * Render child nodes of the given menu node (father) (direct nodes onlye, that is no recursive)
 *
 * @param mixed $path String path of the father node or boolen false to use current path 
 * @param string $region Theme region where the child nodes will be rendered, 'content' by default
 *
 * @return string Html menu
 */
    public function menuNodeChildren($path = false, $region = 'content'){
        $output = '';
        if (!$path){
            $base = Router::url('/');
            $path = '/';
            $path .= $base !== '/' ? str_replace($base, '', $this->_View->here) : $this->_View->here;
            $path = preg_replace("/\/{2,}/i", '/', $path);
        }
       
        $MenuLink = Classregistry::init('Menu.MenuLink');
        $here =  $MenuLink->find('first',
            array(
                'conditions' => array(
                    'MenuLink.router_path' => $path,
                    'MenuLink.status' => 1
                )
            )
        );
        
        if ( !empty($here) ){
            $subs = $MenuLink->find('all', array('conditions' => array('MenuLink.status' => 1, 'MenuLink.parent_id' => $here['MenuLink']['id']) ) );
            $_subs['MenuLink']	= Set::extract('{n}.MenuLink', $subs);
            $_subs['region']	= $region; 
            $_subs['id']		= 'no-id';
            foreach($_subs['MenuLink'] as &$node){
                $node['link_title'] = __t($node['link_title']);
                $node['description'] = __t($node['description']);
            }
            $output = $this->hook('theme_menu', $_subs, array('collectReturn' => false));
        }
        return $output;        
    }
	
/**
 * Get Role ID of the current loged in user
 *
 * @return array of all roles that user belongs to
 */
    public function getRoleId() {
        $roleId = $this->isLoggedIn() ? $this->Session->read('Auth.User.role_id') : 3;
        return $roleId;
    }
    
/**
 * Verify if the given URL is allowed to user
 *
 * @param string $url URL to check permissions if nothing is given then current url will be checked
 *
 * @return boolean
 */   
    public function isAllowed($url = false){
        $url = !$url ? $this->_View->here : $url;
        $url = Router::parse($url);
        //pr($url);
    }
    
/**
 * Insert custom block to stack
 *
 * @param array $data formatted block array (see $_block)
 * @param string $region theme region
 * @param boolean $show_on optional, shortcut for 'if ( this == that) blockPush()' 
 *         becomes : 'blockPush(, , ( this == that) )'
 *
 * @return boolean true on success
 */
    public function blockPush($block = array(), $region = '', $show_on = true){
        if (!$show_on) return;
            
        $_block = array(
            'title' => '', 
            'pages' => '',
            'visibility' => 0,
            'body' => '', #
            'region' => null, 
            'theme' => null, 
            'format' => null #
        );
        
        $block = array_merge($_block, $block);
        $block['module'] = null;
        $block['id'] = null;
        $block['delta'] = null;
        
        if ( !empty($region) )
            $block['region'] = $region;
        
        if ( is_null($block['theme']) )
            $block['theme'] =  $this->themeName();
        
        if ( empty($block['region']) || empty($block['body']) )
            return false;
            
        $__block  = $block;
        unset($__block['format'], $__block['body'], $__block['region'], $__block['theme']);
        $Block = array(
            'Block' => $__block,
            'BlockCustom' => array(
                'body' => $block['body'],
                'format' => $block['format']
            ),
            'BlockRegion' => array(
                0 => array(
                    'theme' => $this->themeName(),
                    'region' => $block['region']
                )
            )
        );
        
        $this->_View->viewVars['Layout']['blocks'][] = $Block;
        
        return true;
    }
    
/**
 * Creates a simple menu list (deph = 1)
 *
 * @param array $links array of links: array('title', '/your/url/')
 * @param array $options can be:
 *      id: id attribute for the container (ul, ol)
 *      type: type of list, ol, ul. default: ul
 *      itemType: type of child node. default: li
 *      selectedClass: class attribute for selected itemType. default: selected
 * @return string HTML
 */
    public function toolbar($links, $options = array()){
        /* Hook */
        $data = array('links' => $links, 'options' => $options);
        $this->hook('toolbar_alter', $data, array('alter' => true, 'collectReturn' => true));
        extract($data);
        
        $_options = array(
            'id' => null,
            'type' => 'ul',
            'itemType' => 'li',
            'selectedClass' => 'selected'
        );
        
        $options = array_merge($_options, $options);
        extract($options);
        
        $id = !is_null($id) ? " id=\"{$id}\" " : '';
        $o = "<{$type}{$id}>\n";
        $here = preg_replace("/\/{2,}/i", '/', str_replace($this->_View->base, '', $this->_View->here) . "/");
        foreach ( $links as $link ){
            $link[1] = preg_replace("/\/{2,}/i", '/', "{$link[1]}/");
            $selected =   strpos($here, $link[1]) !== false  ? " class=\"{$selectedClass}\" " : '';
            $link = isset($link[2]) && is_array($link[2]) ? $this->_View->Html->link($link[0], $link[1], $link[2]) : $this->_View->Html->link($link[0], $link[1]);
            $o .= "\t<{$itemType}{$selected}><span>" . $link . "</span></{$itemType}>\n";
        }
        $o .= "\n</{$type}>";
        return $o;
    }
    
/**
 * gives current theme name
 *
 * @return string theme name
 */
    public function themeName(){
        return Configure::read('Theme.info.folder');
    }
    
/**
 * Region is empty ?
 * returns true if Region has no Blocks.
 *
 * @param string $region Region alias
 * @return boolean true if region has no blocks, false otherwise
 */
    public function emptyRegion($region) {
        return ($this->blocksInRegion($region) == 0);
    }
    
/**
 * Returns the numbers of blocks in a region
 *
 * @param string $region Region alias
 * @return integer
 */
    public function blocksInRegion($region){
		$blocks_in_theme = Set::extract("/BlockRegion[theme=" . $this->themeName() . "]/..", $this->_View->viewVars['Layout']['blocks']);
		$blocks_in_region = Set::extract("/BlockRegion[region={$region}]/..", $blocks_in_theme);

        $t = 0;
        foreach ($blocks_in_region as $key => $block) {
            if ( !empty($block['UserRole'])  ){
                $roles_id = Set::extract('/UserRole/id', $block);
                $allowed = false;
                foreach ( $this->userRoles() as $role ){
                    if ( in_array($role, $roles_id) ){
                        $allowed = true;
                        break;
                    }
                }
            }
            switch ( $block['Block']['visibility'] ){
                case 0:
                    $allowed = $this->__matchPath($block['Block']['pages']) ? false : true;
                break;
                
                case 1:
                    $allowed = $this->__matchPath($block['Block']['pages']) ? true : false;
                break;
                
                case 2: 
                    $allowed = $this->__php_eval($block['Block']['pages']);
                break;
            }
            if ( !$allowed )
                continue;
            if (    !isset($this->tmp['blocksInRegion'][$region]['blocks_ids']) || 
                    !in_array($block['Block']['id'], $this->tmp['blocksInRegion'][$region]['blocks_ids']) 
            ){
                $this->tmp['blocksInRegion'][$region][] = $block;                              # Cache improve
                $this->tmp['blocksInRegion'][$region]['blocks_ids'][] = $block['Block']['id'];   # Cache improve
            }
            $t++;
        }
        
        return ($t>0);
    }
    
/**
 * Checks User is logged in
 *
 * @return boolean
 */
    public function loggedIn() {
        return $this->Session->check('Auth.User.id');
    }
    
/**
 * Retuns current user roles
 *
 * @return array associative array with id and names of the roles: array(id:integer => name:string, ...)
 */
    public function userRoles(){
        $roles = array();
        if (!$this->loggedIn()){
            $roles[] = 3;
        } else {
            $roles = CakeSession::read('Auth.User.role_id');
        }
        return $roles;
    }
    
/**
 * Render all blocks for a particular Region
 *
 * @param string $region Region alias
 * @return string Html blocks
 */
    public function blocks($region) {
        $output = '';
		if ( !$this->emptyRegion($region) ) {
            $blocks =  isset($this->tmp['blocksInRegion'][$region]) ?  
                        $this->tmp['blocksInRegion'][$region] : # else
                        Set::extract("/BlockRegion[region={$region}]/..", 
                            Set::extract("/BlockRegion[theme=" . $this->themeName() . "]/..", 
                                $this->_View->viewVars['Layout']['blocks'])
                        );
            $blocks = Set::sort($blocks, '{n}.BlockRegion.{n}.ordering', 'asc');

            $i = 1;
            $total = count($blocks);
			foreach ($blocks as $block) {
                $block['Block']['__region'] = $region;
                $block['Block']['__weight'] = array($i, $total);
                if ( $o = $this->block($block) ){
                    $output .= $o;
                    $i++;
                }
			}
			$_data = array('html' => $output, 'region' => $region);
			$this->hook('theme_region_blocks_alter', $_data, array('alter' => true, 'collectReturn' => false)); // pass all rendered blocks (HTML) to modules
            extract($_data);
        }
        return $output;
    }
    
/**
 * Render single block
 *
 * @param array $block Well formated  block array
 * @param array $options
 * @return string Html
 */   
    public function block($block, $options = array() ){
        $options = array_merge( 
            array(
                'title' => true,
                'body' => true,
                'region' => true
            ),
            $options
        );
        
        $block['Block']['__region'] = !isset($block['Block']['__region']) ? '' : $block['Block']['__region'];
        $block['Block']['__weight'] = !isset($block['Block']['__weight']) ? array(0, 0) : $block['Block']['__weight'];
    
        if ( isset($block['Block']['locale']) && !empty($block['Block']['locale']) && !in_array(Configure::read('Variable.language.code'), $block['Block']['locale']) )
            return;
        
        if ( isset($block['Menu']) ){
            $block['Menu']['locale'] = (array) unserialize($block['Menu']['locale']);
            if ( !empty($block['Menu']['locale'][0]) && !in_array(Configure::read('Variable.language.code'), $block['Menu']['locale']) )
                return;
        }

        if (!empty($block['Role'])){
            $roles_id = Set::extract('/Role/id', $block);
            $allowed = false;
            foreach ($this->userRoles() as $role){
                if (in_array($role, $roles_id)){
                    $allowed = true;
                    break;
                }
            }
            if(!$allowed) return;
        }
        $region = $block['Block']['__region'];
        /** 
         * Check visibility
         * 0 = Show on all pages except listed pages
         * 1 = Show only on listed pages
         * 2 = Use custom PHP code to determine visibility
         */
        $block['Block']['pages'] = strtolower($block['Block']['pages']);
        switch ( $block['Block']['visibility'] ){
            case 0:
                $allowed = $this->__matchPath($block['Block']['pages']) ? false : true;
            break;
            
            case 1:
                $allowed = $this->__matchPath($block['Block']['pages']) ? true : false;
            break;
            
            case 2: 
                $allowed = $this->__php_eval($block['Block']['pages']);
            break;
        }

        if ( !$allowed ) return; #skip if is not allowed
        
        $Block = array(
            'id' => $block['Block']['id'],
            'module' => $block['Block']['module'],
            'delta' => $block['Block']['delta'],
            'title' => $block['Block']['title'],
            'body' => null,
            'region' => $region,
            'description' => null,
            'format' => null # 
        );
        
        if (!empty($block['Menu']['id']) && $block['Block']['module'] == 'menu'){
            // menu block
            $block['Menu']['region'] = $region;
            $Block['title'] = empty($Block['title']) ? $block['Menu']['title'] : $Block['title'];
            $Block['body'] = $this->hook('theme_menu', $block['Menu'], array('collectReturn' => false));
            $Block['description'] = $block['Menu']['description'];
        } elseif ( !empty($block['BlockCustom']['body']) ){
            // custom block
            $Block['body'] = @$block['BlockCustom']['body'];
            $Block['format'] = @$block['BlockCustom']['format'];
            $Block['description'] = @$block['BlockCustom']['description'];
        } else {
            // module block
            
            // module hook must return formated array block
            $Block = $this->hook("{$block['Block']['module']}_{$block['Block']['delta']}", $block, array('collectReturn' => false));
            if ( empty($Block) ) return false;
            $Block['id'] = $block['Block']['id'];
            $Block['module'] = $block['Block']['module'];
            $Block['delta'] = $block['Block']['delta'];
            $Block['region'] = $region;
            $Block['title'] = !isset($Block['title']) ? $block['Block']['title'] : $Block['title'];
        }
        
        $Block['weight'] = $block['Block']['__weight']; // X of total
        if ( $options['title'] ){
            $Block['title'] = $this->hookTags($Block['title']);
        } else {
            unset($Block['title']);
        }
        
        if ( $options['body'] ){
            $Block['body'] = $this->hookTags($Block['body']);
        } else {
            unset($Block['body']);
        }

        if (!$options['region']) $Block['region'] = null;
        
        $this->hook('theme_block_alter', $Block, array('alter' => true, 'collectReturn' => true));	// pass block item to theme before render
        $out = $this->hook('theme_block', $Block, array('alter' => true, 'collectReturn' => false));	// try theme rendering
        
        // No response from theme -> use default rendering
        if (empty($out)) $out = $this->default_theme_block($Block);
        return $out;
    }
    
/**
 * Parse string for special placeholders
 * placeholder example: [hook_function param1=text param=2 param3=0 ... /]
 *                      [other_hook_function]only content & no params[/other_hook_function]
 *
 * @return string HTML
 */	
    public function hookTags($text){
        $text = $this->specialTags($text);
        $tags = implode('|', $this->events);
        return preg_replace_callback('/(.?)\[(' . $tags . ')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', array($this, '__doHookTag'), $text);
    }
    
    private function __doHookTag($m){
        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' )
            return substr($m[0], 1, -1);

        $tag = $m[2];
        $attr = $this->__hookTagParseAtts( $m[3] );
        $hook = isset($this->eventMap[$tag]) ? $this->eventMap[$tag] : false;
        if ( $hook ){
            $hook =& $this->{$hook};
            if ( isset( $m[5] ) ) {
                // enclosing tag - extra parameter
                return $m[1] . call_user_func(array($hook, $tag), $attr, $m[5], $tag) . $m[6];
            } else {
                // self-closing tag
                return $m[1] . call_user_func(array($hook, $tag), $attr, null, $tag) . $m[6];
            }
        }
        return false;
    }
    
    private function __hookTagParseAtts($text) {
        $atts       = array();
        $pattern    = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text       = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3]))
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5]))
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) and strlen($m[7]))
                    $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8]))
                    $atts[] = stripcslashes($m[8]);
            }
        } else {
            $atts = ltrim($text);
        }
        return $atts;
    }    
    
/**
 * replace some core useful tags:
 * [date=FORMAT] -> return date(FORMAT)
 * [language.OPTION] -> current language option (code, name, native, direction)
 * [language] -> shortcut to [language.code] wich return current language code
 * [url]YourURL[/url] or [url=YourURL] -> formatted url
 * [url=LINK]LABEL[/url] -> <href="LINK">LABEL</a>
 * [t=stringToTranslate] or [t]stringToTranslate[/t] -> text translation: __t(stringToTranslate)
 * [t=domain@@stringToTranslate] -> text translation by domain __d(domain, stringToTranslate)
 *
 * @param string $text original text to replace tags
 * @return string
 */	
    public function specialTags($text){
        // [locale]
        $text = str_replace('[language]', Configure::read('Variable.language.code'), $text);
        
        //[locale.OPTION]
        preg_match_all('/\[language.(.+)\]/iUs', $text, $localeMatches);
        foreach ( $localeMatches[1] as $attr )
            $text = str_replace("[language.{$attr}]", Configure::read('Variable.language.' .$attr ), $text );

        //[url]URL[/url]
        preg_match_all('/\[url\](.+)\[\/url\]/iUs', $text, $urlMatches);
        foreach ( $urlMatches[1] as $url )
            $text = str_replace("[url]{$url}[/url]", $this->_View->Html->url($url, true), $text );
        
        //[url=URL]
        preg_match_all('/\[url\=(.+)\]/iUs', $text, $urlMatches);
        foreach ( $urlMatches[1] as $url )
            $text = str_replace("[url={$url}]", $this->_View->Html->url($url, true), $text );
        
        //[t=text to translate]
        preg_match_all('/\[t\=(.+)\]/iUs', $text, $tMatches);
        foreach ( $tMatches[1] as $string )
            $text = str_replace("[t={$string}]", __t($string), $text );

        //[t]text to translate[/t]
        preg_match_all('/\[t\](.+)\[\/t\]/iUs', $text, $tMatches);
        foreach ( $tMatches[1] as $string )
            $text = str_replace("[t]{$string}[/t]", __t($string), $text );

        //[t=domain@@text to translate]
        preg_match_all('/\[t\=(.+)\@\@(.+)\]/iUs', $text, $dMatches);
        foreach ( $dMatches[1] as $key => $domain )
            $text = str_replace("[d={$domain}@@{$dMatches[2][$key]}]", __d($domain, $dMatches[2][$key]), $text );
            
        //[date=FORMAT@@TIME_STAMP]
        preg_match_all('/\[date\=(.+)\@\@(.+)\]/iUs', $text, $dateMatches);
        foreach ( $dateMatches[1] as $key => $format ){
            $stamp = $dateMatches[2][$key];
            $replace = is_numeric($stamp) ? date($format, $stamp) : date($format, strtotime($stamp) );
            $text = str_replace("[date={$format}@@{$stamp}]", $replace, $text);
        }
            
        //[date=FORMAT]
        preg_match_all('/\[date\=(.+)\]/iUs', $text, $dateMatches);
        foreach ( $dateMatches[1] as $format )
            $text = str_replace("[date={$format}]", date($format), $text);
        
        # pass text to modules so they can apply their own special tags
        $this->hook('specialTags_alter', $text);
        
        return $text;
    }
	
/**
 * Default hooks, used in case of no response
 *
 * @return string HTML rendered elements
 */	
	public function default_theme_block($Block){
		return $this->_View->element('default_theme_block', array('block' => $Block) );
	}
    
    public function default_theme_breadcrumb($crumb){
        return $this->_View->element('default_theme_breadcrumb', array('crumbs' => $crumb) );
    }
	
    public function default_renderNode($node){
        return $this->_View->element('default_renderNode', array('node' => $node) ); #could not render node
    }
	
/**
 * Evaluate a string of PHP code.
 *
 * This is a wrapper around PHP's eval(). It uses output buffering to capture both
 * returned and printed text. Unlike eval(), we require code to be surrounded by
 * <?php ?> tags; in other words, we evaluate the code as if it were a stand-alone
 * PHP file.
 *
 * Using this wrapper also ensures that the PHP code which is evaluated can not
 * overwrite any variables in the calling code, unlike a regular eval() call.
 *
 * @param string $code The code to evaluate.
 * @return
 *   A string containing the printed output of the code, followed by the returned
 *   output of the code.
 *
 * @ingroup php_wrappers
 */
    private function __php_eval($code) {
      ob_start();
      print eval('?>' . $code);
      $output = ob_get_contents();
      ob_end_clean();
      return $output;
    }
  
/**
 * Check if a path matches any pattern in a set of patterns.
 *
 * @param $path The path to match.
 * @param $patterns String containing a set of patterns separated by \n, \r or \r\n.
 *
 * @return Boolean value: TRUE if the path matches a pattern, FALSE otherwise.
 */
    private function __matchPath($patterns, $path = false) {
        if ( empty($patterns) )
            return false;
        $path = !$path ? $this->_View->here : $path;
        $path = str_replace($this->_View->base, '', $path);
        $patterns = explode("\n", $patterns);
        foreach($patterns as &$p){
            $p = Router::url('/') . $p;
            $p = str_replace('//', '/', $p);
            $p = str_replace($this->_View->base, '', $p);
        }
        
        $patterns = implode("\n", $patterns);
        
        // Convert path settings to a regular expression.
        // Therefore replace newlines with a logical or, /* with asterisks and the <front> with the frontpage.
        $to_replace = array(
            '/(\r\n?|\n)/', // newlines
            '/\\\\\*/',     // asterisks
            '/(^|\|)\/($|\|)/' // front '/'
        );

        $replacements = array(
            '|',
            '.*',
            '\1' . preg_quote(Router::url('/'), '/') . '\2'
        );
        
        $patterns_quoted = preg_quote($patterns, '/');
        $regexps[$patterns] = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
        return (bool) preg_match($regexps[$patterns], $path);
    }
}