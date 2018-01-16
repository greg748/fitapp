<?php
require_once '../../init.php';

use Fitapp\classes\Users;
use Fitapp\tools\Template;

$id = $_REQUEST['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['created_date'])) {
        $_POST['created_date'] = date('Y-m-d', strtotime($_POST['created_date']));
    } else {
        $_POST['created_date'] = date('Y-m-d');
    }
    
    if ($id) {
        $User = Users::get($id);
        $User->setFields($_POST);
        $User->save();
        // error check
        if (!$User->isSaved()) {
            echo "<pre>Save not successful<br>".$User->lastSql()."<pre><br>";
            echo $User->errorMsg();
            print_r($User->problemFields());
            die;
        }
    } else {
        $User = Users::create($_POST);
        if (!$User) {
            echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
            die;
        }
        $id = $User->getField('id');
        header("Location: /admin/users/index.php?id={$id}");
        die;
    }
    
} else {
    if (isset($id)) {
        $u = Users::get($id)->getFields();
    } else {
        $u = [];
    }
}

if ($id) {
    Template::startPage("Edit User");
    echo "<h3>Edit User {$u['username']}</h3>";
} else {
    Template::startPage("New User");
    echo "<h3>New User</h3>";
}
?>

<form method="POST" action="edit.php">
<table class="crud">
<tr>
    <th>Username</th>
    <td><input type="text" name="username" value="<?=$u['username']; ?>"/></td>
</tr>
<tr>
    <th>First Name</th>
    <td><input type="text" name="firstname" value="<?=$u['firstname']; ?>"/></td>
</tr>
<tr>
    <th>Last Name</th>
    <td><input type="text" name="lastname" value="<?=$u['lastname']; ?>"/></td>
</tr>
<tr>
    <th>Email</th>
    <td><input type="text" name="email" value="<?=$u['email']; ?>"/> @todo add confirm</td>
</tr>
<tr>
    <th>Password</th>
    <td><input type="text" name="password" value="<?=$u['password']; ?>"/> @todo add confirm, enter twice</td>
</tr>
<tr>
    <th>TimeZone</th>
    <td>Implemented later</td>
</tr>
</table>
<button name="save">Save</button>
</form>

<?php
Template::endPage();
