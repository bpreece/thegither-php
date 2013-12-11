<?php
global $page_data, $block;
?>

<form id="post_response" method="POST">
    <h3>Respond to this post:</h3>
    <input type="hidden" name="post-id" value="<?php echo htmlspecialchars($page_data['post-id']); ?>"/>
    <div class='form-field'>
        <textarea name="response-text" id="response_text" rows=5></textarea>
        <div class="user-hint">
            When you respond to this post, your Google account email
            address will be provided to the poster.
        </div>
    </div>
    <div style="margin-top: 0.5em">
        <input type="submit" value="Submit response"/>
    </div>
</form>
