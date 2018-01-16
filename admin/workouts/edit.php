<?php
require_once '../../init.php';
use Fitapp\classes\Workouts;
use Fitapp\tools\Template;
use Fitapp\classes\RegimenWorkouts;

global $rw_id;
$id = $_REQUEST['id'];
if (isset($_REQUEST['rw_id'])) {
    $rw_id = $_REQUEST['rw_id'];
    $RWO = RegimenWorkouts::get($rw_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['created_date'])) {
        $_POST['created_date'] = date('Y-m-d', strtotime($_POST['created_date']));
    } else {
        $_POST['created_date'] = date('Y-m-d');
    }
      
    if ($id) {
        $Workout = Workouts::get($id);
        $Workout->setFields($_POST);
        $Workout->save();
        // error check
        if (!$Workout->isSaved()) {
            echo "<pre>Save not successful<br>".$Workout->lastSql()."<pre><br>";
            echo $Workout->errorMsg();
            print_r($Workout->problemFields());
            die;
        }
        if (isset($rw_id)) {
            $RWO->setField('workout_id',$id);
            $RWO->save();
        }
    } else {
        $Workout = Workouts::create($_POST);
        if (!$Workout) {
            echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
            die;
        }
        $id = $Workout->getField('id');
        if (isset($rw_id)) {
            $RWO->setField('workout_id',$id);
            $RWO->save();
        }
        if (isset($_POST['createAdd'])) {
            header("Location: /admin/exercises/edit.php?workout_id={$id}");
            die;
        }
        header("Location: /admin/workouts/index.php?id={$id}");
        die;
    }

} else {
    if (isset($id)) {
        $w = Workouts::get($id)->getFields();
    } else {
        $w = [];
    }
}
if (isset($RWO) && $w['workout_type'] == '') {
    $regimen = $RWO->getRegimenData();
    $w['workout_type'] = $RWO->getField('workout_type');
    $w['created_by'] = $regimen['created_by'];
    $w['user_id'] = $regimen['user_id'];
}
Template::startPage('Edit Workout');
require_once 'workout_input_form.php';
Template::endPage();
