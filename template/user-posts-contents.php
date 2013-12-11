<?php
global $page_data, $block;
?>

<div id="user_posts">
    <?php if (isset($page_data['posts'])) : ?>
        <ul class="post-list">
            <?php foreach ($page_data['posts'] as $post) : ?>
                <li class="<?php echo $post->type; ?>">
                    <a href="post?id=<?php echo urlencode($post->id); ?>">
                        <span class='post-summary'><?php echo htmlspecialchars($post->summary); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul> <!-- post-list -->
        <?php if (isset($page_data['x'])) : ?>
            <form method="get">
                <input type="hidden" name="x" value="<?php echo htmlspecialchars($page_data['x']); ?>">
                <input type="submit" value="Next">
            </form>
        <?php endif; // x ?>
    <?php else : // posts ?>
        <div class="user-hint">
            <?php echo $page_data['postings-message']; ?>
        </div>
    <?php endif; // posts ?>
</div> <!-- /user_posts -->
