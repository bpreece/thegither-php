<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once('model/user.php');
require_once('model/session.php');

use Session;
use User;

$page_data = array(
    'continue' => 'signon.php',
    'user-email' => '',
);

switch ($_SERVER['REQUEST_METHOD']) {
    
    case 'POST':
        $page_data['continue'] = $_POST['continue'];
        $page_data['user-email'] = $_POST['user-email'];
        $user = User::lookup_by_signon($_POST['user-email'], $_POST['user-password']);
        if ($user) {
            $session = Session::create($user->id);
            if ($session) {
                setcookie('session-id', $session->id, time() + 14400, '/');
                header("Location: {$page_data['continue']}");
                return;
            } else {
                $page_data['message'] = "Failed creating a session: " . DB::error();
                $page_data['message-class'] = 'error';
            }
        } else {
            $page_data['message'] = "We couldn't locate your account.  Please check your email and password and try again.";
            $page_data['message-class'] = 'error';
        }
        break;
    
    case 'GET':
        if (isset($_GET['continue'])) {
            $page_data['continue'] = $_GET['continue'];
        }
        break;
    
    default:
        break;
}

?>


<!DOCTYPE html>
<html>
    <head>
        <title>Sign on</title>
        <link rel="stylesheet" type="text/css" href="css/signon.css">
    </head>
    <body>
        <div class="page">
            <h1>Thegither Sign-on</h1>
            <div class="user-help">Sign on with your account information.</div>
            <div id="form_wrapper">
                <img src="img/anonymous-128x128.png">
                <form id="signon_form" method="POST">
                    <?php if (isset($page_data['message'])): ?>
                    <div class="message <?php echo $page_data['message']; ?>">
                        <?php echo $page_data['message']; ?>
                    </div>
                    <?php endif; ?>
                    <input type="hidden" id="continue" name="continue" value="<?php echo $page_data['continue']; ?>">
                    <div class="form-field">
                        <input type="text" id="user_email" name="user-email" 
                               value="<?php echo $page_data['user-email']; ?>" placeholder="Email">
                    </div>
                    <div class="form-field">
                        <input type="password" id="user_password" name="user-password" placeholder="Password">
                    </div>
                    <div class="form-field">
                        <input type="submit" value="Sign on">
                    </div>
                </form>
            </div>
            <div class="user-help">
                <a href="register.php?continue=<?php echo htmlspecialchars_decode($page_data['continue']); ?>">Create an account</a>
            </div>
        </div>
    </body>
</html>
