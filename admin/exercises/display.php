<?php
require_once ('../../init.php');
global $exercise;
?>
<div class="exercise">
<div class="exerciseName"><a href="/admin/exercises/edit.php?id=<?=$exercise['exercise_id']; ?>"><?=$exercise['exercise_name']; ?></a></div>
<div class="exerciseDesc"><?=$exercise['description']; ?></div>
<div class="primaryMuscle"><?=$exercise['primary_muscle_name']; ?></div>
<div class="secondaryMuscles"><?=$exercise['secondary_muscle_names']; ?></div>
<div class="equipment"><?=$exercise['equipment']; ?></div>
<div class="weightType"><?=$exercise['weight_type']; ?></div>
<div class="grip"><?=$exercise['grip']; ?></div>
<div class="user_position"><?=$exercise['user_position']; ?></div>
<div class="ability"><?=$exercise['ability_level']; ?></div>
</div>