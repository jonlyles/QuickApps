<?php 
/**
 * Application Model
 *
 * PHP version 5
 *
 * @category Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class AppModel extends Model {
	public $cacheQueries = false;
	public $actsAs = array(
        'WhoDidIt' => array(
            'auth_session' => 'Auth.User.id', 
            'user_model' => 'User.User'
        )
    ); 

	public $listeners = array();
	public $events = array();
	
	public function __construct($id = false, $table = null, $ds = null) {
		$this->__loadHooks();
		parent::__construct($id, $table, $ds);
		$this->__loadHookEvents();
    }
	
/**
 * Wrapper method to $this->dispatchEvent()
 *
 * @param string $hook Name of the event
 * @param mix $data Any data to attach
 * @param bool $raw_return false means return asociative data, true will return a listed array
 * 
 * @return mixed FALSE -or- result array
 */
	public function hook($hook, &$data = array(), $options = array()) {
		return $this->__dispatchEvent($hook, $data, $options);
	}
	
/**
 * Chech if hook exists
 *
 * @param string $hook Name of the hook to check
 * 
 * @return bool
 */
	public function hook_defined($hook) {
		return (in_array($hook, $this->events) == true);
	}
	
/**
 * Translate validation messages
 *
 */
	public function invalidate($field, $value = true) {
		return parent::invalidate($field, __t($value));
	}
	
/**
 * Utility function
 *
 */
	public function optimize() {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$tablename = $db->fullTableName($this);
		if (!empty($tablename)) {
            return $db->query('OPTIMIZE TABLE ' . $tablename . ';');
		} else {
            return false;
		}
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
	private function __dispatchEvent($event, &$data = array(), $options = array()) {
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
        if (!$options['alter']) {
            $_data = $data;
        }
        
		$collected = array();
		if (!$this->hook_defined($event)) {
            return null;
        }
        
		foreach ($this->listeners as $object => $methods) {
			foreach ($methods as $method) {
				if ($method == $event && is_callable(array($this->Behaviors->{$object}, $method))) {
                    if (!$options['alter']) {
                        $result = call_user_func(array($this->Behaviors->{$object}, $event), $_data);
                    } else {
                        $result = call_user_func(array($this->Behaviors->{$object}, $event), &$data);
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
        
        if (empty($collected) && empty($result)) {
            return null;
        }
        
        return $options['collectReturn'] ? $collected : $result;    
	}
    
	private function __loadHooks() {
        $b = Configure::read('Hook.behaviors');
        
        if (!$b){
            return false; # fix for AppController __preloadHooks()
        }
        
		foreach ($b as $hook) {
			$this->actsAs[$hook] = array();
		}
	}
	
	private function __loadHookEvents() {
		foreach ($this->actsAs as $behavior => $b_data) {
			$behavior = strpos($behavior, '.') !== false ? substr($behavior, strpos($behavior, '.')+1) : $behavior;
			if (strpos($behavior, 'Hook')) {
				$methods = array();
				$_methods = get_this_class_methods($this->Behaviors->{$behavior});
				foreach ($_methods as $method) {
					$methods[] = $method;
				}

				$this->listeners[$behavior] = $methods;
				$this->events = array_merge($this->events, $methods);
			}
		}
		
		$this->events = array_unique($this->events);
		return true;	
	}
}