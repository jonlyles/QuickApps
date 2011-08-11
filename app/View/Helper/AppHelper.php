<?php 
App::uses('Helper', 'View');
class AppHelper extends Helper {

    var $hooks = array();
    var $events = array();
    var $listeners = array();
    var $eventMap = array(); # function_name => Helper, useful for hookTags() searching

	var $helpers = array(
		'Layout',	
		'Menu',		# menu helper
        'Form' => array('className' => 'QaForm'),
        'Html' => array('className' => 'QaHtml'),
        'Session',
		'Js'
    );
	
   function __construct(View $View, $settings = array()) {
        $this->__loadHooks();
        parent::__construct($View, $settings = array());
    }

	function beforeRender(){
		$this->__loadHookEvents();
		return true;
	}
    
    function attachModuleHooks($plugin){
        $Plugin = Inflector::camelize($plugin);
        if ( isset($this->listeners[$Plugin . 'Hook']) )
            return;
        $folder = new Folder;
        $folder->path = CakePlugin::path($Plugin) . 'View' . DS . 'Helper' . DS;
        $files = $folder->find('(.*)Hook(Helper)\.php');
        foreach ( $files as $helper){
            $helper = str_replace('Helper.php', '', $helper);
            $this->hooks[] = "{$Plugin}.{$helper}";
            $this->$helper = $this->_View->loadHelper("{$Plugin}.{$helper}" , array('plugin' => $plugin) );
            if ( !is_object($this->{$helper}) )
                continue;
            $methods = array();
            $_methods = get_this_class_methods($this->{$helper});
            foreach ($_methods as $method)
                $methods[] = $method;
            $this->listeners[$helper] = $methods;
            $this->events = array_merge($this->events, $methods);
        }
    }
	
    function deattachModuleHooks($plugin){
        $Plugin = Inflector::camelize($plugin);
        
        foreach ( $this->hooks as $hk => $hook){
            if ( strpos($hook, "{$Plugin}.") === false )
                continue;
            $Hook = str_replace("{$Plugin}.", '', $hook);
            foreach ( $this->listeners[$Hook] as $event ){
                unset($this->events[array_search($event, $this->events)]);
            }
            unset($this->hooks[$hk]);
            unset($this->listeners[$Hook]);
            unset($this->{$Hook});
        }
    }
	
/**
 * Chech if hook exists
 *
 * @param string $hook Name of the hook to check
 * 
 * @return boolean
 */
	function hook_defined($hook){
		return ( in_array($hook, $this->events) == true );
	}
	
	function hook($event, &$data = array(), $options = array()) {
		$result = $this->__dispatchEvent($event, $data, $options);
		return $result;
	}
	
/**
 * Dispatch Helper-hooks from all the plugins and core
 *
 * @param string $hook Name of the hook to trigger
 * @param array $data Any data to pass to the hook function
 * @param array $options
 * 
 * @return mixed result array if collectReturn is set to true or NULL in case of no response
 */
    function __dispatchEvent($event, &$data = array(), $options = array()) {
		$options = array_merge(
			array(
				'break' => false,
				'breakOn' => false,
				'collectReturn' => false,
				'alter' => true
			),
			(array)$options
		);
        
        # protect original varriable
        if (!$options['alter']) $_data = $data;
        
		$collected = array();
		if ( !$this->hook_defined($event) ) return null;
        
		foreach ($this->listeners as $object => $methods) {
			foreach ( $methods as $method){
				if ( $method == $event && is_callable(array($this->{$object}, $method)) ) {
                    if (!$options['alter']){
                        $result = call_user_func(array($this->{$object}, $event), $_data);
                    } else {
                        $result = call_user_func(array($this->{$object}, $event), &$data);
                    }
                    if ($options['collectReturn'] === true) {
                        $collected[] = $result;
                    }
                    if (
                        $options['break'] && ($result === $options['breakOn'] ||
                        (is_array($options['breakOn']) && in_array($result, $options['breakOn'], true)))
                    ) {
                        return $result;
                    }
				}
			}
		}
        if(empty($collected) && empty($result)) return null;
        return $options['collectReturn'] ? $collected : $result;    
	}	
	
	function __loadHookEvents(){
        $eventMap = array();
        
		foreach ($this->helpers as $helper ){
			if ( is_array($helper) )
				continue;
				
			$helper = strpos($helper, '.') !== false ? substr($helper, strpos($helper, '.')+1) : $helper;
			if ( strpos($helper, 'Hook') !== false ){
				if ( !is_object($this->{$helper}) )
					continue;
				$methods = array();
				$_methods = get_this_class_methods($this->{$helper});
				foreach ($_methods as $method){
					$methods[] = $method;
                    $eventMap[$method] = (string)$helper;
                }
				$this->listeners[$helper] = $methods;
				$this->events = array_merge($this->events, $methods);
				$this->eventMap = array_merge($this->eventMap, $eventMap);
			}
		}	
	}	

    function __loadHooks() {
        if (  $hooks = Configure::read('Hook.helpers') ) {
            foreach ($hooks as $hook) {
                if ( strpos($hook, '.') !== false ) {
					$hookE = explode('.', $hook);
					$plugin = $hookE[0];
					$hookHelper = $hookE[1];
					$filePath = App::pluginPath($plugin) . 'View' . DS . 'Helper' . DS . "{$hookHelper}Helper" . '.php';
                } else {
                    $filePath = APP . 'View' . DS . 'Helper' . DS . "{$hook}Helper.php";
                }
				
                if (file_exists($filePath)) {
                    $this->hooks[] = $hook;
					$this->helpers[] = $hook;
                }
            }
        }
		$this->helpers = array_unique($this->helpers);
    }
 
}