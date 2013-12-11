<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once('model/user.php');
require_once('model/session.php');

global $user, $page_data;
$page_data = array(
    'page-title' => 'Thegither',
    'body-class' => 'main',
    'sign-on-url' => 'index.php',
    'sign-off-url' => 'index.php',
);
if (($user = Session::current_user())) {
    $page_data['user-id'] = $user->id;
    $page_data['user-email'] = $user->email;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/thegither.css" media="all" />
        <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Capriola'>
        <title>Thegither <?php echo $page_data['page-title']; ?></title>
    </head>
    <body class="<?php echo $page_data['body-class']; ?>">
        <div id="main_wrapper">
            <div id="header">
                <div id="left_logo"><a href=""><img src="img/Shield.png"/></a></div>
                <?php if ($user && $user->is_admin) : ?>
                    <div id="right_logo"><a href="boards.php"><img src="img/Shield.png"/></a></div>
                <?php else : ?>
                    <div id="right_logo"><img src="img/Shield.png"/></div>
                <?php endif; ?>
                <div id="title"><a href="/">Thegither</a></div>
                <div id="slogan">A' thegither they will sup!</div>
            </div> <!-- /header -->
            <div id="page_contents">
                <div id="main_menu">
                <?php if (isset($block['main-menu'])) : ?>
                    <?php echo $block['main-menu']; ?>
                    <?php else : ?>
                        <?php require_once('template/main_menu.php'); ?>
                    <?php endif; ?>
                </div> <!-- /main-menu -->
                <?php if (isset($page_data['user-id'])) : ?>
                    <div id="user_id">
                        <a href="user-boards.php"><?php echo htmlspecialchars($page_data['user-email']); ?></a>
                    </div> <!-- /user-id -->
                <?php endif; ?>
                <div id="main_contents">
                    <?php if (isset($block['intro'])) : ?>
                    <?php echo $block['intro']; ?>
                    <?php else : ?>
                        <div id="page_intro">
                            <h1 class="page-title"><?= htmlspecialchars($page_data['page-title']) ?></h1>
                        </div> <!-- /page-intro -->
                    <?php endif; ?>
                    <?php if (isset($block['page-menu'])) : ?>
                        <?php echo $block['page-menu']; ?>
                    <?php endif; ?>
                    <div id="front_page">
                        <div class='blare'>
                            <div style="font-size: 200%">Connect all the peoples!</div>
                            <div style="font-size: 150%">All your people are belong to you!</div>
                            <?php if (!isset($page_data['user-id'])) : ?>
                                <div style="font-family:Times New Roman, serif;font-style:italic">Sign on to start.</div>
                            <?php endif; ?>
                        </div>
                        <div class='exhortation'>
                            Want to check us out first?  Click on a sample page to see a what you could already be doing!
                        </div>
                        <div class='link-images'>
                            <div class='link-image'>
                                <a href='demo/board-ag1kZXZ-dGhlZ2l0aGlychILEgVCb2FyZBiAgICAgICACgw.html'>
                                    <img src="img/job-board.png"><br>
                                    <span class='label'>Job Board</span>
                                </a>
                            </div><div class='link-image'>
                                <a href='demo/board-ag1kZXZ-dGhlZ2l0aGlychILEgVCb2FyZBiAgICAgOCXCgw.html'>
                                    <img src="img/Lost-and-found.png"><br>
                                    <span class='label'>Lost &amp; Found</span>
                                </a>
                            </div><div class='link-image'>
                                <a href='demo/board-ag1kZXZ-dGhlZ2l0aGlychILEgVCb2FyZBiAgICAgNC7Cgw.html'>
                                    <img src="img/Rideshare.png"><br>
                                    <span class='label'>Rideshare</span>
                                </a>
                            </div>
                        </div>
                    </div> <!-- /front_page -->
                </div> <!-- /main-contents -->
            </div> <!-- /page-contents -->
            <div id="footer">
                <?php if (isset($block['footer'])) : ?>
                    <?php echo $block['footer']; ?>
                <?php else : ?>
                    <div id="copyright">Copyright 2013.   All rights reserved.</div>
                    <div><a href="mailto:ben@bpreece.com">Contact me</a></div>
                <?php endif; ?>
            </div> <!-- /footer -->
        </div> <!-- /main-wrapper -->
    </body>
</html>
