<?php
/**
 * Application Controller
 *
 * PHP version 5
 *
 * @package  QuickApps.Controller
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class AppController extends Controller {
    public $view = 'Theme';
    public $theme = 'default';
    
    public $Layout = array(
        'feed' => null, # url to rss feed
        'blocks' => array(),
        'node' => array(),
        'viewMode' => '', # full, list
        'header' => array(), # extra code for header
        'footer' => array(), # extra code for </body>
        'stylesheets' => array(
            'all' => array(),
            'braille' => array(),
            'embossed' => array(),
            'handheld' => array(),
            'print' => array(),
            'projection' => array(),
            'screen' => array(),
            'speech' => array(),
            'tty' => array(),
            'tv' => array(),
            'embed' => array()
        ),
        'javascripts' => array(
            'embed' => array(),
            'file' => array('jquery.js', 'quickapps.js')
        ),
        'meta' => array() # meta tags for layout
    );
    
    public $helpers = array(
        'Layout',    
        'Form' => array('className' => 'QaForm'),
        'Html' => array('className' => 'QaHtml'),
        'Session',
        'Cache',
        'Js',
        'Time'
    );
    
    public $uses = array(
        'System.Variable',
        'System.Module',
        'Menu.MenuLink',
        'Locale.Language'
    );

    public $components = array(
        'Session', 
        'Cookie', 
        'RequestHandler', 
        'Hook',
        'Acl',
        'Auth',
        'Installer'
    );

    public function __construct($request = null, $response = null) {
        $this->__preloadHooks();
        parent::__construct($request, $response);
    }

    public function beforeFilter() {
        $this->_accessCheck();
        $this->_loadVariables();
        $this->_loadModules();
        $this->_setTheme();
        $this->_setTimeZone();
        $this->_setLanguage();
        $this->_prepareContent();
        $this->setCrumb();
        $this->_siteStatus();
        return true;
    }

    public function beforeRender() {
        if ($this->Layout['feed']) {
            $this->Layout['meta']['link'] = $this->Layout['feed'];
        }
        
        $this->set('Layout', $this->Layout);
        if ($this->name == 'CakeError') {
            $this->beforeFilter();
            $this->layout = 'error';
        }
        return true;
    }

/**
 * shortcut for $this->set(`title_for_layout`...)
 *
 * @param string $str layout title
 *
 * @return void
 */
    public function title($str) {
        $this->set('title_for_layout', $str);
    }
    
/**
 * shortcut for Session setFlash
 *
 * @param string $msg mesagge to display
 * @param string $class type of message: error, success, alert, bubble
 *
 * @return void
 */
    public function flashMsg($msg, $class) {
        $this->Session->setFlash($msg, 'default', array('class' => $class));
    }
    
/**
 * Insert custom block in stack
 *
 * @param array $data formatted block array
 * @param string $region theme region where to push
 *
 * @return boolean
 */
    public function blockPush($block = array(), $region = null, $show_on = true) {
        if (!$show_on) {
            return;
        }
        
        $_block = array(
            'title' => '', 
            'pages' => '',
            'visibility' => 0,
            'body' => '', #
            'region' => null, 
            'format' => null #
        );

        $block = array_merge($_block, $block);
        $block['module'] = null;
        $block['id'] = null;
        $block['delta'] = null;

        if (!is_null($region)) {
            $block['region'] = $region;
        }

        if (empty($block['region']) || empty($block['body'])) {
            return false;
        }

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
                    'theme' => $this->theme,
                    'region' => $block['region']
                )
            )
        );

        $this->Layout['blocks'][] = $Block;
    }
    
/**
 * Wrapper method to Hook::hook_defined
 *
 * @param string $hook Name of the event
 * 
 * @return bool
 */
    public function hook_defined($hook) {
        return $this->Hook->hook_defined($hook);
    }
    
/**
 * Wrapper method to Hook::__dispatchEvent
 *
 * @param string $hook Name of the event
 * @param mix $data Any data to attach
 * @param bool $raw_return false means return asociative data, true will return a listed array
 * 
 * @return mixed FALSE -or- result array
 */
    public function hook($hook, &$data = array(), $options = array()) {
        return $this->Hook->hook($hook, &$data, $options);
    }
    
