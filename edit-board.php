<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';
require_once 'model/board.php';

# set the basic values for the HTML template

function doGet()
{
    global $page_data, $block;

    # The user must be signed on to create or edit this board

    $user = Session::current_user();
    if (!is_object($user)) {
        if (isset($_GET['id'])) {
            header("Location: signon.php?continue=edit-board.php?id=" . urlencode($_GET['id']));
        } else {
            header("Location: signon.php?continue=edit-board.php");
        }
        return;
    }

    # Validate the query parameters.  If the query string in the URL includes
    # an ID, then we're editing an existing board;  otherwise we're creating
    # a new board.

    if (isset($_GET['id'])) {

        # Fetch the board from the datastore

        $board_id = $_GET['id'];
        $board = Board::query_by_id($board_id);
        if (! is_object($board)) {
            header("Location: page-not-found.php");
            return;
        }

        # Verify that the user has permission to edit the board

        if (!$board->is_writable_by_user($user)) {
            header("Location: board.php?id=" . urlencode($board_id));
            return;
        }

        # Set the boards properties, and provide the values needed to
        # pre-populate the form fields

        $page_data = array(
            'user-id' => $user->id,
            'user-email' => $user->email,
            'sign-off-url' => 'signoff.php?continue=thegither.php',
            'body-class' => 'edit-board',
            'page-title' => $board->title,
            'board-id' => $board_id,
            'board-title' => $board->title,
            'have-label' => $board->have_label,
            'want-label' => $board->want_label,
            'published' => $board->published ? 'checked' : '',
            'closed' => $board->open_to_posts ? '' : 'checked',
            'description' => $board->description,
        );

    } else {

        # Set the boards properties, and provide the values needed to
        # pre-populate the form fields

        $page_data = array(
            'user-id' => $user->id,
            'user-email' => $user->email,
            'sign-off-url' => 'signoff.php?continue=thegither.php',
            'body-class' => 'edit-board',
            'page-title' => 'New Board',
            'board-title' => '',
            'have-label' => '',
            'want-label' => '',
            'published' => '',
            'closed' => 'checked',
            'description' => '',
        );
    }

    $block = array( 'contents' => "template/edit-board-contents.php" );
}

function doPost()
{
    # The user must be signed on to create or edit this board

    $user = Session::current_user();
    if (!is_object($user)) {
        header("Location: page-not-found.php");
        return;
    }
    
    # If 'id' is set, then we're editing an existing board; otherwise
    # we're creating a new board.

    if (isset($_POST['board-id'])) {

        # Fetch the board and verify that the board exists and that the user
        # has permission to edit the board.
        $board_id = $_POST['board-id'];
        $board = Board::query_by_id($board_id);
        if (! (is_object($board) && $board->is_writable_by_user($user))) {
            header("Location: page-not-found.php");
            return;
        }

    } else {

        $board = new Board($user->id);

    }

    # Apply the values from the post form to the datastore

    $board->title = $_POST['title-field'];
    $board->have_label = $_POST['have-label-field'];
    $board->want_label = $_POST['want-label-field'];
    $board->published = isset($_POST['publish-checkbox']) ? 'yes' : 'no';
    $board->open_to_posts = isset ($_POST['closed-checkbox']) ? 'no' : 'yes';
    $board->description = $_POST['description-field'];
    if ($board->put()) {
        header("Location: board.php?id=$board->id");
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
