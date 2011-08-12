<?php
/**
 * Theme Helper
 * Theme: AdminDefault
 *
 * PHP version 5
 *
 * @package  Quickapps.Theme.AdminDefault.View.Helper
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class ThemeAdminDefaultHookHelper extends AppHelper {
	
	/**
	* Returns formated menu
	*
	* @return string HTML
	*/
	function theme_menu($menu) {
		$output = '';
		switch ($menu['region']) {
			case 'management-menu':
				$settings = array('id' => 'top-menu');
            break;
			
			case 'content':
				return $this->_View->element('content-menu', array('menu' => $menu), array('plugin' => 'ThemeAdminDefault') );
			break;
			
			default:
				$settings = array();
			break;
		}
        //$c = unserialize('a:9:{i:0;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:1:"1";s:10:"link_title";s:7:"Default";s:11:"router_path";s:7:"default";s:3:"lft";s:1:"1";s:4:"rght";s:1:"2";s:9:"parent_id";s:1:"0";s:10:"post_count";i:0;}}i:1;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"54";s:10:"link_title";s:5:"Venta";s:11:"router_path";s:5:"venta";s:3:"lft";s:1:"3";s:4:"rght";s:2:"10";s:9:"parent_id";s:1:"0";s:10:"post_count";i:4;}}i:2;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"63";s:10:"link_title";s:9:"Viviendas";s:11:"router_path";s:9:"viviendas";s:3:"lft";s:1:"4";s:4:"rght";s:1:"5";s:9:"parent_id";s:2:"54";s:10:"post_count";i:4;}}i:3;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"64";s:10:"link_title";s:15:"Otros Inmuebles";s:11:"router_path";s:15:"otros-inmuebles";s:3:"lft";s:1:"6";s:4:"rght";s:1:"7";s:9:"parent_id";s:2:"54";s:10:"post_count";i:0;}}i:4;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"65";s:10:"link_title";s:10:"Histórico";s:11:"router_path";s:9:"hist-rico";s:3:"lft";s:1:"8";s:4:"rght";s:1:"9";s:9:"parent_id";s:2:"54";s:10:"post_count";i:0;}}i:5;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"55";s:10:"link_title";s:8:"Alquiler";s:11:"router_path";s:8:"alquiler";s:3:"lft";s:2:"11";s:4:"rght";s:2:"18";s:9:"parent_id";s:1:"0";s:10:"post_count";i:7;}}i:6;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"66";s:10:"link_title";s:19:"Próximos Concursos";s:11:"router_path";s:18:"pr-ximos-concursos";s:3:"lft";s:2:"12";s:4:"rght";s:2:"13";s:9:"parent_id";s:2:"55";s:10:"post_count";i:0;}}i:7;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"67";s:10:"link_title";s:9:"Viviendas";s:11:"router_path";s:11:"viviendas-1";s:3:"lft";s:2:"14";s:4:"rght";s:2:"15";s:9:"parent_id";s:2:"55";s:10:"post_count";i:7;}}i:8;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"68";s:10:"link_title";s:15:"Otros Inmuebles";s:11:"router_path";s:17:"otros-inmuebles-1";s:3:"lft";s:2:"16";s:4:"rght";s:2:"17";s:9:"parent_id";s:2:"55";s:10:"post_count";i:0;}}}a:9:{i:0;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:1:"1";s:10:"link_title";s:7:"Default";s:11:"router_path";s:7:"default";s:3:"lft";s:1:"1";s:4:"rght";s:1:"2";s:9:"parent_id";s:1:"0";s:10:"post_count";i:0;}}i:1;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"54";s:10:"link_title";s:5:"Venta";s:11:"router_path";s:5:"venta";s:3:"lft";s:1:"3";s:4:"rght";s:2:"10";s:9:"parent_id";s:1:"0";s:10:"post_count";i:4;}}i:2;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"63";s:10:"link_title";s:9:"Viviendas";s:11:"router_path";s:9:"viviendas";s:3:"lft";s:1:"4";s:4:"rght";s:1:"5";s:9:"parent_id";s:2:"54";s:10:"post_count";i:4;}}i:3;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"64";s:10:"link_title";s:15:"Otros Inmuebles";s:11:"router_path";s:15:"otros-inmuebles";s:3:"lft";s:1:"6";s:4:"rght";s:1:"7";s:9:"parent_id";s:2:"54";s:10:"post_count";i:0;}}i:4;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"65";s:10:"link_title";s:10:"Histórico";s:11:"router_path";s:9:"hist-rico";s:3:"lft";s:1:"8";s:4:"rght";s:1:"9";s:9:"parent_id";s:2:"54";s:10:"post_count";i:0;}}i:5;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"55";s:10:"link_title";s:8:"Alquiler";s:11:"router_path";s:8:"alquiler";s:3:"lft";s:2:"11";s:4:"rght";s:2:"18";s:9:"parent_id";s:1:"0";s:10:"post_count";i:7;}}i:6;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"66";s:10:"link_title";s:19:"Próximos Concursos";s:11:"router_path";s:18:"pr-ximos-concursos";s:3:"lft";s:2:"12";s:4:"rght";s:2:"13";s:9:"parent_id";s:2:"55";s:10:"post_count";i:0;}}i:7;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"67";s:10:"link_title";s:9:"Viviendas";s:11:"router_path";s:11:"viviendas-1";s:3:"lft";s:2:"14";s:4:"rght";s:2:"15";s:9:"parent_id";s:2:"55";s:10:"post_count";i:7;}}i:8;a:1:{s:8:"MenuLink";a:7:{s:2:"id";s:2:"68";s:10:"link_title";s:15:"Otros Inmuebles";s:11:"router_path";s:17:"otros-inmuebles-1";s:3:"lft";s:2:"16";s:4:"rght";s:2:"17";s:9:"parent_id";s:2:"55";s:10:"post_count";i:0;}}}');
        return $this->Menu->generate($menu, $settings);
	}
	
	function theme_breadcrumb($b) {
		$out = array();
		foreach ($b as $node) {
			$selected = $node['MenuLink']['router_path'] == str_replace($this->_View->base, '', $this->_View->here) ? 'text-decoration:underline;' : '';
			$out[] = $this->_View->Html->link($node['MenuLink']['link_title'], $node['MenuLink']['router_path'], array('title' => $node['MenuLink']['description'], 'style' => $selected) );
		}
		if (empty($out) )
            return '';
            
		return implode(' » ', $out) . ' » ';
	}
	
    function stylesheets_alter($css) {
        if (isset($this->request->params['prefix']) && $this->request->params['prefix'] == 'admin') {
            foreach ($css['all'] as $k => $file) {
                if ($file == 'default.frontend.css')
                    unset($css['all'][$k]);
            }
        } else {
            foreach ($css['all'] as $k => $file) {
                if ($file == 'default.backend.css')
                    unset($css['all'][$k]);
            }        
        }
    }
	
	function theme_block($block) {
		$output = '';
		
		switch ( $block['region']) {
			case 'management-menu':
					$output .= "<div id=\"{$block['region']}\" class=\"item-list\">{$block['body']}</div>";
			break;
			
            case 'toolbar':
				$output =  $block['body']; 
			break;

            case 'footer':
				$output =  $block['body']; 
			break;

			default:
				$output = $this->_View->element('default_theme_block', array('block' => $block) );	
			break;
		}
		
		return $output;	
	}
    
}