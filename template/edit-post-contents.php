<?php
global $page_data, $block;
?>

<div id="sidebar">
    <div class="board-description"><?php echo htmlspecialchars($page_data['board-description']); ?></div>
</div> <!-- /sidebar -->
<div id="contents" class="with-sidebar">
    <form id="edit-board-form" method="POST" class="board-form">
        <?php if (isset($page_data['posting-id'])) : ?>
        <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($page_data['posting-id']); ?>"/>
        <?php endif; ?>
        <input type="hidden" id="board-id" name="board-id" value="<?php echo htmlspecialchars($page_data['board-id']); ?>" />
        <div class="form-field">
            <div class="field-label">Type of post:</div>
            <div class="field-control">
                <input type="radio" name="post-type" id="have_type" value="have" <?php echo $page_data['have-checked']; ?>>
                <label for="have_type"><?php echo htmlspecialchars($page_data['have-label']); ?></label></br>
                <input type="radio" name="post-type" id="want_type" value="want" <?php echo $page_data['want-checked']; ?>>
                <label for="want_type"><?php echo htmlspecialchars($page_data['want-label']); ?></label>
                <div class='user-hint'>
                    Check the appropriate button to indicate whether this is a
                    "<?php echo htmlspecialchars($page_data['have-label']); ?>" 
                    or a "<?php echo htmlspecialchars($page_data['want-label']); ?>" post.
                </div>  
            </div> <!-- field-control -->
        </div> <!-- login-checkbox -->
        <div class="form-field">
            <div class="field-label">Summary:</div>
            <div class="field-control">
                <input type="text" name="summary-field" value="<?php echo htmlspecialchars($page_data['summary']); ?>">
                <div class='user-hint'>
                    Provide a short (one or two sentences) summary.
                </div>  
            </div> <!-- field-control -->
        </div> <!-- title-field -->
        <div class="form-field">
            <div class="field-label">Information:</div>
            <div class="field-control">
                <textarea name="details-field" rows="4"><?php echo htmlspecialchars($page_data['details']); ?></textarea>
                <div class='user-hint'>
                    Optionally provide additional information about your posting.
                </div>  
            </div> <!-- field-control -->
            <div class="form-field">
                <div class="field-label">Publish:</div>
                <div class="field-control">
                    <input type="checkbox" name="published-checkbox" <?php echo $page_data['published']; ?> >
                    <div class='user-hint'>
                        Check this box when you're ready to publish this post.   
                        Uncheck this box to unpublish the post again.   Unpublishing
                        a post does not delete the post; it only makes the post
                        unavailable for other users to see.
                    </div>
                </div> <!-- field-control -->
            </div> <!-- published-checkbox -->
            <div class="form-field">
                <div class="field-label">Close to responses:</div>
                <div class="field-control">
                    <input type="checkbox" name="closed-checkbox" value="closed" <?php echo $page_data['closed']; ?>>
                    <div class='user-hint'>
                        Check this box to disallow new responses.  This post will still
                        be viewable if the Publish checkbox is checked, but new responses
                        to this post will not be allowed.
                    </div>
                </div> <!-- /field-control -->
            </div> <!-- closed-checkbox -->
            <div class='controls-div'>
                <input type="submit" value="Submit Post">
                <input type="submit" value="Cancel">
            </div>
    </form>
</div> <!-- /contents -->
