<?php
/**
 * Add a regimen for a user, with defined workout types, which can then be filled in with actual workouts
 */
require_once '../../init.php';
use Fitapp\classes\Regimens;
use Fitapp\classes\Users;
use Fitapp\classes\RegimenWorkouts;
use Fitapp\tools\Template;
use Fitapp\tools\Functions;

$user_id = $_REQUEST['user_id'];

$regimenTypes = Regimens::$regimenTypes;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['created_date'])) {
        $_POST['created_date'] = date('Y-m-d', strtotime($_POST['created_date']));
    } else {
        $_POST['created_date'] = date('Y-m-d');
    }
    $_POST['regimen_workouts'] = $regimenTypes[$_POST['regimen_type']];
    print_pre($_POST); //debug
    
    if ($id) {
        $Regimen = Regimens::get($id);
        $Regimen->setFields($_POST);
        $Regimen->save();
        // error check
        if (!$Regimen->isSaved()) {
            echo "<pre>Save not successful<br>".$Regimen->lastSql()."<pre><br>";
            echo $Regimen->errorMsg();
            print_r($Regimen->problemFields());
            die;
        }
    } else {
        $Regimen = Regimens::create($_POST);
        if (!$Regimen) {
            echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
            die;
        }
        $id = $Regimen->getField('id');
        // Now created the workout types under it
        $regimenWorkoutData = ['regimen_id' => $id,
        'workout_type'=> 0,
        'add_date' => $_POST['created_date']];
        foreach ($_POST['regimen_workouts'] as $wtype) {
            $regimenWorkoutData['workout_type'] = $wtype;
            $RWO = RegimenWorkouts::create($regimenWorkoutData, TRUE);
            if (!$RWO) {
                echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
                die;
            }
        }
        die;// debug
        header("Location: /admin/regimens/one.php?id={$id}");
        die;
    }
    
} else {
    $regimenTypesMenu = menu(array_keys(Regimens::$regimenTypes), 'regimen_type','', true, false, false);
    if (isset($user_id)) {
        $u = Users::get($user_id)->getFields();
    }
}

if ($id) {
    Template::startPage("Add Regimen for Regimen");
    echo "<h3>Add Regiment for Regimen {$u['username']}</h3>";
} else {
    Template::startPage("New Regimen");
    echo "<h3>New Regimen</h3>";
}

?>

<form method="POST" action="add.php">
    <input type="hidden" name="user_id" value="<?=$u['id']; ?>"/>
<table class="crud">
    <tr><th>Regimen Name</th><td><input type="text" name="name" value=""/></td></tr>
    <tr><th>Regimen Type</th><td><?=$regimenTypesMenu; ?></td></tr>
    <tr><th>Created Date</th><td><input type="date" name="created_date" value=""/></td></tr>
</table>
<button name="save" value="save">Save</button>
