<?php

include_once '../init.php';
use Fitapp\api\RestUsers;

$api_request = $_SERVER['REQUEST_URI'];

list($resource,$others) = explode('/',ltrim($api_request,'/'));

switch ($resource) {
    case 'users' : $restObject = new RestUsers();
    break;

    case 'workouts' :
    break;

    case 'regimens' :
    break;

    case 'exercises' :
    break;

    case 'sessions' :
    break;

}
$restObject->doRest();

die;