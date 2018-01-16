<?php

require_once '../../init.php';
use Fitapp\classes\Users;
use Fitapp\tools\Template;

$users = Users::getAll();
Template::startPage('Users');
foreach ($users as $u) {
    print_r($u);
}
Template::endPage();