<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';
require_once 'model/board.php';

global $POSTINGS_PER_QUERY;
$POSTINGS_PER_QUERY = 20;

function doGet()
{
    global $page_data, $block, $POSTINGS_PER_QUERY;

    $user = Session::current_user();

    // make sure the board ID is set
    
    if (! isset($_GET['id'])) {
        header("Location: page-not-found.php");
        return;
    }

    // Fetch the board from the datastore.

    $board_id = $_GET['id'];
    $board = Board::query_by_id($board_id);

    // make sure the user is allowed to read this

    if (! is_object($board)) {
        header("Location: page-not-found.php");
        return;
    }

    if (! $board->is_readable_by_user($user)) {
        header("Location: page-not-found.php");
        return;
    }
    // fetch the next set of postings for the board
    
    $start = isset($_GET['x']) ? $_GET['x'] : 0;
    $posting_list = Posting::query_all_by_board($board_id, $POSTINGS_PER_QUERY, $start);

    // set the basic values for the HTML template
    
    $page_data = array (
        'body-class' => 'board',
        'page-title' => $board->title,
        'title-link' => "board.php?id=$board->id",
        'sign-off-url' => "board.php?id=$board->id",
        'board-id' => $board_id,
        'open-to-posts' => $board->open_to_posts,
        'labels' => array(
            'have' => $board->have_label,
            'want' => $board->want_label,
        ),
        'sidebar' => $board->description,
        'sidebar-class' => 'board-description',
    );
    
    if (count($posting_list) > 0) {
        $page_data['posts'] = $posting_list;
        $page_data['user-messages'][] = array (
            'class' => 'user-hint',
            'text' => "Select a posting to view it.",
        );
    } else {
        $page_data['postings-message'] = "There are no postings on this board yet.";
    }

    if (is_object($user)) {
        $page_data += array(
            'user-id' => $user->id,
            'user-email' => $user->email,
        );
        if ($board->is_postable_by_user($user)) {
            $page_data['page-menu']['Add a posting'] = 'edit-post.php?board-id=' . urlencode($board_id);
        } else {
            $page_data['user-messages'][] = array (
                'class' => 'user-hint',
                'text' => "This board is not open for new posts.",
            );
        }
        if ($board->is_writable_by_user($user)) {
            $page_data['page-menu']['Edit this board'] = 'edit-board.php?id=' . urlencode($board_id);
        }
    } else {
        $sign_on_url = "board.php?id=" . urlencode($board_id);
        $page_data['user-messages'][] = array (
                'class' => 'user-hint',
                'text' => "You must <a href='signon.php?continue=$sign_on_url'>sign on</a> to add a posting to this board.",
            );
    }

    $block = array( 
        'contents' => 'template/board-contents.php' ,
    );
}

/**
 * A page to display a list of all posts which have been posted to a board.
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
