<?php
/**
 * Fields Controller
 *
 * PHP version 5
 *
 * @package  QuickApps.Plugin.User.Controller
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class FieldsController extends UserAppController {
	public $name = 'Fields';
	public $uses = array('Field.Field');
	
	public function admin_index() {
        if (isset($this->data['Field'])) {
            $data = $this->data;
            $data['Field']['name'] = !empty($data['Field']['name']) ? 'field_' . $data['Field']['name'] : '';
            $data['Field']['belongsTo'] = "User";
            $Field = ClassRegistry::init('Field.Field');
            
            if ($Field->save($data)) {
                $this->redirect("/admin/user/fields/field_settings/{$Field->id}");
            }
            
            $this->flashMsg(__t('Field could not be created. Please, try again.'), 'error');
        }
    
        $fields = $this->Field->find('all', array('conditions' => array('Field.belongsTo' => 'User')));
        
        /* Available field objects */
        foreach (App::objects('plugins') as $plugin) {
            $_plugin = Inflector::underscore($plugin);
            
            if (strpos(App::pluginPath($plugin), DS . 'Fields' . DS . $_plugin . DS) !== false) {
                $field_modules[$_plugin] = $plugin;
            }
        }
        
        $this->set('results', $fields);
        $this->set('field_modules', $field_modules);
		$this->setCrumb('/admin/user/');
		$this->setCrumb( array(__t('Manage Fields'), ''));
		$this->title(__t('Manage User Fields'));
	}
    
    public function admin_field_settings($id) {
        if (isset($this->data['Field'])) {
            if ($this->Field->save($this->data)) {
                $this->redirect($this->referer());
            }
        }
        
        $this->data = $this->Field->findById($id) or  $this->redirect('/admin/node/types');
        
        $this->setCrumb('/admin/user');
        $this->setCrumb( array(__t('Fields'), '/admin/user/fields'));
        $this->setCrumb( array(__t('Field settings'), ''));
        $this->title(__t('Field Settings'));
        $this->set('result', $this->data);    
    }
}