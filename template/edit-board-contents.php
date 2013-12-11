<?php
global $page_data, $block;
?>

<div id="sidebar">
    <?php if (isset($page_data['thegither-policy'])) : ?>
        <div class="board-description"><?php echo htmlspecialchars($page_data['thegither-policy']); ?></div>
    <?php else : ?>
        <div class="board-description">TERMS OF USE

By creating a board on this site, or otherwise using this service, you automatically agree to these terms of use:

* The administrators can do whatever they want with your boards and your data, and their decisions are final.

* You will immediately comply with any requests from the administrators regarding your boards or the data you keep on this website.

* The administrators can view, modify, or delete your boards and your data without your permission.

Of course, the administrators are actually pretty good guys.  If there are any issues, just talk to us.  We can probably work it out.</div> <!-- /board-description -->
    <?php endif; ?>
</div> <!-- /sidebar -->
<div id="contents" class="with-sidebar">
    <form id="edit-board-form" class="board-form" method="POST" >
        <?php if (isset($page_data['board-id'])) : ?>
            <input type="hidden" name="board-id" value="<?php echo htmlspecialchars($page_data['board-id']); ?>" />
        <?php endif; ?>
        <div class="form-field">
            <div class="field-label">Board Title:</div>
            <div class="field-control">
                <input type="text" name="title-field" value="<?php echo htmlspecialchars($page_data['board-title']); ?>">
                <div class='user-hint'>This will be the title at the top of your thegither board.</div>
            </div> <!-- /field-control -->
        </div> <!-- /form-field -->
        <div class="form-field">
            <div class="field-label">"Have" label:</div>
            <div class="field-control">
                <input type="text" name="have-label-field" value="<?php echo htmlspecialchars($page_data['have-label']); ?>">
                <div class='user-hint'>
                    This is the label for the "Have" entries. For example, on a lost and found board, this
                    might be "FOUND".
                </div>
            </div> <!-- /field-control -->
        </div> <!-- /form-field -->
        <div class="form-field">
            <div class="field-label">"Want" label:</div>
            <div class="field-control">
                <input type="text" name="want-label-field" value="<?php echo htmlspecialchars($page_data['want-label']); ?>">
                <div class='user-hint'>
                    This is the label for the "Want" entries. For example, on a lost and found board, this
                    might be "LOST".
                </div>
            </div> <!-- /field-control -->
        </div> <!-- want-label-field -->
        <div class="form-field">
            <div class="field-label">Publish:</div>
            <div class="field-control">
                <input type="checkbox" name="publish-checkbox" value="publish" <?php echo $page_data['published']; ?> >
                <div class='user-hint'>
                    Check this box when you're ready to publish your thegither board.   Uncheck this box
                    to unpublish the board again.   Unpublishing a board does not delete its entries; it
                    only makes the board unavailable to users.
                </div>
            </div> <!-- /field-control -->
        </div> <!-- publish-checkbox -->
        <div class="form-field">
            <div class="field-label">Close to posts:</div>
            <div class="field-control">
                <input type="checkbox" name="closed-checkbox" value="closed" <?php echo $page_data['closed']; ?> >
                <div class='user-hint'>
                    Check this box to disallow new posts.  This board will still be viewable if the Publish checkbox is checked, but new posts to this board will not be allowed.
                </div>
            </div> <!-- /field-control -->
        </div> <!-- login-checkbox -->
        <div class="form-field">
            <div class="field-label">Page description:</div>
            <div class="field-control">
                <textarea name="description-field" rows=5><?php echo htmlspecialchars($page_data['description']); ?></textarea>
                <div class='user-hint'>
                    Provide a short description of your board; for example, identify your organization
                    say what your board is for, list any basic rules for using your board.
                </div>
            </div> <!-- /field-control -->
        </div> <!-- description-field -->
        <div class='controls-div'>
            <input type="submit" value="Save Board">
        </div>
    </form>
</div> <!-- /contents -->
