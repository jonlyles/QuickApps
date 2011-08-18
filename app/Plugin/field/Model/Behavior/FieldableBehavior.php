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
 * Expand your table columns by attaching extra fields to any Model.
 * 
 * ### What a Field is
 * 
 * Fields are actually modules (cake's plugin), which manage the storing proccess of especific data.
 * Acts like a module, what means they may have hooks and all that a common plugin have.
 * The data is commonly stored in DB tables, QuickApps provides a basic storage table
 * called '{prefix}_field_data'. Anyway, each field is able to define its own storing system (extra tables commonly).
 * Also, each field's data must have a unique ID in that storing system and each data is associated 
 * to a unique Model record.
 * 
 * ### Understanding Model->Field relations
 * 
 * - Model -> hasMany -> FieldInstances:
 *      A model may have multples instances of the same field, i.e.: 
 *      An User model may define extra fields 'last name' and 'age', both represented
 *      by a textbox, means that each field ('last name' & 'age') is an instance of the same Field handler: 'field_textbox'.
 *   
 * 
 * - FieldInstance -> hasMany -> FieldData:
 *      Obviously each instance may have multiple data records in its storing system, BUT each of
 *      this records belongs to diferent Model record. i.e.: the instance 'last name' for the 
 *      User model may have many records of data but each 'last name' actually belong to diferent Users. 
 * 
 * - Model -> FieldInstance -> hasOne -> FieldData:
 *      When retrieving a Model record, all its extra fields are captured (instances).
 *      Then each of this instances has ONLY ONE related data for this Model record.
 *      i.e.: When editing a User, its 'last name' field must have only one value even though the 
 *      field instance has many data records in its storage system. (explanation above)
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
 *                                   (field instances are stored in '{prefix}_fields' table)
 * 
 *      - (mixed) data: Field data. It may be from a simple text to complex arrays of mixed data
 *                      For example, an 'field_image_album' could define data as an array of images.
 * 
 *      - (int) id: Storage ID. Unique ID for the data in the storage system implemented by the field.
 *                  null ID means that there is no data stored yet for this Model record and this field instance.
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
 * 
 * ### Fields, capturing POST and saving the data
 * 
 * All field objects (modules) may/must have the following Model hooks:
 *  - {field_module}_beforeSave() [optional]        # before save the Model record
 *  - {field_module}_afterSave() [required]         # after Model record has been saved
 *  - {field_module}_deleteInstance() [required]    # delete an instance an all related data (~delete table column)
 *  - {field_module}_beforeValidate() [optional]    # before validate Model record being saved
 *  - {field_module}_beforeDelete() [optional]      # before Model record delete
 *  - {field_module}_afterDelete() [optional]       # after Model record has been deleted
 * 
 * ### NOTE:
 * 
 * Field data must always be saved after Model record has been saved, 
 * that is on afterSave() callback. i.e.: When updating/creating a new User, all field's data
 * must be saved after the User native data has been updated/created
 * 
 */
class FieldableBehavior extends ModelBehavior {
/**
 * belongsTo: Name of the object that field belongs to. (Commonly Model Name)
 * If no information is given then Model name is used as default.
 */
    private $__settings = array();
    
/**
 * Temp holder for afterSave() proccessing
 */    
    private $__fieldData = array();

/**
 * Initiate Fieldable behavior
 *
 * @param object $Model instance of model
 * @param array $settings array of configuration settings.
 * 
 * @return void
 */
	public function setup(&$Model, $settings = array()) {
        # keep a setings array for each model
        $this->__settings[$Model->alias] = array();
		$this->__settings[$Model->alias] = Set::merge($this->__settings[$Model->alias], $settings);

        if (empty($this->__settings[$Model->alias]['belongsTo'])) {
            $this->__settings[$Model->alias]['belongsTo'] = $Model->alias;
        }
    }

/**
 * Check if field instances should be fetch or not to the Model
 * 
 * @param object $Model instance of model
 * 
 * @return boolean true
 */
    public function beforeFind(&$Model, $query) {
        if ((isset($Model->fieldsNoFetch) && $Model->fieldsNoFetch) || 
            (isset($query['recursive']) && $query['recursive'] <= 0)
        ) {
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
 * Fields data is stored in a temporaly variable (__fieldData) in order to save it
 * after the new Model record has been saved. That is, in afterSave() callback.
 * Remember: Field's storing process must always be executed after Model's save()
 * 
 * @param object $Model instance of model
 * 
 * @return boolean False if any of the fields has returned false. True otherwise
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

        if (isset($Model->data['FieldData'])) {
            $this->__fieldData = $Model->data['FieldData'];
        }

        return !in_array(false, $r, true);
    }

/**
 * Save field information after Model record has been saved.
 * 
 * @param object $Model instance of model
 * @param boolean $created wich indicate if a new record has been inserted
 * @see $this::beforeSave()
 * 
 * @return void
 */
    public function afterSave(&$Model, $created) {
        if (!empty($this->__fieldData)) {
            foreach ($this->__fieldData as $field_module => $fields) {
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

/**
 * Call each Model's field instances callback
 * 
 * @param object $Model instance of model
 * 
 * @return boolean False if any of the fields has returned false. True otherwise.
 */
    public function beforeDelete(&$Model) {
       return $this->__beforeAfterDelete(&$Model, 'before');
    }
    
/**
 * Call each Model's field instances callback
 * 
 * @param object $Model instance of model
 * 
 * @return boolean False if any of the fields has returned false. True otherwise.
 */
    public function afterDelete(&$Model) {
        return $this->__beforeAfterDelete(&$Model, 'after');
    }

/**
 * Invoke each field's beforeValidate()
 * If any of the fields return 'false' then Model's save() proccess is interrupted
 *
 * Note:
 *  The hook chain does not stop if in chain any of the fields returns a false value.
 *  All fields response for the event are collected, this is so because fields 
 *  may invalidate its field input in form.
 * 
 * @param object $Model instance of model
 * 
 * @return boolean True if all the fields are valid, false otherwise
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

/**
 * Fecth fields to Model results
 * 
 * @param object $Model instance of model
 * @param array $results The results of the Model's find operation
 * @param boolean $primary Whether Model is being queried directly (vs. being queried as an association)
 * 
 * @return mixed An array value will replace the value of $results - any other value will be ignored.
 */
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

            $belongsTo = $this->__parseBelongsTo($this->__settings[$Model->alias]['belongsTo'], $result);

            $result['Field'] = array();
            $modelFields = ClassRegistry::init('Field.Field')->find('all', 
                array(
                    'order' => array('Field.ordering' => 'ASC'), 
                    'conditions' => array(
                        'Field.belongsTo' => $belongsTo
                    )
                )
            );

            $result['Field'] = Set::extract('/Field/.', $modelFields);

            foreach ($result['Field'] as $key => &$field) {
                $field['FieldData'] = array();  # field module storage data must be set here
                
                $data['field'] =& $field; # field instance information
                $data['belongsTo'] = $Model->alias; # field belongsTo
                $data['foreignKey'] = @$result[$Model->alias][$Model->primaryKey]; # Model unique ID                                                                
                $data['result'] =& $result; # instance of the current node being fetched
                
                $Model->hook("{$field['field_module']}_afterFind", $data);
            }
        }

        return $results;
    }

/**
 * Attach a new field instance to Model.
 * (Would like to add a new column to your table)
 * 
 * @param object $Model instance of model
 * @param array $data Field instance information:
 *  - label: Field input label. i.e.: 'Article Body' for a textarea
 *  - name: Filed unique name. underscored and alphanumeric characters only. i.e.: 'field_article_body'
 *  - field_module: Name of the module that handle this instance. i.e.: 'filed_textarea'
 * 
 * @return mixed Return (int) Field instance ID if it was added correctly. False otherwise.
 */
    public function attachFieldInstance(&$Model, $data) {
        $data = isset($data['Field']) ? $data['Field'] : $data;
        $data = array_merge(
            array(
                'label' => '',
                'name' => '',
                'field_module' => ''
            ),
            $data
        );

        extract($data);
    
        $field_module = Inflector::underscore($field_module);
        $field_info = $Model->hook('field_info', $field_module, array('alter' => false, 'collectReturn' => false));

        if (isset($field_info[$field_module])) {
            if (isset($field_info[$field_module]['max_instances']) && is_numeric($field_info[$field_module]['max_instances']) && $field_info[$field_module]['max_instances'] > 0) {
                $count = ClassRegistry::init('Field.Field')->find('count', 
                    array(
                        'Field.belongsTo' => $this->__settings[$Model->alias]['belongsTo'],
                        'Field.field_module' => $field_module
                    )
                );
                
                if ($count > $field_info[$field_module]['max_instances']) {
                    return false;
                }
            }
        }

        $newField = array(
            'Field' => array(
                'belongsTo' => $this->__settings[$Model->alias]['belongsTo'],
                'label' => $label,
                'name' => $name,
                'field_module' => $field_module
            )
        );
        $Field = ClassRegistry::init('Field.Field');
        
        if ($Field->save($newField)) {
            return $Field->id;
        }

        return false;
    }

/**
 * Return all fields instantces attached to Model.
 * Useful when rendering forms.
 * 
 * @param object $Model instance of model
 * 
 * @return array List array of all attached fields
 */
    public function fieldInstances(&$Model) {
        $results = ClassRegistry::init('Field.Field')->find('all', 
            array(
                'conditions' => array(
                    'Field.belongsTo' => $this->__settings[$Model->alias]['belongsTo']
                ),
                'order' => array('Field.ordering' => 'ASC')
            )
        );

        return $results;
    }

/**
 * Makes a beforeDelete() or afterDelete().
 * Invoke each field before/afterDelte event.
 * 
 * @param object $Model instance of model
 * @param string $type callback to execute, possible values: 'before' or 'after'
 * 
 * @return boolean False if any of the fields has returned false. True otherwise
 */    
    private function __beforeAfterDelete(&$Model, $type = 'before') {
        $model_id = $Model->id ? $Model->id : $Model->tmpData[$Model->alias][$Model->primaryKey];

        if ($type == 'before') {
            $result = $Model->find('first',
                array(
                    'conditions' => array(
                        "{$Model->alias}.{$Model->primaryKey}" => $model_id
                    ),
                    'recursive' => -1
                )
            );

            $Model->tmpBelongsTo = $belongsTo = $this->__parseBelongsTo($this->__settings[$Model->alias]['belongsTo'], $result);
            $Model->tmpData = $result;
        } else {
            $belongsTo = $Model->tmpBelongsTo;
        }

        $fields = ClassRegistry::init('Field.Field')->find('all',
            array(
                'conditions' => array(
                    'belongsTo' => $belongsTo
                )
            )
        );

        $r = array();

        foreach ($fields as $field) {
            $info['field_id'] = $field['Field']['id'];
            $info['model_name'] = $Model->name;
            $info['model_id'] = $model_id;
            $info['Model'] =& $Model;
            $r[] = $Model->hook("{$field['Field']['field_module']}_{$type}Delete", $info, array('collectReturn' => false));
        }

        return !in_array(false, $r, true);
    }

/**
 * Parses 'belongsTo' parameter looking for array paths.
 * This functionality is used only (and should be used only) by Nodes. That is so because, 
 * Nodes may have diferent fields attached depending in NodeType (bridge association), so nodes
 * uses a 'belongsTo' like: 'NodeType-{Node.node_type_id}'
 *
 * @param string $belongsTo string to parse
 * @param array $result a Node model row
 *
 * @return string
 */
    private function __parseBelongsTo($belongsTo, $result = array()){
         # look for dynamic belongsTo
		preg_match_all('/\{([\{\}0-9a-zA-Z_\.]+)\}/iUs', $belongsTo, $matches);
		if (isset($matches[1]) && !empty($matches[1])) {
			foreach ($matches[0] as $i => $m) {
				$belongsTo = str_replace($m, Set::extract(trim($matches[1][$i]), $result), $belongsTo);
			}
		}

        return $belongsTo;
    }

/**
 * Do not fetch fields instances on Model->find()
 * 
 * @param object $Model instance of model
 * 
 * @return void
 */    
    public function unbindFields(&$Model) {
        $Model->fieldsNoFetch = true;
    }

/**
 * Fetch all field instances on Model->find()
 * 
 * @param object $Model instance of model
 * 
 * @return void
 */    
    public function bindFields(&$Model) {
        $Model->fieldsNoFetch = false;
    } 
}