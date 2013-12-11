
<?php
global $block, $page_data;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="css/layout.css" media="all" />
        <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Noto+Sans'>
        <style>
            #header { font-family: Noto Sans, sans-serif }
            div.debug-message.error { color: red; font-size: 200% }
        </style>
        <?php
        if (isset($block['head'])) {
            include "{$block['head']}";
        }
        ?>
        <title><?php echo $page_data['page-title']; ?></title>
    </head>
    <body class="<?php echo $page_data['body-class']; ?>">
        <div id="main_wrapper">

            <?php
            // The header by default contains only the title of the page.  This
            // can be overridden by defining the HTML contents in 
            // $page_data['header'] or an include file in $block['header'].
            ?>
            <?php if (isset($page_data['header'])) : ?>
                <div id="header">
                    <?php echo $page_data['header']; ?>
                </div> <!-- /header -->
            <?php elseif (isset($block['header'])) : ?>
                <?php include "{$block['header']}"; ?>
            <?php else : ?>
                <div id="header">
                    <?php if (isset($page_data['title-link'])) : ?>
                        <div id="title"><a href="<?php echo $page_data['title-link']; ?>"><?php echo htmlspecialchars($page_data['page-title']); ?></a></div>
                    <?php else : // title-link ?>
                        <div id="title"><?php echo htmlspecialchars($page_data['page-title']); ?></div>
                    <?php endif; // title-link ?>
                </div> <!-- /header -->
            <?php endif; ?>

            <?php
            // The main menu appears just below the header
            ?>
            <?php if (isset($page_data['main-menu'])) : ?>
                <ul id='main_menu' class="menu-list right-menu">
                    <?php foreach ($page_data['main-menu'] as $menu_text => $menu_link) : ?>
                        <li class="menu-item">
                            <a href="<?php echo $menu_link; ?>">
                                <?php echo $menu_text; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php elseif (isset($block['main-menu'])) : ?>
                <div class='menu'>
                    <?php include "{$block['main-menu']}"; ?>
                </div> <!-- /menu -->
            <?php endif; ?>

            <?php
            // if the user is signed on, then putting his email or other
            // identification in $page_data['user-id'] will cause it to be show.
            ?>
            <?php if (isset($page_data['user-id'])) : ?>
                <div id="user_id">
                    <a href="user-boards.php"><?php echo htmlspecialchars($page_data['user-email']); ?></a>
                </div> <!-- /user-id -->
            <?php endif; ?>

            <div id="page_contents">

                <?php
                // The intro may contain for example, a subtitle, page
                // metadata, or other content suitable to start the page.
                ?>
                <?php if (isset($page_data['intro'])) : ?>
                    <div class='page-intro'>
                        <?php echo $page_data['intro']; ?>
                    </div> <!-- /intro -->
                <?php elseif (isset($block['intro'])) : ?>
                    <?php include "{$block['intro']}"; ?>
                <?php endif; ?>

                <?php
                // The page menu contains content-specific menu items, as
                // opposed to the main menu, which contains menu items
                // common to all pages.
                ?>
                <?php if (isset($page_data['page-menu'])) : ?>
                    <ul id='page_menu'>
                        <?php foreach ($page_data['page-menu'] as $menu_text => $menu_link) : ?>
                            <li class="menu-item">
                                <a href="<?php echo $menu_link; ?>">
                                    <?php echo $menu_text; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif (isset($block['page-menu'])) : ?>
                    <?php include "{$block['page-menu']}"; ?>
                <?php endif; ?>

                <div id="main_block">

                    <?php if (isset($block['sidebar'])) : ?>
                        <div id="sidebar" class="<?php echo $page_data['sidebar-class']; ?>"><?php include "{$block['sidebar']}"; ?></div> <!-- /sidebar -->
                    <?php elseif (isset($page_data['sidebar'])) : // sidebar ?>
                        <div id="sidebar" class="<?php echo $page_data['sidebar-class']; ?>"><?php echo htmlspecialchars($page_data['sidebar']); ?></div> <!-- /sidebar -->
                    <?php endif; // sidebar ?>

                    <?php if (isset($page_data['user-messages'])) : ?>
                        <div id="user_messages">
                            <?php foreach ($page_data['user-messages'] as $message) : ?>
                                <div class="user-message <?php echo $message['class']; ?>">
                                    <?php echo $message['text']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div> <!-- /user-messages -->
                    <?php endif; ?>

                    <?php if (isset($block['contents'])) : ?>
                        <?php include "{$block['contents']}"; ?>
                    <?php else : // contents ?>
                        <div class="debug-message error">
                            --- NEED TO DEFINE THE MAIN CONTENTS ---
                        </div>
                    <?php endif; // contents ?>
                </div>

            </div> <!-- /page-contents -->

            <div id="footer">
                <?php if (isset($block['footer'])) : ?>
                    <?php include "{$block['footer']}"; ?>
                <?php else : ?>
                    <a href="index.php">A' thegither they will sup.</a>
                <?php endif; ?>
            </div> <!-- /footer -->
        </div> <!-- /main-wrapper -->
    </body>
</html>
