<?php 
/**
 * Fieldable Behavior
 * 
 * PHP version 5
 *
 * @package  QuickApps.Plugin.Field.Model.Behavior
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
 
 /**
 * Basically this behavior allows to:
 * Expand your table columns by attaching extra fields to any your Model.
 * 
 * ### What a Field is
 * 
 * Fields are actually a module (cake plugin), which manage the storing proccess of especific data.
 * Acts like a module, what means they may have hooks and all that a common plugin have.
 * The data storaged is commonly stored in DB tables, QuickApps provides a basic storing table
 * called '{prefix}_field_data'. But each field may define its own storing system (extra tables commonly).
 * Each field's data must have a unique ID in that storing system and each data is associated 
 * to a unique Model record.
 * 
 * ### Understanding Model->Field relations
 * 
 * - Model -> hasMany -> FieldInstances:
 *      A model may have multples instances of the same field, i.e.: 
 *      An User model may define extra field 'last name' and 'age', and both are represented
 *      by a textbox, means that each field is an instance of the same Field: 'field_textbox'.
 *   
 * 
 * - FieldInstance -> hasMany -> FieldData:
 *      Obviously each instance may have multiples data in its storing system, BUT each of
 *      this records belongs to diferent Model record. i.e.: the instance 'last name' for the 
 *      User model may have many records of data but each 'last name' actually belongs to diferent Users. 
 * 
 * - Model -> FieldInstance -> hasOne -> FieldData:
 *      When retrieving a Model record, all its extra fields are captured (instances).
 *      Then each of this instances has ONLY ONE related data for this Model record.
 * 
 * ### Field posting strucure:
 * 
 * Each field MUST always send its information following this structure,
 * 
 *      data[FieldData][{field_module}][{field_instance_id}][data]
 *      data[FieldData][{field_module}][{field_instance_id}][id]
 * 
 *      - (string) {field_module}: name of the field handler, i.e.: 'field_textarea', 'field_my_field'.
 *                                 Note: 'field_' prefix is not required but highly recommended.
 * 
 *      - (int) {field_instance_id}: ID of the field instance attached to the current Model. 
 *                                   field instances are stored in {prefix}_fields table.
 * 
 *      - (mixed) data: Field data. It may be from a simple text to complex arrays of mixed data
 *                      For example, an 'field_image_album' could define data as an array of images.
 * 
 *      - (int) id: Storage ID. Unique ID for the data in the store system implemented by the field.
 *                  null ID means that there is no data stored yet for this Model record and field instance.
 *
 *  debug($this->data) should look like:
 *          array(
 *              .... // Other Model's native fields (table columns)
 *              'FieldData' => array(
 *                  'field_module_1' => array(
 *                      41 => array(
 *                          'id' => 153,
 *                          'data' => 'This data has an id = 153 and instance id 41'
 *                      ),
 *                      95 => array(
 *                          'id' => 181,
 *                          'data' => 'This is other instance (95) of field_module_1'                         
 *                      )...
 *                  ),
 *                  'field_module_2' => array(
 *                      60 => array(
 *                          id => null,
 *                          'data' => 'null storage ID means that there is no data stored yet for this field instance and Model record'
 *                      )
 *                  )
 *              )
 *          )
 * ### Fields, capturing POST and saving the data
 * 
 * All field objects (modules) may/must have the following Model hooks:
 *  - {field_module}_beforeSave() [optional]
 *  - {field_module}_afterSave() [required]         # save() logic here.
 *  - {field_module}_deleteInstance() [required]    # delete an instance an all related data
 *  - {field_module}_beforeValidate() [optional]
 *  - {field_module}_beforeDelete() [optional]
 *  - {field_module}_afterDelete() [optional]
 * 
 * ### NOTE:
 * 
 * Field data must always be saved after Model record has been saved, 
 * that is on afterSave() callback. i.e.: When updating/creating a new User, all field's data
 * must be saved after the User native data has been saved
 * 
 */
