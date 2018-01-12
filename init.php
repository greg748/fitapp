<?php
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
session_start();
include_once 'autoload.php';
include_once 'vendor/autoload.php';

use Fitapp\classes\DBConnection;
use Fitapp\classes\AppConfig;

$Config = AppConfig::get();
$db = DBConnection::get()->getADODB();