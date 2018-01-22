<?php
namespace Fitapp\classes;
class WorkoutExercises extends Table {
    
    function __construct() {
        $this->table_name = 'workout_exercises';
        $this->table_prefix = '';
        $this->pkey = 'id';
        $this->fields = [
          'id' => 0,
          'exercise_id'=>0,
          'workout_id' => 0,
          'exercise_group_id' => 0,
          'exercise_order' => 1,
          'nickname_used' => NULL,
          'rep_pattern'=> [12,10,8],
        ];
        $this->no_insert = ['id'];
        $this->null_fields = ['nickname_used'];
        $this->array_fields = ['rep_pattern'];
        $this->no_save = [];
        parent::__construct();
    }
    

    public static function removeFromGroup($workout_exercise_id = 0) {
        $results = false;
        if ($workout_exercise_id > 0) {
            $WE = static::get($workout_exercise_id);
            $exercise_order = $WE->getField('exercise_order');
            $sql = "DELETE FROM workout_exercises
            WHERE id=$workout_exercise_id";
            $results = $WE->db->Execute($sql);

            if ($results && $exercise_order > 0) {
                $group_id = $WE->getField('exercise_group_id');
                $sql = "UPDATE workout_exercises 
                SET exercise_order = exercise_order-1
                WHERE exercise_group_id=$group_id  
                AND exercise_order > $exercise_order";
                $updates = $WE->db->Execute($sql);
            } else {
                echo $sql;
                echo $WE->db->errorMsg();
                die;
            }
        }
        return $results;
    }
  
}