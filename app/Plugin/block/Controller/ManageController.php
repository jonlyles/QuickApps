<?php
/**
 * Manage Controller
 *
 * PHP version 5
 *
 * @category Block.Controller
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class ManageController extends BlockAppController {
	public $name = 'Manage';
	public $uses = array('Block.Block', 'User.Role');
	
	public function admin_index() {
        $this->setCrumb('/admin/block');
		$this->title(__t('Blocks'));
		
        $this->set('results', $this->Block->find('all'));
        $this->set('themes', $this->__themesYaml());
	}
    
    public function admin_move($block_region_id, $dir) {
        if (in_array($dir, array('up', 'down'))) {
            if ($dir == 'up') {
                $this->Block->BlockRegion->move($block_region_id, 'up');
            } else {
                 $this->Block->BlockRegion->move($block_region_id, 'down');
            }
        }
        $this->redirect($this->referer());
    }
	
	public function admin_edit($bid) {
        $this->title(__t('Editing Block'));
        $this->setCrumb('/admin/block');
        
        if (isset($this->data['Block'])) {
            $data =  $this->data;
            $data['Block']['locale'] = !empty($data['Block']['locale']) ? array_values($data['Block']['locale']) : array();
            $data['Block']['themes_cache'] = $this->__themesCache($data['BlockRegion']);
            
            if ($this->Block->saveAll($data, array('validate' => 'first'))) { # saveAll only will save Block related models!
                $this->Block->BlockRegion->deleteAll( array('region' => ''));
                if (isset($data['Module'])) { # save widgets variables
                    $this->Module->save($data['Module']);
                    Cache::delete('Modules');
                    $this->_loadModules();
                }
                
                if (isset($data['Variable'])) {
                     $this->Variable->save($data['Variable']);
                    Cache::delete('Variable'); 
                    $this->_loadVariables();
                }
                
                $this->flashMsg(__t('Block has been saved'), 'success');
            } else {
                //$invalidFields = $this->Block->invalidFields();
                $this->flashMsg(__t('Block could not be saved. Please, try again.'), 'error');
            }
            $this->redirect("/admin/block/manage/edit/{$bid}");
        }
        
        $themes = $this->__themesYaml();
        foreach ($themes as $theme => $yaml) {
            $_regions["{$yaml['info']['name']}@|@{$theme}"] = array();
            foreach ($yaml['regions'] as $name => $title) {
                $_regions["{$yaml['info']['name']}@|@{$theme}"]["{$name}"] = $title;
            }
        }
        
		$this->data = $this->Block->findById($bid);
        $this->set('regions', $_regions);
        $this->set('roles', $this->Role->find('list'));
	}
	
	public function admin_add() {
        $this->title(__t('Add new block'));
        $this->setCrumb('/admin/block');
        $this->setCrumb( array(__t('New block'), '') );
        
        if (isset($this->data['Block'])) {
            $data = $this->data;
            foreach ($data['BlockRegion'] as $key => $br) {
                if (empty($br['region'])) {
                    unset($data['BlockRegion'][$key]);
                }
            }
                    
            $data['Block']['module'] = 'block';
            $data['Block']['locale'] = !empty($data['Block']['locale']) ? array_values($data['Block']['locale']) : array();            
            $data['Block']['themes_cache'] = $this->__themesCache($data['BlockRegion']);
            
            if ($this->Block->saveAll($data, array('validate' => 'first'))) {
                $this->Block->BlockRegion->deleteAll( array('region' => ''));
                $this->flashMsg(__t('Block has been saved'), 'success');
                $this->redirect("/admin/block/manage/edit/{$this->Block->id}");
            } else {
                $this->flashMsg(__t('Block could not be saved. Please, try again.'), 'error');
            }
        }   
        
        $themes = $this->__themesYaml();
        foreach ($themes as $theme => $yaml) {
            $_regions["{$yaml['info']['name']}@|@{$theme}"] = array();
            foreach ($yaml['regions'] as $name => $title) {
                $_regions["{$yaml['info']['name']}@|@{$theme}"]["{$name}"] = $title;
            }
        }
        
        $this->set('regions', $_regions);
        $this->set('roles', $this->Role->find('list'));
	}
    
    public function admin_delete($id) {
        $block = $this->Block->findById($id);
        if (empty($block) || $block['Block']['module'] != 'block') {
            $this->redirect('/admin');
        } else {
            $this->Block->delete($id);
            $this->redirect($this->referer());
        }
    }
    
    private function __themesCache($BlockRegion) {
        $o = array();
        foreach ($BlockRegion as $key => $r) {
            if (!empty($r['region'])) {
                $o[] = $r['theme'];
            }
        }
        return implode("\n", array_unique($o) );
    }
    
    private function __themesYaml() {
        $return = array();
        $folder = new Folder;
        $folder->path = APP . 'View' . DS . 'Themed';
        $folders = $folder->read();
        foreach ($folders[0] as $theme) {
            if (APP . 'View' . DS . 'Themed' . DS . $theme . DS . "{$theme}.yaml") {
                $yaml = Spyc::YAMLLoad(APP . 'View' . DS . 'Themed' . DS . $theme . DS . "{$theme}.yaml");
                $return[$theme] = $yaml;
            }
        }
        
        return $return;
    }    
}