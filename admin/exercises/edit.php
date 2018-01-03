<?php
include_once '../../init.php';
use Exercises;
$gripTypes = Exercises::gripTypes;
$gripsMenu = menu($gripTypes,'grip','',FALSE);
$userPosition = Exercises::userPositions;
$userPositionsMenu = menu($userPosition,'user_position','', FALSE);

