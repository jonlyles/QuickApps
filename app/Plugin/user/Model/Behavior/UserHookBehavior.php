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
    function beforeFind($model, $query){
        # empty conditions = select *
        if ($model->name == 'Variable' && empty($query['conditions']) )
            $query['conditions'] = Set::merge($query['conditions'], 
                array(
                    'NOT' => array(
                        'Variable.name LIKE' => "user_mail_%"
                    )
                )
            );
        return $query;
    }
}