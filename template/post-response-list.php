<?php
global $page_data, $block;
?>

<h3>Responses</h3>
<?php if (isset($page_data['response-list'])) : ?>
    <ul class="response-list">
        <?php foreach ($page_data['response-list'] as $response) : ?>
            <li>
                <?php echo htmlspecialchars($response->contents); ?> &mdash;
                <a href="mailto:<?php echo urlencode($response->user_email); ?>">
                    <?php echo htmlspecialchars($response->user_email); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php if (isset($page_data['x'])) : ?>
        <form method="get">
            <input type="hidden" name="id" value="<?php echo $page_data['post-id']; ?>">
            <input type="hidden" name="x" value="<?php echo $page_data['x']; ?>">
            <input type="submit" value="Next">
        </form>
    <?php endif; // x ?>
<?php else : // response-list ?>
    <div class="user-hint">
        <?php echo $page_data['response-message']; ?>
    </div>
<?php endif; // response-list ?>
