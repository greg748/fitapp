<?php
namespace Fitapp\classes;
use Fitapp\classes\WorkoutExercises;

class ExerciseGroups extends Table {

    // this is an enum type
    public static $exercise_group_types = ['warmup', 'main', 'warmdown', 'cardio', 'active_recovery'];

    function __construct() {
    $this->table_name = 'exercise_groups';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
        'id' => 0,
        'workout_id' => 0,
        'group_type' => 'main',
        'group_order' => 1,
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

    }

    /**
     * Returns next exercise ordinal value for this workout and group
     *
     * @return Integer
     */
    public function getNextExerciseOrdinal() {
        $workout_id = $this->getField('workout_id');
        $group_id = $this->getField('id');
        $sql = "SELECT MAX(coalesce(exercise_order,0))+1
            FROM workout_exercises
            WHERE workout_id=$workout_id
            AND group_id=$group_id";
        return $this->db->getOne($sql);

    }

}