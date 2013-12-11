<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';
require_once 'model/posting.php';

global $POSTINGS_PER_QUERY;
$POSTINGS_PER_QUERY = 20;

global $page_data, $block;

function doGet()
{
    global $page_data, $block, $POSTINGS_PER_QUERY;

    $user = Session::current_user();

    // The user must be signed on to view this page.
    if (!is_object($user)) {
        header("Location: signon.php?continue=user-boards.php");
        return;
    }

    // Fetch the next 20 postings from the datastore.  The 'x' field in the
    // URL's query string indicates an offset into the datastore.

    $start = isset($_GET['x']) ? $_GET['x'] : 0;
    $postings = Posting::query_all_by_poster($user->id, $POSTINGS_PER_QUERY, $start);

    // Set the basic values for the HTML template
    $page_data = array(
        'body-class' => 'user-posts',
        'page-title' => 'My Postings',
        'user-id' => $user->id,
        'user-email' => $user->email,
        'sign-off-url' => '/',
        'main-menu' => array(
            'My Boards' => 'user-boards.php?id=' . urlencode($user->id),
        ),
        'page-menu' => array(
            'New Posting' => 'edit-post.php',
        ),
    );
    if (sizeof($postings) == $POSTINGS_PER_QUERY) {
        $page_data['x'] = $start + sizeof($postings);
    }
    if (count($postings) > 0) {
        $page_data['posts'] = $postings;
        $page_data['user-messages'][] = array (
            'class' => 'user-hint',
            'text' => "Select a posting to view it.",
        );
    } else {
        $page_data['postings-message'] = "You have no postings to view.";
    }
    
    $block = array( 
        'contents' => 'template/user-posts-contents.php' 
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
