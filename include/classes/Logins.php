<?php
namespace Fitapp\classes;
class Logins extends Table {

  function __construct() {
    $this->table_name = 'logins';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'user_id'=>0,
      'login_time'=>NULL,
      'logout_time'=>NULL,
      'login_from'=>'',
    ];

    $this->no_insert = ['id','login_time','logout_time'];
    $this->no_save = ['created','login_time'];
    parent::__construct();

  }

  public function extendSession() {
    $this->setField('logout_time',date('Y-m-d H:i:s'));
    $this->save();
  }

}