<?php
namespace Fitapp\classes;
class Exercises extends Table {

  function _construct() {
    $this->table_name = 'exercises';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'nicknames' => '',
      'primary_musc' => 0,
      'secondary_muscs' => [],
      'description' => '',
      'ability_level' => 1,
      'equipment' => '',
      'user_position' => '',
      'workout_type' => [],
      'grip' => '',
      'weight_type' => [], 
      'image' => '',
      'video' => '',
      'status' => 'a',
      'created_by' => 0,
      'notes' => '',
      'created' => NULL // @todo add lastmodified?
    ];
    $this->no_insert = ['id', 'created'];
    $this->no_save = ['created'];
    parent::_construct();

  }

  /**
   * Gets Exercises from DB, based on filters, or all if none specified
   * @param mixed $filters
   * @return mixed Array of exercises
   */
  public function getExercises($filters = NULL) {
    $filters = $this->filterExercises($filters);
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}
      WHERE true {$filters}";
    $results = $this->db->CacheExecute($sql);
    $exercises = [];
    foreach ($results as $r) {
      $exercises[$r['id']]=$r;
    } 
    return $exercises;
  }

  /**
   * Builds out sql where clauses based on filters assigned
   * @param mixed $filters facets to filter on
   * @return string SQL to filter on these facets
   */
  protected function filterExercises($filters = NULL) {
    $exFitlers = '';
    return $exFilters;
  }

}