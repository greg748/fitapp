<?php
require_once '../../init.php';
use Fitapp\classes\Workouts;

$id = $_REQUEST['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['created_date'])) {
        $_POST['created_date'] = date('Y-m-d', strtotime($_POST['created_date']));
    } else {
        $_POST['created_date'] = date('Y-m-d');
    }
      
    if ($id) {
        $Workout = Workouts::get($id);
        $Workout->setFields($_POST, TRUE);
        $Workout->save();
        // error check
        if (!$Workout->isSaved()) {
            echo "<pre>Save not successful<br>".$Workout->lastSql()."<pre><br>";
            echo $Workout->errorMsg();
            print_r($Workout->problemFields());
            die;
        }
    } else {
        $Workout = Workouts::create($_POST, TRUE);
        if (!$Workout) {
            echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
            die;
        }
        $id = $Workout->getField('id');
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
require_once 'workout_input_form.php';
