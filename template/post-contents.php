<?php
global $page_data, $block;
?>

<div id="contents" class="with-sidebar">
    <div id="post_contents">
        <div id="post_timestamp">
            Posted on: <?php echo $page_data['post-timestamp']; ?>
        </div>
        <div id="post_summary">
            <span class="post-type <?php echo $page_data['post-type']; ?>" ><?php echo $page_data['post-type-label']; ?>:</span> 
            <?php echo htmlspecialchars($page_data['post-summary']); ?>
        </div>

        <?php if (isset($page_data['post-details'])) : ?>
            <div id="post_details"><?php echo htmlspecialchars($page_data['post-details']); ?></div>
        <?php endif; ?>

        <?php if (isset($block['secondary'])) : ?>
            <h2>Block secondary</h2>
            <?php include $block['secondary']; ?>
        <?php endif; ?>
    </div> <!-- /post_contents -->
</div> <!-- /contents -->
