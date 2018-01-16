<?php
namespace Fitapp\classes;
class Users extends Table {

  function __construct() {
    $this->table_name = 'users';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'username' => '',
      'firstname' => '',
      'lastname' => '',
      'email' => '',
      'password' => '',
      'created' => NULL,
      'last_login' => NULL,
      'lastmodified'=> NULL,
      'timezone' => '',
      'status' => 'a',
    ];

    $this->no_insert = ['id','created','last_login','lastmodified'];
    $this->no_save = ['created'];
    parent::__construct();

  }

  public static function getAll() {
    $Users = static::getNewSelf();
    $sql = "SELECT * from users order by lastname asc";
    $users = [];
    $results = $Users->db->Execute($sql);
    foreach ($results as $r) {
      $users[$r['id']] = $r;
    }
    return $users;
  }

}