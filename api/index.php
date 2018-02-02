<?php

include_once '../init.php';
use Fitapp\api\RestUsers;
use Fitapp\api\RestSets;

$api_request = $_SERVER['REQUEST_URI'];
$headers = getallheaders();
if (isset($headers['HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = "Bearer ".$headers['HTTP_AUTHORIZATION'];
}
if (isset($headers['Authorization'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $headers['Authorization'];
}
if (isset($headers['HTTP_USER_TOKEN'])) {
    $_SERVER['HTTP_USER_TOKEN'] = $headers['HTTP_USER_TOKEN'];
}

list($resource,$others) = explode('/',ltrim($api_request,'/'));

switch ($resource) {
    case 'users' : $restObject = new RestUsers();
    break;

    case 'sets' : $restObject = new RestSets();

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