class FieldableBehavior extends ModelBehavior {
/**
 * belongsTo: Name of the object that field belongs to. (Commonly Model Name)
 * If no information is given then Model name is used as default. 
 */
    private $__settings = array('belongsTo' => null);
    
/**
 * Temp holder for afterSave() proccessing
 */    
    private $fieldData = null;

/**
 * Initiate Fieldable behavior
 *
 * @param object $Model instance of model
 * @param array $settings array of configuration settings.
 * 
 * @return void
 */
	public function setup($Model, $settings = array()) {
		$this->__settings = Set::merge($this->__settings, $settings);

        if(empty($this->__settings['belongsTo'])) {
            $this->__settings['belongsTo'] = $Model->alias;
        }

        $this->Field = ClassRegistry::init('Field.Field');
        $this->Field->FieldData = ClassRegistry::init('Field.FieldData');
        
        return;
    }

/**
 * Check if field instances should be fetch or not to the Model
 * 
 * @param object $Model instance of model
 * 
 * @return boolean true
 */     
    public function beforeFind(&$Model) {
        if (isset($Model->fieldsNoFetch) && $Model->fieldsNoFetch) {
            $Model->unbindModel(
                array(
                    'hasMany' => array('Field')
                )
            );
        }
        
        return true;
    }

/**
 * Invoke each field's beforeSave() event and proceed with the Model's save proccess
 * if all the fields has returned 'true'.
 * 
 * If Model->id is not set means that a new record will be saved, in that case all field's data 
 * set in Model is stored in a temporaly variable (fieldData) in order to save it
 * after the new Model-record has been saved. That is, in afterSave() callback.
 * 
 * @param object $Model instance of model
 * 
 * @return boolean False if any of the fields has returned false. True otherwhise
 */    
    public function beforeSave(&$Model) {
        $r = array();

        /**
         * optionaly we send field information to field handlers before save the Model record, 
         * so they can stop the proccess if it is required
         */
        if (isset($Model->data['FieldData'])) {
            foreach ($Model->data['FieldData'] as $field_module => $fields) {
                foreach ($fields as $field_id => $info) {
                    $info['field_id'] = $field_id;
                    $info['model_name'] = $Model->name;
                    $info['model_id'] = $Model->id;
                    $info['Model'] =& $Model;
                    $r[] = $Model->hook("{$field_module}_beforeSave", $info, array('collectReturn' => false));
                }
            }
        }

        $this->fieldData = $Model->data['FieldData'];

        return !in_array(false, $r, true);
    }

/**
 * If a new Model-record has been saved, then proceed to save related field's data.
 * 
 * @param object $Model instance of model
 * @param boolean $created wich indicate if a new record has been inserted
 * @see $this::beforeSave()
 * 
 * @return void
 */
    public function afterSave(&$Model, $created) {
        if (!empty($this->fieldData)) {
            foreach ($this->fieldData as $field_module => $fields) {
                foreach ($fields as $field_id => $info) {
                    $info['field_id'] = $field_id;
                    $info['model_name'] = $Model->name;
                    $info['model_id'] = $Model->id;
                    $info['Model'] =& $Model;
                    $info['created'] = $created;
                    $Model->hook("{$field_module}_afterSave", $info);
                }
            }
        }

        return;
    }

    public function beforeDelete(&$Model) {
       return $this->__beforeAfterDelete(&$Model, 'before');
    }

