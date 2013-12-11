<?php

/**
 * 
 */

require_once('model/db.php');

$page_data = array(
);

switch ($_SERVER['REQUEST_METHOD']) {
    
    case 'POST':
        $result = DbInstaller::create_all($_POST['sql-signon'], $_POST['sql-password'], $_POST['admin-email'], $_POST['admin-password']);
        $page_data['status-message'] = $result['status'];
        $page_data['status-class'] = $result['connection'] ? 'status good' : 'status error';
        break;
    
    case 'GET':
        break;
    
    default:
        $page_data['status-message'] = "{$_SERVER['REQUEST_METHOD']} is not supported";
        $page_data['status-class'] = 'status error';
        break;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Thegither Installation</title>
        <link rel="stylesheet" type="text/css" href="css/install.css">
    </head>
    <body>
        <h1>Thegither Installation</h1>
        <div class="<?php echo $page_data['status-class']; ?>">
            <?php echo $page_data['status-message']; ?>
        </div>
        <form method="POST">
            <div class="form-field">
                <label for="sql_signon"> SQL Sign-on:</label>
                <div class="input-wrapper">
                    <input type="text" id="sql_signon" name="sql-signon" 
                           value="<?php echo $_POST['sql-signon'] ?>" placeholder="SQL sign-on"/>
                </div>
            </div>
            <div class="form-field">
                <label for="sql_password"> SQL Password:</label>
                <div class="input-wrapper">
                    <input type="password" id="sql_password" name="sql-password" 
                           placeholder="SQL password"/>
                </div>
            </div>
            <div class="form-field">
                <label for="admin_email"> Admin Email:</label>
                <div class="input-wrapper">
                    <input type="text" id="admin_email" name="admin-email" 
                           value="<?php echo $_POST['admin-email'] ?>" placeholder="admin email"/>
                </div>
            </div>
            <div class="form-field">
                <label for="admin_password"> Admin Password:</label>
                <div class="input-wrapper">
                    <input type="password" id="admin_password" name="admin-password" 
                           placeholder="admin password"/>
                </div>
            </div>
            <div class='form-controls'>
                <input type='submit' value='Create database'>
            </div>
        </form>
    </body>
</html>