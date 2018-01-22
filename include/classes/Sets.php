<?php
namespace Fitapp\classes;
class Sets extends Table {

    public static $set_types = ['bilateral'=>'bilateral','single'=>'single','alt'=>'alt',
        'alt-high'=>'alt-high','alt-low'=>'alt-low'];
    public static $units = ['lbs'=>'lbs','kg'=>'kg'];
    function __construct() {
        $this->table_name = 'sets';
        $this->table_prefix = '';
        $this->fields = [
            'id' => 0,
            'workout_instance_id' => 0,
            'exercise_id'=>0,
            'set_order'=>0,
            'set_type' => '', // 'bilateral','single','alt','alt-high','alt-low'
            'weight' => NULL,
            'units' => 'lbs', // lbs/kg
            'reps' => 0, 
            'created'=>NULL,
            'lastmodified'=>NULL,
        ];
        $this->no_insert = ['id','created','lastmodified'];
        $this->no_save = ['created','lastmodified'];
        parent::__construct();

        $this->pkey = 'id';
    }
}
