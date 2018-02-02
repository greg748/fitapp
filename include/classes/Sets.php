<?php
namespace Fitapp\classes;
class Sets extends Table {

    public static $set_types = ['bilateral' => 'bilateral', 'single' => 'single', 'alt' => 'alt',
        'alt-high' => 'alt-high', 'alt-low' => 'alt-low', 'position-1' => 'position-1',
        'position-2' => 'position-2', 'position-3' => 'position-3', 'time' => 'time'];
    public static $units = ['' => '--', 'lbs' => 'lbs', 'kg' => 'kg'];

    function __construct()
    {
        $this->table_name = 'exercise_sets';
        $this->table_prefix = '';
        $this->fields = [
            'id' => 0,
            'workout_instance_id' => 0,
            'group_id' => 0,
            'exercise_id' => 0,
            'set_order' => 0,
            'set_type' => '', // 'bilateral','single','alt'... above
            'weight' => NULL,
            'units' => 'lbs', // lbs/kg
            'reps' => 0, // @todo how to store time?
            'created' => NULL,
            'lastmodified' => NULL,
        ];
        $this->no_insert = ['id', 'created', 'lastmodified'];
        $this->no_save = ['created', 'lastmodified'];
        parent::__construct();

        $this->pkey = 'id';
    }

    /** 
     * Handles logic for update or create of set 
     * 
     * @param mixed $data
     * @param boolean $showSql
     * @return boolean Saved or not
     * @throws null
     */
    public static function saveSet($data = [], $showSql = FALSE) {
        $data['set_type'] = $data['type'];
        if (isset($data['id'])) {
            echo "<br> existing ";
            $Set = Sets::get($data['id']);
            if (intval($data['reps']) == 0) {
                $setOrdinal = $data['set_order'];
                $Set->delete();
                $sql = "UPDATE exercise_sets 
                SET set_order = set_order - 1
                WHERE workout_instance_id={$data['workout_instance_id']}
                AND group_id={$data['group_id']}
                AND exercise_id={$data['exercise_id']}
                AND set_order > $setOrdinal";
                $update = $Set->db->Execute($sql);
                return true;
            } else {
                $Set->setFields($data);
                $Set->save($showSql);
            }
            echo "<br>";
        } elseif (intval($data['reps']) > 0) {
            echo "<br> create new ";
            $Set = Sets::create($data, $showSql);
            echo "<br>";
        } else {
            echo "<br> no reps";
            echo $data['reps'] .' = ' .intval($data['reps']);
            print_pre($data);
            echo "<br>";
            return false;
        }
        return $Set->isSaved();
    }

    public function delete() {
        $id = $this->getField('id');
        $sql = "DELETE FROM exercise_sets 
            WHERE id=$id";
        return $this->db->Execute($sql);
    }
}
