<?php
global $page_data, $block;
?>

<div id="contents" class="with-sidebar">
    <?php if (isset($page_data['posts'])) : ?>
        <ul class="post-list">
            <?php foreach ($page_data['posts'] as $post) : ?>
                <li class="<?php echo $post->type; ?>">
                    <div  class="border-div" onclick="location.href = 'post.php?id=<?php echo urlencode($post->id); ?>'">
                        <div class="post-type"><?php echo htmlspecialchars($page_data['labels'][$post->type]); ?>:</div>
                        <div class="post-summary <?php echo $post->type; ?>"><?php echo htmlspecialchars($post->summary); ?></div>
                        <div class="post-details"><?php echo htmlspecialchars($post->details); ?></div>
                    </div> <!-- border-div -->
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if (isset($page_data['x'])) : ?>
            <div style="margin-left:10px">
                <form method="get">
                    <input type="hidden" name="id" value="<?php echo $page_data['board-id']; ?>">
                    <input type="hidden" name="x" value="<?php echo $x; ?>">
                    <input type="submit" value="More">
                </form>
            </div>
        <?php endif; // x  ?>
    <?php else : // posts  ?>
        <div id="board_contents" class="user-hint">
            <?php echo $page_data['postings-message']; ?>
        </div>
    <?php endif; // posts  ?>
    <div style="clear:both"></div>
</div> <!-- /contents -->
