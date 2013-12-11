<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';

$page_data = array();

if (isset($_COOKIE['session-id'])) {
    if (Session::close_by_id($_COOKIE['session-id'])) {
        if (isset($_POST['continue'])) {
            header("Location: {$_POST['continue']}");
            return;
        } else if (isset($_GET['continue'])) {
            header("Location: {$_GET['continue']}");
            return;
        } else {
            $page_data['message'] = 'You have been signed off';
            $page_data['message-class'] = 'info';
        }
    } else {
        $page_data['message'] = 'Sign-off failed: ' . DB::error();
        $page_data['message-class'] = 'error';
    }
} else {
    $page_data['message'] = 'You were not signed on';
    $page_data['message-class'] = 'error';
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Sign on</title>
        <link rel="stylesheet" type="text/css" href="css/signon.css">
    </head>
    <body>
        <div class="message <?php echo $page_data['message-class']; ?>">
            <?php echo $page_data['message']; ?>
        </div>
    </body>
</html>
