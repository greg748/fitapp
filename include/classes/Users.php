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
      'timezone' => '',
      'status' => 'a',
    ];

    $this->no_insert = ['id','created','last_login'];
    $this->no_save = ['created','last_login'];
    parent::__construct();

  }

  

}