/**
 * Set crumb from url parse or add url to the links list
 *
 * @param mixed $url if is array then will push de formated array to the crumbs list
 *                   else will set base crum from string parsing
 * 
 * @return void
 */
    public function setCrumb($url = false) {
        if (is_array($url) && !empty($url)) {
            if (is_array($url[0])) {
                foreach ($url as $link) {
                    if (empty($link)) {
                        continue;
                    }
                    
                    $push = array(
                        'MenuLink' => array(
                            'link_title' => $link[0],
                            'router_path' => ( empty($link[1]) ? 'javascript:return false;': $link[1] ),
                            'description' => (isset($link[2]) ? $link[2] : ''),
                        )
                    );
                    
                    $this->viewVars['breadCrumb'][] = $push;
                }
            } else {
                $push = array(
                    'MenuLink' => array(
                        'link_title' => $url[0],
                        'router_path' => ( empty($url[1]) ? 'javascript:return false;': $url[1] ),
                        'description' => (isset($url[2]) ? $url[2] : ''),
                    )
                );
                $this->viewVars['breadCrumb'][] = $push;
            }
            return;
        } else {
            $url = !is_string($url) ? $this->__getUrl() : $url;
        }
         
        if (is_array($url)) {
            foreach ($url as $k => $u) {
                $url[$k] = preg_replace('/\/{2,}/', '',  "{$u}//");
            }
        } else {
            $url = preg_replace('/\/{2,}/', '',  "{$url}//");
        }

        $this->set('breadCrumb', array());
        $current = $this->MenuLink->find('first', 
            array(
                'conditions' => array(
                    'MenuLink.router_path' => ( empty($url) ? 'javascript:return false;': $url )
                )
            )
        );

        if (empty($current)) {
            return;
        }
        
        $this->MenuLink->Behaviors->detach('Tree');
        $this->MenuLink->Behaviors->attach('Tree', array('parent' => 'parent_id', 'left' => 'lft', 'right' => 'rght', 'scope' => "MenuLink.menu_id = '{$current['MenuLink']['menu_id']}'"));
        
        $path = $this->MenuLink->getPath($current['MenuLink']['id']);
        if (isset($path[0]['MenuLink']['link_title'])) {
            $path[0]['MenuLink']['link_title'] = __t($path[0]['MenuLink']['link_title']);
        }
        $this->set('breadCrumb', $path);
    }
    
    protected function _siteStatus() {
        if (Configure::read('Variable.site_online') != 1 && !$this->__isAdmin()) {
            if (
                $this->plugin != 'user' && 
                $this->request->params['controller'] != 'log' && 
                !in_array($this->request->params['controller'], array('login', 'logout') ) 
           ) {
                # TODO: site down throw
                //throw new NotFoundException(__t('Site offline'), 503);
            }
        }
    }
    
    protected function _setTheme() {
        #set theme:
        if (isset($this->request->params['admin']) && $this->request->params['admin'] == 1) {
            $this->theme =  Configure::read('Variable.admin_theme') ? Configure::read('Variable.admin_theme') : 'admin_default';
        } else {
            $this->theme =  Configure::read('Variable.site_theme') ? Configure::read('Variable.site_theme') : 'default';
        }
        
        $this->layout    ='default';
        $this->viewClass= 'Theme';
        
        if (file_exists(APP . 'View' . DS . 'Themed' . DS . $this->theme . DS . "{$this->theme}.yaml")) {
            $yaml = Spyc::YAMLLoad(APP . 'View' . DS . 'Themed' . DS . $this->theme . DS . "{$this->theme}.yaml");
            $yaml['info']['folder'] = $this->theme;
            $yaml['settings'] = Configure::read('Modules.' . Inflector::underscore("Theme{$this->theme}") . '.settings');
            
            # set custom or default logo
            $yaml['settings']['site_logo_url'] = isset($yaml['settings']['site_logo_url']) && !empty($yaml['settings']['site_logo_url']) ? $yaml['settings']['site_logo_url'] : '/img/logo.png';
            
            # set custom or default favicon
            $yaml['settings']['site_favicon_url'] = isset($yaml['settings']['site_favicon_url']) && !empty($yaml['settings']['site_favicon_url']) ? $yaml['settings']['site_logo_url'] : '/favicon.ico';
            
            Configure::write('Theme', $yaml);
            foreach ($yaml['stylesheets'] as $media => $files) {
                if (!isset($this->Layout['stylesheets'][$media])){
                    $this->Layout['stylesheets'][$media] = array();
                }
                
                foreach ($files as $file) {
                    $this->Layout['stylesheets'][$media][] = $file;
                }
            }
        }
        
        if (Configure::read('Theme.layout')) {
            $this->layout = Configure::read('Theme.layout');
        }
        
        $this->hook('stylesheets_alter', $this->Layout['stylesheets']);    # pass css list to modules if they need to alter them (add/remove)
    }
    
    protected function _prepareContent() {
        $theme = Router::getParam('admin') ? Configure::read('Variable.admin_theme') : Configure::read('Variable.site_theme');
        $options = array( 
            'conditions' => array(
                'Block.themes_cache LIKE' => "%{$theme}%", # only blocks assigned to current theme
                'Block.status' => 1,
                'OR' => array( # only blocks assigned to any/current language
                    'Block.locale = ' => null,
                    'Block.locale =' => '',
                    'Block.locale LIKE ' => '%s:2:"' . Configure::read('Variable.language.code') . '"%',
                    'Block.locale' => 'a:0:{}'
                )
            )
        );
        
        $this->Layout['blocks'] = $this->hook('blocks_list', $options, array('alter' => false, 'collectReturn' => false)); # request blocks to block module
        $this->hook('blocks_alter', $this->Layout['blocks']); # pass blocks to modules
        
        /* Basic js files/embed */
        $this->Layout['javascripts']['embed'][] = '
jQuery.extend(QuickApps.settings, {
    "url": "' . str_replace("//", "/", $this->here . '/') . '", 
    "base_url": "' . Router::url('/') . '",
    "locale": {"code": "' . Configure::read('Variable.language.code') . '"}
} );
';
        
        $this->hook('javascripts_alter', $this->Layout['javascripts']);    # pass js to modules
        $this->paginate = array('limit' => Configure::read('Variable.rows_per_page') );
        Configure::write('Variable.qa_version', Configure::read('Modules.system.yaml.version') );
        
        $defaultMetaDescription = Configure::read('Variable.site_description');
        if (!empty($defaultMetaDescription)){
            $this->Layout['meta']['description'] = $defaultMetaDescription;
        }
            
        #auto favicon meta
        if (Configure::read('Theme.settings.site_favicon')) {
            $faviconURL = Configure::read('Theme.settings.site_favicon_url');
            $this->Layout['meta']['icon'] = $faviconURL && !empty($faviconURL) ? Router::url($faviconURL) : '/favicon.ico';
        }
    }
    
    protected function _setLanguage() {
        $langs           = $this->Language->find('all', array('conditions' => array('status' => 1), 'order' => array('ordering' => 'ASC')  ) );
        $installed_codes = Set::extract('/Language/code', $langs);
        
        $lang = $this->Session->read('language');
        $lang = isset($this->request->params['named']['lang']) ? $this->request->params['named']['lang'] : $lang;
        $lang = isset($this->request->query['lang']) && !empty($this->request->query['lang']) ? $this->request->query['lang'] : $lang;
        $lang = empty($lang) ? Configure::read('Variable.default_language') : $lang;
        $lang = empty($lang) || !in_array($lang, $installed_codes) || strlen($lang) != 3 ? 'eng' : $lang;
        
        $this->Session->write('language', $lang);
        $_lang = Set::extract("/Language[code={$lang}]/..", $langs);
        if (!isset($_lang[0]['Language'])) { # not defined -> default = english
            $_lang[0]['Language'] = array(
                'code' => 'eng',
                'name' => 'English',
                'native' => 'English',
                'direction' => 'ltr'
            );
        }
        
        Configure::write('Variable.language', $_lang[0]['Language']);
        Configure::write('Variable.languages', $langs);
        Configure::write('Config.language', Configure::read('Variable.language.code') );
    }
    
    protected function _accessCheck() {
        $this->Auth->authenticate = array(
            'Form' => array(
                'fields' => array(
                    'username' => 'username',
                    'password' => 'password'
                ),
                'userModel' => 'User.User',
                'scope' => array('User.status' => 1)
            )
        );
        
        $this->Auth->loginAction = array(
            'controller' => 'user',
            'action' => 'login',
            'plugin' => 'user'
        );
        
        $this->Auth->loginRedirect = Router::getParam('admin') ? '/admin' : '/';
        $this->Auth->logoutRedirect = $this->Auth->loginRedirect;
        $this->Auth->allowedActions = array('login', 'logout');
        
        $cookie = $this->Cookie->read('UserLogin');
        if (!$this->Auth->user() && 
            isset($cookie['id']) && 
            !empty($cookie['id']) && 
            isset($cookie['password']) && 
            !empty($cookie['password'])
        ) {
            $this->loadModel('User.User');
            $this->User->unbindFields();
            $user = $this->User->find('first', 
                array(
                    'conditions' => array(
                        'User.id' => @$cookie['id'],
                        'User.password' => @$cookie['password']
                    )
                )
            );
            
            $this->User->bindFields();
            
            if ($user) {
                $this->loadModel('UsersRole');
                $session = $user['User'];
                $session['role_id'] = $this->UsersRole->find('all', 
                    array(
                        'conditions' => array('UsersRole.user_id' => $user['User']['id']),
                        'fields' => array('role_id', 'user_id')
                    )
                );
                $session['role_id'] = Set::extract('/UsersRole/role_id', $session['role_id']);
                $session['role_id'][] = 2; #role: authenticated user
                $this->Auth->login($session);
                return true;
            }
        }        
        
        if ($this->__isAdmin()) {
            $this->Auth->allowedActions = array('*');
        } else {
            $roleId = $this->Auth->user() ? $this->Auth->user('role_id') : 3; # 3: anonymous user (public)
            $aro = $this->Acl->Aro->find('first', 
                array(
                    'conditions' => array(
                        'Aro.model' => 'User.Role',
                        'Aro.foreign_key' => $roleId, # roles! array of ids
                    ),
                    'recursive' => -1,
                )
            );
            
            $aroId = $aro['Aro']['id'];
            
            # get current plugin ACO
            $pluginNode = $this->Acl->Aco->find('first', 
                array(
                    'conditions' => array(
                        'Aco.alias' => $this->params['plugin'], 
                        'parent_id = ' => null
                    ),
                    'fields' => array('alias', 'id')
                )
            );
            
            # get plugin controllers ACOs
            $thisControllerNode = $this->Acl->Aco->find('first', 
                array(
                    'conditions' => array(
                        'alias' => $this->name,
                        'parent_id' => $pluginNode['Aco']['id']
                    )
                )
            );
            if ($thisControllerNode) {
                $thisControllerActions = $this->Acl->Aco->find('list', 
                    array(
                        'conditions' => array(
                            'Aco.parent_id' => $thisControllerNode['Aco']['id'],
                        ),
                        'fields' => array(
                            'Aco.id',
                            'Aco.alias',
                        ),
                        'recursive' => -1,
                    )
                );
                $thisControllerActionsIds = array_keys($thisControllerActions);
                $allowedActions = $this->Acl->Aco->Permission->find('list', 
                    array(
                        'conditions' => array(
                            'Permission.aro_id' => $aroId,
                            'Permission.aco_id' => $thisControllerActionsIds,
                            'Permission._create' => 1,
                            'Permission._read' => 1,
                            'Permission._update' => 1,
                            'Permission._delete' => 1,
                        ),
                        'fields' => array('id', 'aco_id'),
                        'recursive' => -1,
                    )
                );
                $allowedActionsIds = array_values($allowedActions);
            }
            
            $allow = array();
            if (isset($allowedActionsIds) &&
                is_array($allowedActionsIds) &&
                count($allowedActionsIds) > 0
            )
            foreach ($allowedActionsIds as $i => $aId){
                $allow[] = $thisControllerActions[$aId];
            }
            $this->Auth->allowedActions = array_merge($this->Auth->allowedActions, $allow);    
       }
    }
    
    protected function _setTimeZone() {
        return date_default_timezone_set(Configure::read('Variable.date_default_timezone'));
    }
    
    protected function _loadVariables() {
        $variables = Cache::read('Variable');
        if ($variables === false) {
            $this->Variable->writeCache();
        } else {
            Configure::write('Variable', $variables);
        }
    }
    
    protected function _loadModules() {
        $modules = Cache::read('Modules');
        if ($modules === false) {
            $modules = $this->Module->find('all', array('recursive' => -1) );
            foreach ($modules as $m) {
                $v = $m['Module'];
                CakePlugin::load($m['Module']['name']);
                $v['path'] = App::pluginPath($m['Module']['name']);
                $yamlFile = (strpos($m['Module']['name'], 'theme_') !== false) ? dirname(dirname($v['path'])) . DS . basename(dirname(dirname($v['path']))) . '.yaml' : $v['path'] . "{$m['Module']['name']}.yaml";
                $v['yaml'] = file_exists($yamlFile) ? Spyc::YAMLLoad($yamlFile) : array();   
                Configure::write('Modules.' . $m['Module']['name'], $v);
            }
            Cache::write('Modules', Configure::read('Modules') );
        } else {
            Configure::write('Modules', $modules);
        }
    }
    
    private function __getUrl() {
        $url = '/' . $this->request->url;
        $out = array();
        
        $out[] = $url;
        foreach ($this->request->params['named'] as $key => $val) {
            $url = $this->__str_replace_once("/{$key}:{$val}", '', $url);
            $out[] = $url;
        }
        
        $out[] = $url;
        
        if ($this->request->params['controller'] == $this->plugin) {
            $url =  $this->__str_replace_once("/{$this->request->params['controller']}", '', $url);
            $out[] = $url;
        } else if ($this->request->params['action'] == 'index' || $this->request->params['action'] == 'admin_index') {
            $url =  $this->__str_replace_once("/index", '', $url);
            $out[] = $url;
        }
        
        foreach ($this->request->params['pass'] as $p) {
            $url = $this->__str_replace_once("/{$p}", '', $url);
            $out[] = $url;
        }
        return array_unique($out);
    }
    
    private function __str_replace_once($str_pattern, $str_replacement, $string) {
        if (strpos($string, $str_pattern) !== false) {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }
        return $string;
    }
    
    function __isAdmin() {
        return ($this->Auth->user() && in_array(1, $this->Auth->user('role_id')));
    }
    
