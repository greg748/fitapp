<?php
include_once '../../init.php';
use Fitapp\classes\ExerciseGroups;
use Fitapp\classes\WorkoutExercises;
use Fitapp\classes\Workouts;
use Fitapp\tools\Template;

$id = $_REQUEST['id'];
$referrer = $_SERVER['HTTP_REFERER'];

if (isset($_REQUEST['delete'])) {
    $Group = ExerciseGroups::get($id);
    $g = $Group->getFields();
    $Workout = Workouts::get($g['workout_id']);
    if ($Workout) {
        $exercises = $Workout->getExercises($g['id']);
        foreach ($exercises as $e) {
            $exercise_remove_id = $e['workout_exercise_id'];
            WorkoutExercises::removeFromGroup($exercise_remove_id);
        }
        $Workout->deleteGroup($g['id']);
        header("Location /admin/workouts/one.php?id={$g['workout_id']}");
        die;
    }
    header("Location $referrer");
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // @todo check to see if group is part of any workout_instances
    if ($id) {
        $Group = ExerciseGroups::get($id);
        $Group->setFields($_POST);
        $Group->save();
        // error check
        if (!$Group->isSaved()) {
            echo "<pre>Save not successful<br>".$Group->lastSql()."<pre><br>";
            echo $Group->errorMsg();
            print_r($Group->problemFields());
            die;
        }
        if (count($_POST['remove_exercises']) > 0) {
            foreach ($_POST['remove_exercises'] as $exercise_remove_id) {
                WorkoutExercises::removeFromGroup($exercise_remove_id);
            }
        }
        header("Location: /admin/groups/edit.php?id=$id");
    } else {
        $Group = ExerciseGroups::create($_POST);
        if (!$Group) {
            echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
            die;
        }
        $id = $Group->getField('id');
    }
} else {
    if (isset($id)) { 
        $Group = ExerciseGroups::get($id);
        $g = $Group->getFields();
        $Workout = Workouts::get($g['workout_id']);
        if ($Workout) {
            $exercises = $Workout->getExercises($g['id']);
        }
    } else {
        $g = [];
    }
}
$groupTypesMenu = menu(ExerciseGroups::$exercise_group_types, 'group_type', $g['group_type'], FALSE, TRUE, FALSE);

Template::startPage('Edit Group');
?>
<form action="edit.php" method="POST">
<input type="hidden" name="id" value="<?=$id;?>"/>
Group Type: <?=$groupTypesMenu;?><br>
Remove The checked exercises<br>
<?php
foreach ($exercises as $e) {
    // @todo check to see if part of any existing workout_instances
    echo "<input type=\"checkbox\" name=\"remove_exercises[]\" value=\"{$e['workout_exercise_id']}\">{$e['exercise_name']}</input><br>";
}
?>
<button value='save' name='save'>Save</button>
<button value='delete' name='delete'>Delete group</button>
</form>
<?php
Template::endPage();