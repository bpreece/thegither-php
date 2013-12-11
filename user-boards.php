<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';
require_once 'model/board.php';

global $BOARDS_PER_QUERY;
$BOARDS_PER_QUERY = 20;

function doGet()
{
    global $page_data, $block, $BOARDS_PER_QUERY;

    $user = Session::current_user();

    // The user must be signed on to view this page.
    if (!is_object($user)) {
        header("Location: signon.php?continue=user-boards.php");
        return;
    }

    // Fetch the next 20 boards from the datastore.  The 'x' field in the
    // URL's query string indicates an offset into the datastore.

    $start = isset($_GET['x']) ? $_GET['x'] : 0;
    $boards = Board::query_all_by_owner($user, $BOARDS_PER_QUERY, $start);

    // Set the basic values for the HTML template
    $page_data = array(
        'body-class' => 'user-boards',
        'page-title' => 'My Boards',
        'user-id' => $user->id,
        'user-email' => $user->email,
        'sign-off-url' => 'signoff.php?continue=user-boards.php',
        'main-menu' => array(
            'My Postings' => 'user-posts.php?id=' . urlencode($user->id),
        ),
        'page-menu' => array(
            'New Board' => 'edit-board.php',
        ),
    );
    
    if (sizeof($boards) == $BOARDS_PER_QUERY) {
        $page_data['x'] = $start + sizeof($boards);
    }
    if (count($boards) > 0) {
        $page_data['boards'] = $boards;
        $page_data['user-messages'][] = array (
            'class' => 'user-hint',
            'text' => "Select a board to view it.",
        );
    } else {
        $page_data['boards-message'] = "You have no boards to view.";
    }

    $block = array( 
        'contents' => 'template/user-boards-contents.php' 
    );
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        doGet();
        break;

    default:
        header("Location: page-not-found.php");
        break;
}

// Now show the page

include 'template/base.php';