/*
 * Load and attach hooks to AppController. (Hooks can be Components, Helpers, Behaviours)
 *  - Preload helpers hooks
 *  - Preload behaviors hooks
 *  - Preload components hooks
 *
 * @return void
 */
    private function __preloadHooks() {
        $paths = $c = $h = $b = array();
        
        // load current theme hooks only
        $_cache = Cache::read('Variable');
        $_themeType = Router::getParam('admin') ? 'admin_theme' : 'site_theme';
        if (!$_cache) {
            if (!isset($this->Variable)) {
                $this->loadModel('System.Variable');
            }
            $q = $this->Variable->find('first', array('conditions' => array('Variable.name' => $_themeType) ) );
        }
        $themeToUse = !$_cache ? $q['Variable']['value'] : $_cache[$_themeType];
        $plugins = App::objects('plugin', null, false);
        foreach ($plugins as $plugin) {
            $ppath = CakePlugin::path($plugin);
            $modulesCache = Cache::read('Modules');
            $_plugin = Inflector::underscore($plugin);
            if ((isset($modulesCache[$_plugin]['status']) && $modulesCache[$_plugin]['status'] == 0) || 
                    (
                        strpos($ppath, DS . 'View' . DS . 'Themed') !== false && 
                        strpos($ppath, 'Themed' . DS . $themeToUse . DS . 'Plugin') === false
                    )
           ) {
                continue; # Important: skip no active themes
            }
            $paths["{$plugin}_components"] =  $ppath . 'Controller' . DS . 'Component' . DS;
            $paths["{$plugin}_behaviors"] = $ppath . 'Model' . DS . 'Behavior' . DS;
            $paths["{$plugin}_helpers"] = $ppath . 'View' . DS . 'Helper' . DS;
        }
        
        $paths = array_merge(
            array(    
                APP . 'Controller' . DS . 'Components' . DS,    # core components
                APP . 'View' . DS . 'Helper' . DS,              # core helpers
                APP . 'Model' . DS . 'Behavior' . DS            # core behaviors
            ),
            (array)$paths
        );
        
        $folder = new Folder;

        foreach ($paths as $key => $path) {
            $folder->path = $path;
            $files = $folder->find('(.*)Hook(Component|Behavior|Helper)\.php');
            $plugin = is_string($key) ? explode('_', $key) : false;
            $plugin = is_array($plugin) ? $plugin[0] : $plugin;
        
            foreach ($files as $file) {
                $prefix = ($plugin) ? Inflector::camelize($plugin) . '.' : '';
                $hook = $prefix . Inflector::camelize(str_replace(array('.php'), '', basename($file)));
                $hook = str_replace(array('Component', 'Behavior', 'Helper'),'', $hook);
                if (strpos($path, 'Helper')) {
                    $h[] = $hook;
                    $this->helpers[] = $hook;
                } elseif (strpos($path, 'Behavior')) { 
                    $b[] = $hook;
                } else {
                    $c[] = $hook;
                    $this->components[] = $hook;
                }
            }
        }
        $h[] = 'CustomHooks'; # merge custom hooktags helper
        Configure::write('Hook.components', $c);
        Configure::write('Hook.behaviors', $b);
        Configure::write('Hook.helpers', $h);
        
        if (!$_cache) { # 'weird' fix, complicated explanation
            ClassRegistry::flush();
            unset($this->Variable);
        }
    }
}