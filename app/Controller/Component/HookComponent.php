<?php
/**
 * Hook Component
 *
 * PHP version 5
 *
 * @package  QuickApps.Controller.Component
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class HookComponent extends Object {
	public $Controller;
	
/**
 * Array with loaded event listener classes
 *
 * @var array
 */
	public $listeners = array();
	public $events = array();
	public $eventMap = array();
	
	public function startup() { }
	public function beforeRender() { }
	public function shutdown() { }
    public function beforeRedirect() {}
	
/**
 * On every controller startup
 *
 * @param unknown_type $controller
 */
	public function initialize(&$Controller) {
		$this->Controller =& $Controller;
		$eventMap = array();
        
		foreach (Configure::read('Hook.components') as $component) {
			$component = strpos($component, '.') !== false ? substr($component, strpos($component, '.')+1) : $component;
			if (strpos($component, 'Hook')) {
				$methods = array();
				$_methods = get_this_class_methods($this->Controller->{$component});
				foreach ($_methods as $method) {
					$methods[] = $method;
                    $eventMap[$method] = (string)$component;
				}

				$this->listeners[$component] = $methods;
				$this->events = array_merge($this->events, $methods);
                $this->eventMap = array_merge($this->eventMap, $eventMap);
			}
		}
		
		$this->events = array_unique($this->events);
		return true;
	}
    
/**
 * Parse string for special placeholders
 * placeholder example: [hook_function param1=text param=2 param3=0 ... /]
 *                      [other_hook_function]only content & no params[/other_hook_function]
 *
 * @return string HTML
 */	
    public function hookTags($text) {
        $text = $this->specialTags($text);
        $tags = implode('|', $this->events);
        return preg_replace_callback('/(.?)\[(' . $tags . ')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)/s', array($this, '__doHookTag'), $text);
    }
    
    private function __doHookTag($m) {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']' )
            return substr($m[0], 1, -1);

        $tag = $m[2];
        $attr = $this->__hookTagParseAtts( $m[3] );
        $hook = isset($this->eventMap[$tag]) ? $this->eventMap[$tag] : false;
        if ($hook) {
            $hook =& $this->Controller->{$hook};
            if (isset( $m[5] )) {
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
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
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
    public function specialTags($text) {
        // [locale]
        $text = str_replace('[language]', Configure::read('Variable.language.code'), $text);
        
        //[locale.OPTION]
        preg_match_all('/\[language.(.+)\]/iUs', $text, $localeMatches);
        foreach ($localeMatches[1] as $attr )
            $text = str_replace("[language.{$attr}]", Configure::read('Variable.language.' .$attr ), $text );

        //[url]URL[/url]
        preg_match_all('/\[url\](.+)\[\/url\]/iUs', $text, $urlMatches);
        foreach ($urlMatches[1] as $url )
            $text = str_replace("[url]{$url}[/url]", $this->_View->Html->url($url, true), $text );
        
        //[url=URL]
        preg_match_all('/\[url\=(.+)\]/iUs', $text, $urlMatches);
        foreach ($urlMatches[1] as $url )
            $text = str_replace("[url={$url}]", $this->_View->Html->url($url, true), $text );
        
        //[t=text to translate]
        preg_match_all('/\[t\=(.+)\]/iUs', $text, $tMatches);
        foreach ($tMatches[1] as $string )
            $text = str_replace("[t={$string}]", __t($string), $text );

        //[t]text to translate[/t]
        preg_match_all('/\[t\](.+)\[\/t\]/iUs', $text, $tMatches);
        foreach ($tMatches[1] as $string )
            $text = str_replace("[t]{$string}[/t]", __t($string), $text );

        //[t=domain@@text to translate]
        preg_match_all('/\[t\=(.+)\@\@(.+)\]/iUs', $text, $dMatches);
        foreach ($dMatches[1] as $key => $domain )
            $text = str_replace("[d={$domain}@@{$dMatches[2][$key]}]", __d($domain, $dMatches[2][$key]), $text );
            
        //[date=FORMAT@@TIME_STAMP]
        preg_match_all('/\[date\=(.+)\@\@(.+)\]/iUs', $text, $dateMatches);
        foreach ($dateMatches[1] as $key => $format) {
            $stamp = $dateMatches[2][$key];
            $replace = is_numeric($stamp) ? date($format, $stamp) : date($format, strtotime($stamp) );
            $text = str_replace("[date={$format}@@{$stamp}]", $replace, $text);
        }
            
        //[date=FORMAT]
        preg_match_all('/\[date\=(.+)\]/iUs', $text, $dateMatches);
        foreach ($dateMatches[1] as $format )
            $text = str_replace("[date={$format}]", date($format), $text);
        
        # pass text to modules so they can apply their own special tags
        $this->hook('specialTags_alter', $text);
        
        return $text;
    }

/**
 * Chech if hook exists
 *
 * @param string $hook Name of the hook to check
 * 
 * @return boolean
 */
	public function hook_defined($hook) {
		return (in_array($hook, $this->events) == true);
	}
	
/**
 * Wrapper method to Hook::__dispatchEvent()
 *
 * @param string $hook Name of the event
 * @param array $data Any data to attach
 * @param bool $raw_return false means return asociative data, true will return a listed array
 * 
 * @return mixed FALSE -or- result array
 */
	public function hook($event, &$data = array(), $options = array()) {
		return $this->__dispatchEvent($event, &$data, $options);
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
        if (!$options['alter']) $_data = $data;
        
		$collected = array();
		if (!$this->hook_defined($event) ) return null;
        
		foreach ($this->listeners as $component => $methods) {
			foreach ($methods as $method) {
				if ($method == $event && is_callable(array($this->Controller->{$component}, $method))) {
                    if (!$options['alter']) {
                        $result = call_user_func(array($this->Controller->{$component}, $event), $_data);
                    } else {
                        $result = call_user_func(array($this->Controller->{$component}, $event), &$data);
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
        if (empty($collected) && empty($result)) return null;
        return $options['collectReturn'] ? $collected : $result;
	}
}