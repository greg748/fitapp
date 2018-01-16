<?php
namespace Fitapp\classes;
use Fitapp\classes\Regimens;

class RegimenWorkouts extends Table {

    function __construct() {
    $this->table_name = 'regimen_workouts';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
        'id' => 0,
        'regimen_id' => 0,
        'workout_id' => 0,
        'workout_type'=> 0,
        'add_date' => NULL, 
        'remove_date' => NULL,
        'lastmodified' => NULL,
        'status' => 'a',
    ];

    $this->no_insert = ['id','','lastmodified'];
    $this->no_save = ['created'];
    parent::__construct();

    }

    public function getRegimenData() {
        $regimen_id = $this->getField('regimen_id');
        $Regimen = Regimens::get($regimen_id);
        $regimen = $Regimen->getFields();
        return $regimen;
    }

}