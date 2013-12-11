<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';
require_once 'model/posting.php';

function doGet()
{
    global $page_data, $block;

    # The user must be signed on to create or edit this posting

    $user = Session::current_user();
    if (!is_object($user)) {
        if (isset($_GET['id'])) {
            header("Location: signon.php?continue=edit-post.php?id=" . urlencode($_GET['id']));
        } else if (isset($_GET['board-id'])) {
            header("Location: signon.php?continue=edit-post.php?board-id=" . urlencode($_GET['board-id']));
        } else {
            header("Location: signon.php?continue=edit-post.php");
        }
        return;
    }
    
    # Validate the query parameters.  If the query string in the URL includes
    # an ID, then we're editing an existing board;  otherwise we're creating
    # a new board.

    if (isset($_GET['id'])) {

        # Fetch the board from the datastore

        $post_id = $_GET['id'];
        $posting = Posting::query_by_id($post_id);
        if (! is_object($posting)) {
            header("Location: page-not-found.php");
            return;
        }

        # Verify that the user has permission to edit the board

        if (! $posting->is_writable_by_user($user)) {
            header("Location: posting.php?id=" . urlencode($post_id));
            return;
        }

        # Set the page's properties, and provide the values needed to
        # pre-populate the form fields

        $board = Board::query_by_id($posting->board_id);
        $page_data = array(
            'user-id' => $user->id,
            'user-email' => $user->email,
            'sign-off-url' => 'signoff.php?continue=thegither.php',
            'body-class' => 'edit-post',
            'page-title' => $posting->summary,
            'posting-id' =>$post_id,
            'board-id' => $board->id,
            'board-title' => $board->title,
            'board-description' => $board->description,
            'have-label' => $board->have_label,
            'want-label' => $board->want_label,
            'published' => $posting->published ? 'checked' : '',
            'closed' => $posting->open_for_response ? '' : 'checked',
            'summary' => $posting->summary,
            'details' => $posting->details,
            'have-checked' => $posting->type == 'have' ? 'checked' : '',
            'want-checked' => $posting->type == 'want' ? 'checked' : '',
            'intro' => '<h2>' . htmlspecialchars($posting->summary) . '</h2>',
        );

    } else if (isset($_GET['board-id'])) {
        
        # Set the page's properties, and provide the values needed to
        # pre-populate the form fields

        $board_id = $_GET['board-id'];
        $board = Board::query_by_id($board_id);
        if (!is_object($board)) {
            header("Location: page-not-found.php");
            return;
        }
        
        $page_data = array(
            'user-id' => $user->id,
            'user-email' => $user->email,
            'sign-off-url' => 'signoff.php?continue=thegither.php',
            'body-class' => 'edit-post',
            'page-title' => 'New Posting',
            'board-id' => $board->id,
            'board-title' => $board->title,
            'board-description' => $board->description,
            'have-label' => $board->have_label,
            'want-label' => $board->want_label,
            'published' => '',
            'closed' => 'checked',
            'summary' => '',
            'details' => '',
            'have-checked' => 'checked',
            'want-checked' => '',
            'intro' => '<h2>New Posting</h2>',
        );
        
    } else {

        # We don't know what board we're creating a posting for
        header("Location: page-not-found.php");
        return;

    }

    $block = array( 'contents' => "template/edit-post-contents.php" );
}

function doPost()
{
    # The user must be signed on to create or edit this posting

    $user = Session::current_user();
    if (!is_object($user)) {
        header("Location: page-not-found.php");
        return;
    }
    
    # If 'id' is set, then we're editing an existing posting; otherwise
    # 'board-id' must be set and we're creating a new board.

    if (isset($_POST['id'])) {

        # Retrieve the current posting attributes

        $id = $_POST['id'];
        $posting = Posting::query_by_id($id);

        # Verify that the board exists and that the user has permission 
        # to edit the board

        if (! ($posting && $posting->is_writable_by_user($user))) {
            header("Location: page-not-found.php");
            return;
        }

        # Modify the posting with the attributes from the POST data

        $posting->summary = $_POST['summary-field'];
        $posting->details = $_POST['details-field'];
        $posting->type = ($_POST['post-type'] == 'want' ? 'want' : 'have');
        $posting->published = isset($_POST['published-checkbox']) ? 'yes' : 'no';
        $posting->open_for_response = isset($_POST['closed-checkbox']) ? 'no' : 'yes';

    } else {

        $board_id = $_POST['board-id'];
        $posting = new Posting (
                $board_id,
                $user->id,
                $_POST['post-type'] == 'want' ? 'want' : 'have',
                $_POST['summary-field'],
                $_POST['details-field'],
                isset($_POST['published-checkbox']) ? 'yes' : 'no',
                isset($_POST['closed-checkbox']) ? 'no' :'yes'
                );                
    }

    # Apply the values from the post form to the datastore

    if ($posting->put()) {
        header("Location: post.php?id=$posting->id");
    } else {
        echo "Database error: " . DB::error();
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    
    case 'GET':
        doGet();
        break;

    case 'POST':
        doPost();
        return;

    default:
        echo "{$_SERVER['REQUEST_METHOD']} commands are not supported.";
        return;
}

// Now show the page

include 'template/base.php';
