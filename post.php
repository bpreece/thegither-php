<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once 'model/session.php';
require_once 'model/posting.php';

global $RESPONSES_PER_QUERY;
$RESPONSES_PER_QUERY = 20;

function doGet()
{
    global $page_data, $block, $RESPONSES_PER_QUERY;

    $user = Session::current_user();

    if (! isset($_GET['id'])) {
        header("Location: page-not-found.php");
        return;
    }

    // Fetch the posting from the datastore.

    $post_id = $_GET['id'];
    $posting = Posting::query_by_id($post_id);

    // make sure the user is allowed to read this

    if (! (is_object($posting) && $posting->is_readable_by_user($user))) {
        header("Location: page-not-found.php");
        return;
    }

    // Fetch the posting's board from the datastore.

    $board_id = $posting->board_id;
    $board = Board::query_by_id($board_id);

    // make sure the user is allowed to read this

    if (! (is_object($posting) && $posting->is_readable_by_user($user))) {
        header("Location: page-not-found.php");
        return;
    }

    $page_data = array (
        'body-class' => 'board',
        'page-title' => $board->title,
        'title-link' => "board.php?id=$board_id",
        'sign-off-url' => "post.php?id=$post_id",
        'post-id' => $post_id,
        'post-timestamp' => $posting->created,
        'post-summary' => $posting->summary,
        'post-details' => $posting->details,
        'post-type' => $posting->type,
        'post-type-label' => $posting->type == 'want' ? $board->want_label : $board->have_label,
        'sidebar' => $board->description,
        'sidebar-class' => 'board-description',
    );

    $block = array( 
        'intro' => 'template/post-intro.php',
        'contents' => 'template/post-contents.php' ,
    );
    
    if (is_object($user)) {
        $page_data['user-id'] = $user->id;
        $page_data['user-email'] = $user->email;
        
        if ($posting->is_writable_by_user($user)) {
            $page_data['page-menu']['Edit this posting'] = 'edit-post.php?id=' . urlencode($post_id);
            
            // Fetch the next set of responses from the datastore.  The 'x' field in the
            // URL's query string indicates an offset into the datastore.
            $start = isset($_GET['x']) ? $_GET['x'] : 0;
            $response_list = Response::query_by_post($post_id, $RESPONSES_PER_QUERY, $start);
            if(is_array($response_list) && count($response_list) > 0) {
                $page_data['response-list'] = $response_list;
            } else {
                $page_data['response-message'] = "There are no responses to this posting yet.";
            }
            $block['secondary'] = 'template/post-response-list.php';
        } else if ($posting->is_respondable_by_user($user)) {
            $block['secondary'] = 'template/post-response-form.php';
        } else {
            $page_data['user-messages'][] = array (
                'class' => 'user-hint',
                'text' => "This posting is not open for responses.",
            );
        }
    } else {
        $sign_on_url = "post.php?id=" . urlencode($post_id);
        $page_data['user-messages'][] = array(
            'class' => 'user-hint',
            'text' => "You must <a href='signon.php?continue=$sign_on_url'>sign on</a> to respond to this posting."
        );
    }
}

function doPost()
{
    // called when a user posts a response to this posting
     if (isset($_POST['post-id'])) {
        $post_id = $_POST['post-id'];
        $user = Session::current_user();
        if (is_object($user)) {
            $posting = Posting::query_by_id($post_id);
            if ($posting->is_respondable_by_user($user)) {
                $text = $_POST['response-text'];
                $response = new Response($post_id, $user->id, $user->mail, $text);
                $response->put();
            }
        }
    }
    header("Location: post.php?id={$_POST['post-id']}");
    return;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        doPost();
        break;
    case 'GET':
        doGet();
        break;
    default:
        break;
}

// Now show the page

include 'template/base.php';
