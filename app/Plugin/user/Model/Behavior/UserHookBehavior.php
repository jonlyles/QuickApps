<?php 
/**
 * User Model Hooks
 *
 * PHP version 5
 *
 * @category User.Model/Behavior
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@qucikapps.es>
 * @link     http://cms.quickapps.es
 */
class UserHookBehavior extends ModelBehavior {
    # prevent unnecessary variables to load on startup by _loadVariables()
    public function beforeFind($model, $query) {
        if ($model->name == 'Variable' && empty($query['conditions'])) { # empty conditions = select *
            $query['conditions'] = Set::merge($query['conditions'], 
                array(
                    'NOT' => array(
                        'Variable.name LIKE' => "user_mail_%"
                    )
                )
            );
        }
        
        return $query;
    }
}