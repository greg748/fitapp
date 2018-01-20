<?php
include_once '../../init.php';
use Fitapp\classes\Regimens;
use Fitapp\classes\Workouts;
use Fitapp\classes\Users;
use Fitapp\tools\Template;

$id = $_REQUEST['id'];

$Regimen = Regimens::get($id);
$regimen = $Regimen->getFields();
$workouts = $Regimen->getWorkouts();
$scores = $Regimen->getMuscleScores();

Template::startPage('Regimen Scoring');
print_pre($regimen);
foreach ($workouts as $w) {
    print_pre($w);
}

Regimens::getScoreBlock($scores);
echo "done";
Template::endPage();

