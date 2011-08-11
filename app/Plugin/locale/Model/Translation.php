<?php
/**
 * Translation Model
 *
 * PHP version 5
 *
 * @category Locale.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class Translation extends LocaleAppModel {
    var $name = 'Translation';
    var $useTable = "translations";
	var $primaryKey = 'id';
    var $validate = array(
        'original' => array(
            'notEmpty' => array(
                'required' => true, 
                'allowEmpty' => false, 
                'rule' => 'notEmpty', 
                'message' => 'Original text can not be empty.'
            ),
            'unique' => array(
                'rule' => 'isUnique',
				'message' => 'Original text already exists.'
            )
        )
    );

    var $hasMany = array(
        'I18n' => array(
            'className' => 'Locale.Internationalization',
            'model' => 'Translation',
            'foreignKey' => 'foreign_key',
            'dependent' => true
        )
    );
    
    function afterSave(){   
        if ( !isset($this->data['Translation']['original']) && isset($this->data['Translation']['id']) ){
            $original = $this->find('first', 
                array(
                    'conditions' => array(
                        'Translation.id' => $this->data['Translation']['id']
                    ),
                    'fields' => array('id', 'original'),
                    'recursive' => -1
                )
            );
            $original = $original['Translation']['original'];
        } else {
            $original = $this->data['Translation']['original'];
        }
        $cacheID = md5($original);

        foreach($this->data['I18n'] as $t){
            Cache::delete("{$cacheID}_{$t['locale']}", 'i18n');
            Cache::write("{$cacheID}_{$t['locale']}", $t['content'], 'i18n');
        }
        return true;
    }
    
    function beforeDelete(){
        $original = $this->field('original');
        $cacheID = md5($original);
        foreach (Configure::read('Variable.languages') as $l){
            Cache::delete("{$cacheID}_{$l['Language']['code']}", 'i18n');
        }
        return true;
    }
}