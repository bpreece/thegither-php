<?php
global $page_data, $block;
?>

<div id="user_boards">
    <?php if (isset($page_data['boards'])) : ?>
        <ul class="board-list">
            <?php foreach ($page_data['boards'] as $board) : ?>
                <li>
                    <a href="board?id=<?php echo $board->id; ?>">
                        <span class="board-title"><?php echo htmlspecialchars($board->title); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul> <!-- /board-list -->
        <?php if (isset($page_data['x'])) : ?>
            <form method=get>
                <input type="hidden" name="x" value="<?php echo $page_data['x']; ?>">
                <input type="submit" value="Next">
            </form>
        <?php endif; // x ?>
    <?php else : // $boards ?>
        <div class="user-hint">
            <?php echo $page_data['boards-message']; ?>
        </div>
    <?php endif; // $boards ?>
</div> <!-- /user_boards -->
