<?php
include_once '../../init.php';
use Fitapp\classes\Muscles;
echo "Here";

$muscleList = Muscles::getMuscles();

foreach($muscleList as $m) {
    print_r($m);
}