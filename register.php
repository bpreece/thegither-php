<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/db.php';
require_once 'model/user.php';
require_once 'model/session.php';

$page_data = array(
    'user-email' => '',
    'continue' => '/',
    'password-class' => '',
);

switch ($_SERVER['REQUEST_METHOD']) {

    case 'POST':
        $page_data['user-email'] = $_POST['user-email'];
        $page_data['continue'] = $_POST['continue'];
        if ($_POST['user-password'] != $_POST['user-password-2']) {
            $page_data['message'] = "The passwords do not match.";
            $page_data['message-class'] = 'error';
            $page_data['password-class'] = 'error';
        } else {
            $user = User::create($_POST['user-password'], $_POST['user-email']);
            if ($user) {
                $session = Session::create($user->id);
                if ($session) {
                    setcookie('session-id', $session->id);
                    header("Location: {$page_data['continue']}");
                    return;
                } else {
                    $page_data['message'] = "Failed creating a session for user " . $user->id . ": " . DB::error();
                    $page_data['message-class'] = 'error';
                }
            } else {
                $page_data['message'] = DB::error();
                $page_data['message-class'] = 'message error';
            }
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
            <div class="user-help">Register with your email to create an account.</div>
            <div id="form_wrapper">
                <img src="img/anonymous-128x128.png">
                <form id="signon_form" method="POST">
                    <input type="hidden" id="continue" name="continue" value="<?php echo $page_data['continue']; ?>">
                    <?php if (isset($page_data['message'])) : ?>
                    <div class="message <?php echo $page_data['message-class']; ?>">
                        <?php echo $page_data['message']; ?>
                    </div>
                    <?php endif; ?>
                    <div class="form-field">
                        <input type="text" id="user_email" name="user-email" 
                               value="<?php echo $page_data['user-email']; ?>" placeholder="Email">
                    </div>
                    <div class="form-field">
                        <input class="<?php echo $page_data['password-class'] ?>" type="password" id="user_password" name="user-password" placeholder="Password">
                    </div>
                    <div class="form-field">
                        <input class="<?php echo $page_data['password-class'] ?>" type="password" id="user_password" name="user-password-2" placeholder="Confirm password">
                    </div>
                    <div class="form-field">
                        <input type="submit" value="Register">
                    </div>
                </form>
            </div>
            <div class="user-help">
                Do you already have an account?</br><a href="signon.php?continue=<?php echo htmlspecialchars($page_data['continue']); ?>">Sign on</a>
            </div>
        </div>
    </body>
</html>
