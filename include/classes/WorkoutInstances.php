<?php
/**
 * Handles instances of a workout, also called a session on the front end
 */

namespace Fitapp\classes;
class WorkoutInstances extends Table {

    function __construct() {
        $this->table_name = 'workout_instances';
        $this->table_prefix = '';
        $this->pkey = 'id';
        $this->fields = [
            'id' => 0,
            'workout_id' => 0,
            'user_id' => '',
            'workout_date' => NULL,
            'notes' => '',
            'created'=>NULL
        ];
        $this->no_insert = ['id','created'];
        $this->no_save = ['created'];
        parent::__construct();
    }

    /**
     * Returns next set ordinal value for this workout instance and exercise
     * @param $exercise_id integer
     * @return Integer
    */
    public function getNextSetOrdinal($exercise_id = 0) {
        $wi_id = $this->getField('id');
        $sql = "SELECT COALESCE(MAX(set_order),0)+1
            FROM exercise_sets
            WHERE workout_instance_id=$wi_id
            AND exercise_id=$exercise_id";
        return $this->db->getOne($sql);
    }
    
    public function getSets() {
        $wi_id = $this->getField('id');
        $sql = "SELECT * FROM exercise_sets 
        WHERE workout_instance_id=$wi_id
        ORDER by group_id, exercise_id, set_order";
        $results = $this->db->Execute($sql);
        echo $sql;
        
        return $results;
    }

    public function getSetsForInputs() {
        $sets = $this->getSets();
        $inputs = [];
        foreach ($sets as $set) {
            $varPrefix = "ex_{$set['group_id']}_{$set['exercise_id']}_{$set['set_order']}";
            $inputs["{$varPrefix}_type"] = $set['set_type'];
            $inputs["{$varPrefix}_weight"] = $set['weight'];
            $inputs["{$varPrefix}_units"] = $set['units'];
            $inputs["{$varPrefix}_reps"] = $set['reps'];
            $inputs["{$varPrefix}_id"] = $set['id'];
        }
        return $inputs;
    }
    
}

