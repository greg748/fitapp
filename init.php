<?php

include_once 'autoload.php';
include_once 'vendor/autoload.php';

use Fitapp\classes\DBConnection;
use Fitapp\classes\AppConfig;

$Config = AppConfig::get();
$db = DBConnection::get()->getADODB();