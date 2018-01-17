<?php
/**
 * Holds the relationship between trainers and clients, both within the users table
 */
namespace Fitapp\classes;
class TrainerClients extends Table {

  function __construct() {
    $this->table_name = 'trainer_clients';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'trainer_id' => 0,
      'client_id' => 0,
      'add_date' => NULL,
      'remove_date' => NULL,
    ];
    $this->no_insert = ['id'];
    $this->null_fields = ['add_date','remove_date'];
    $this->no_save = [];
    parent::__construct();

  }
}
