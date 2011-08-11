<?php
/**
 * BlockRegion Model
 *
 * PHP version 5
 *
 * @category Block.Model
 * @package  QuickApps
 * @version  1.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://cms.quickapps.es
 */
class BlockRegion extends BlockAppModel {

    var $name       = 'BlockRegion';
    var $useTable   = 'block_regions';
	var $order	    = array('BlockRegion.ordering' => 'ASC');
	var $primaryKey = 'id';
    
    function beforeSave(){
        // add last if its a new assignment
        if  ( !isset($this->data['BlockRegion']['id']) ){
            $r = $this->data['BlockRegion']['region'];
            $t = $this->data['BlockRegion']['theme'];
            $c = $this->find('count', array('conditions' => array('BlockRegion.theme' => $t, 'BlockRegion.region' => $r) ) );
            $this->data['BlockRegion']['ordering'] = $c+1;
        }
        return true;
    }
    
    function move($id, $dir = 'up'){
        if ( !$record = $this->findById($id) )
            return false;
        
        $nodes = $this->find('all',
            array(
                'conditions' => array(
                    'BlockRegion.theme' => $record['BlockRegion']['theme'],
                    'BlockRegion.region' => $record['BlockRegion']['region']
                ),
                'order' => array('BlockRegion.ordering' => 'ASC'),
                'fields' => array('id', 'ordering'),
                'recursive' => -1
            )
        );
            
        $ids = Set::extract('/BlockRegion/id', $nodes);
        if (    ($dir == 'down' && $ids[count($ids)-1] == $record['BlockRegion']['id']) || 
                ($dir == 'up' && $ids[0] == $record['BlockRegion']['id'])
        ) #edge -> cant go down/up
            return false;            
        
        $position = array_search($record['BlockRegion']['id'], $ids);
        $key = $dir == 'up' ? $position-1 : $position+1;
        $tmp = $ids[$key];
        $ids[$key] = $ids[$position];
        $ids[$position] = $tmp;
        
        $i = 1;
        foreach($ids as $id){
            $this->id = $id;
            $this->saveField('ordering', $i, false);
            $i++;
        }    
    }
    
}