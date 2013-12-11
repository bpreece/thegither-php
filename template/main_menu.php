<?php 
global $page_data, $block;
?>

<?php if ($user) : ?>
    <ul class="menu-list right-menu">
        <li class="menu-item"><a href="user-boards.php">My Boards</a></li>
        <li class="menu-item"><a href="user-posts.php">My Posts</a></li>
        <li class="menu-item">
            <a href="signoff.php?continue=<?php echo urlencode($page_data['sign-off-url']); ?>">
                Sign off
            </a>
        </li>
    </ul> <!-- /right-menu -->
<?php else : ?>
    <ul class="menu-list right-menu">
        <li class="menu-item">
            <a href="signon.php?continue=<?php echo urlencode($page_data['sign-on-url']); ?>">
                Sign on
            </a>
        </li>
    </ul> <!-- /left-menu -->
<?php endif; ?>