    public function afterDelete(&$Model) {
        return $this->__beforeAfterDelete(&$Model, 'after');
    }

/**
 * Makes a beforeDelete() or afterDelete().
 * Invoke each field before/afterDelte event.
 * 
 * @param object $Model instance of model
 * @param string $type callback to execute, possible values: 'before' or 'after'
 * 
 * @return boolean True if all the fields has returned true. False otherwhise
 */    
    private function __beforeAfterDelete(&$Model, $type = 'before') {
        $model_id = $Model->id ? $Model->id : $Model->tmpId;
        $fields = ClassRegistry::init('Field.Field')->find('all',
            array(
                'conditions' => array(
                    'belongsTo' => "{$Model->name}"
                )
            )
        );
        
        $r = array();

        foreach ($fields as $field) {
            if ($type == 'before') {
                $Model->tmpId = $Model->id;
            }

            $info['field_id'] = $field['Field']['id'];
            $info['model_name'] = $Model->name;
            $info['model_id'] = $model_id;
            $info['Model'] =& $Model;
            $r[] = $Model->hook("{$field['Field']['field_module']}_{$type}Delete", $info, array('collectReturn' => false));
        }

        return !in_array(false, $r, true);
    }

/**
 * Invoke each field's beforeSave()
 * If any of the fields return 'false' then the Model's save proccess is interrupted
 * Note:
 *  The hook chain does not stop if in chain any of the fields returns a false value.
 *  All fields response for the event are collected, this is so because fields 
 *  may invalidate its field input in form.
 * 
 * @param object $Model instance of model
 * 
 * @return boolean True if all the fields are valid, false otherwhise
 */
    public function beforeValidate(&$Model) {
        if (!isset($Model->data['FieldData'])) {
            return true;
        }

        $DummyModel = ClassRegistry::init('Dummy'); # shortcut to AppModel::hook()
        $r = array();

        foreach ($Model->data['FieldData'] as $field_module => $fields) {
            foreach ($fields as $field_id => $info) {
                $info['field_id'] = $field_id;
                $info['model_name'] = $Model->name;
                $info['model_id'] = $Model->id;
                $info['Model'] =& $Model;
                $r[] = $DummyModel->hook("{$field_module}_beforeValidate", $info, array('collectReturn' => false));
            }
        }

        return !in_array(false, $r, true);
    }

    public function afterFind(&$Model, $results, $primary) {
        if (empty($results) || 
            !$primary || 
            (isset($Model->fieldsNoFetch) && $Model->fieldsNoFetch)
        ) {
            return $results;
        }

        # fetch model instance Fields
        foreach ($results as &$result) {
            if (!isset($result[$Model->alias])) {
                continue;
            }

            $belongsTo = $this->__settings['belongsTo'];

            # look for array paths
    		preg_match_all('/\{([\{\}0-9a-zA-Z_\.]+)\}/iUs', $belongsTo, $matches);
    		if (isset($matches[1]) && !empty($matches[1])) {
    			foreach ($matches[0] as $i => $m) {
    				$belongsTo = str_replace($m, Set::extract(trim($matches[1][$i]), $result), $belongsTo);
    			}
    		}

            $result['Field'] = array();
            $modelFields = $this->Field->find('all', 
                array(
                    'order' => array('Field.ordering' => 'ASC'), 
                    'conditions' => array(
                        'Field.belongsTo' => $belongsTo
                    )
                )
            );

            $result['Field'] = Set::extract('/Field/.', $modelFields);

            foreach ($result['Field'] as $key => &$field) {
                /*
                 * Attempt to find basic data (FieldData).
                 * Remember: fields can define their own storage tables (or 'storage system' in general), 
                 * but FieldData is the basic common
                 */
                $field['FieldData'] = $this->Field->FieldData->find('first', 
                    array(
                        'conditions' => array(
                            'FieldData.field_id' => $field['id'], 
                            'FieldData.foreignKey' => $result[$Model->alias][$Model->primaryKey]
                        )
                    )
                );

                $field['FieldData'] = Set::extract('/FieldData/.', $field['FieldData']);
                $field['FieldData'] = isset($field['FieldData'][0]) ? $field['FieldData'][0] : $field['FieldData'];
                $Model->hook("{$field['name']}_afterFind", $result['Field'][$key]);
            }
        }

        return $results;
    }
    
/**
 * Return all fields instantces attached to the current model.
 * Useful when rendering forms.
 * 
 * @return array List array of all attached fields
 */
    public function fieldInstances() {
        $results = $this->Field->find('all', 
            array(
                'conditions' => array(
                    'Field.belongsTo' => $this->__settings['belongsTo']
                ),
                'order' => array('Field.ordering' => 'ASC')
            )
        );

        return $results = Set::extract('/Field/.', $results);
    }
    
/**
 * Do not fetch fields instances on Model->find()
 * 
 * @return void
 */    
    public function unbindFields(&$Model) {
        $Model->fieldsNoFetch = true;
    }

/**
 * Fetch all field instances on Model->find()
 * 
 * @return void
 */    
    public function bindFields(&$Model) {
        $Model->fieldsNoFetch = false;
    } 
}