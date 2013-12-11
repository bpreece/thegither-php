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

    // only the site admin is allowed to access this page
    
    if (! ($user && $user->is_admin)) {
        header("Location: not-found.php");
        return;
    }

    // Fetch the next set of boards from the datastore.  The 'x' field in the
    // URL's query string indicates an offset into the datastore.

    $start = isset($_GET['x']) ? $_GET['x'] : 0;
    $boards = Board::query_all($BOARDS_PER_QUERY, $start);

    // set the basic values for the HTML template
    
    $page_data = array (
        'body-class' => 'boards',
        'page-title' => 'All Boards',
        'user-id' => $user->id,
        'user-email' => $user->email,
        'sign-off-url' => '/',
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
        $page_data['boards-message'] = "There are no boards to view";
    }

    $block = array(
        'contents' => 'template/boards-contents.php'
    );
}


/**
 * A page to display a list of all boards which have been created.
 */

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
