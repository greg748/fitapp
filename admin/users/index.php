<?php

require_once '../../init.php';
use Fitapp\classes\Users;
use Fitapp\classes\Regimens;
use Fitapp\tools\Template;

$users = Users::getAll();
Template::startPage('Users');
?>
<table class="display">
    <tr>
    <th>Username</th>
    <th>Name</th>
    <th>Email</th>
    <th>Created</th>
    <th>Last Login</th>
    </tr>
    <?php
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td><a href=\"edit.php?id={$u['id']}\">{$u['username']}</a> ({$u['id']})</td>";
        echo "<td>{$u['firstname']} {$u['lastname']}</td>";
        echo "<td>{$u['email']}</td>";     
        echo "<td>{$u['created']}</td>";        
        echo "<td>{$u['last_login']}</td>";
        echo "</tr>\n";
        
        $regimens = Regimens::getActiveForUser($u['id']);
        if (count($regimens) > 0) {
            foreach ($regimens as $r) {
                echo "<tr><td colspan='5'>";
                echo "<h4>Regimen {$r['name']} {$r['regimen_type']}</h4>";
                echo "<table class=\"display\">";
                foreach ($r['workouts'] as $rw) {
                    echo "<tr>";
                    echo "<td>{$rw['workout_type']}</td>";     
                    echo "<td>{$rw['add_date']}</td>";  
                    if ($rw['workout_id'] > 0) {
                        echo "<td><a href=\"../workouts/one.php?id={$rw['workout_id']}\">{$rw['workout_name']}</a> ({$rw['workout_id']})</td>";
                    } else {
                        echo "<td><a href=\"../workouts/edit.php?rw_id={$rw['regimen_workout_id']}\">Build</a></td>";
                    }
                    echo "<td>".print_r($rw, TRUE) . "</td>";
                   // echo "<td>{$u['firstname']} {$u['lastname']}</td>";
                   // print_pre($rw);
                    echo "</tr>\n";
                }
                echo "</table>";
                echo "</tr>\n";
            }
        }
    }
    ?>
    </table>
<?php
Template::endPage();