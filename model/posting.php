<?php

require_once('model/db.php');
require_once 'model/board.php';

class PostingInstaller
{
    public static function install_table($db_connection)
    {
        $query_create = "CREATE TABLE IF NOT EXISTS `posting_table` (
            `id` int(31) NOT NULL AUTO_INCREMENT,
            `board_id` int(31) NOT NULL,
            `poster_id` int(31) NOT NULL,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `type` enum('have','want') NOT NULL,
            `summary` varchar(255) NOT NULL,
            `details` varchar(1023) NOT NULL,
            `published` enum('yes','no') NOT NULL DEFAULT 'no',
            `open_for_response` enum('yes','no') NOT NULL DEFAULT 'no',
            PRIMARY KEY (`id`),
            KEY `board_id` (`board_id`),
            KEY `poster_id` (`poster_id`)
            )";

        if (! $db_connection->query($query_create)) {
            echo "<p>$db_connection->error</p>";
            if ($db_connection->error != "Table 'posting_table' already exists") {
                return FALSE;
            }
        }

        return TRUE;
    }
}

class Posting
{
    public $id;
    public $board_id;
    public $poster_id;
    public $created;
    public $type;
    public $summary;
    public $details;
    public $published;
    public $open_for_response;

    public function __construct($board_id, $poster_id, $type, $summary, $details='', $published=FALSE, $open_for_response=FALSE) {
        $this->board_id = $board_id;
        $this->poster_id = $poster_id;
        $this->type = $type;
        $this->summary = $summary;
        $this->details = $details;
        $this->published = $published;
        $this->open_for_response = $open_for_response;
    }
    
    public function is_readable_by_user($user)
    {
        $board = Board::query_by_id($this->board_id);
        if (! is_object($board)) {
            echo "post $this->id, board $this->board_id";
        }
        return ($this->published && $board->published) ||
                (is_object($user) && ($user->is_admin || $user->id == $this->poster_id));
    }
    
    public function is_writable_by_user($user)
    {
        return is_object($user) &&
                ($user->is_admin || $user->id == $this->poster_id);
    }
    
    public function is_respondable_by_user($user)
    {
        return is_object($user) && 
                ($user->is_admin || $this->open_for_response);
    }
    
    public static function query_by_id($id)
    {
        $_id = intval($id);
        $query = "SELECT * FROM `posting_table`
            WHERE `id` = $_id";
        $db_results = DB::query($query);
        if (! ($db_results && $db_results->num_rows > 0)) {
            return FALSE;
        }
        $result = $db_results->fetch_object();
        $posting = new Posting(
                $result->board_id,
                $result->poster_id,
                $result->type,
                $result->summary,
                $result->details,
                $result->published == 'yes',
                $result->open_for_response == 'yes'
                );
        $posting->id = $result->id;
        $posting->created = $result->created;
        return $posting;
    }
    
    public static function query_published_by_board($board_id, $count, $start=0)
    {
        $_board_id = intval($board_id);
        $_start = intval($start);
        $_count = intval($count);
        $query = "SELECT * FROM `posting_table`
            WHERE `board_id` = $_board_id and
                    `published` = 'yes'
            ORDER BY `created`
            LIMIT $_start,$_count";
        $db_results = DB::query($query);
        if (! $db_results) {
            return FALSE;
        } else {
            $posting_list = array();
            while ($result = $db_results->fetch_object()) {
                $posting = new Posting (
                        $result->board_id,
                        $result->poster_id,
                        $result->type,
                        $result->summary,
                        $result->details,
                        $result->published == 'yes',
                        $result->open_for_response = 'yes'
                        );
                $posting->id = $result->id;
                $posting->created = $result->created;
                $posting_list[] = $posting;
            }
            return $posting_list;
        }
    }

   public static function query_all_by_board($board_id, $count, $start=0)
   {
        $_board_id = intval($board_id);
        $_start = intval($start);
        $_count = intval($count);
        $query = "SELECT * FROM `posting_table`
            WHERE `board_id` = $_board_id
            ORDER BY `created`
            LIMIT $_start,$_count";
        $db_results = DB::query($query);
        if (! $db_results) {
            return FALSE;
        } else {
            $posting_list = array();
            while ($result = $db_results->fetch_object()) {
                $posting = new Posting (
                        $result->board_id,
                        $result->poster_id,
                        $result->type,
                        $result->summary,
                        $result->details,
                        $result->published == 'yes',
                        $result->open_for_response == 'yes'
                        );
                $posting->id = $result->id;
                $posting->created = $result->created;
                $posting_list[] = $posting;
            }
            return $posting_list;
        }
   }

   public static function query_all_by_poster($poster_id, $count, $start=0)
   {
        $_poster_id = intval($poster_id);
        $_start = intval($start);
        $_count = intval($count);
        $query = "SELECT * FROM `posting_table`
            WHERE `poster_id` = $_poster_id
            ORDER BY `created`
            LIMIT $_start,$_count";
        $db_results = DB::query($query);
        if (! $db_results) {
            return FALSE;
        } else {
            $posting_list = array();
            while ($result = $db_results->fetch_object()) {
                $posting = new Posting (
                        $result->board_id,
                        $result->poster_id,
                        $result->type,
                        $result->summary,
                        $result->details,
                        $result->published == 'yes',
                        $result->open_for_response == 'yes'
                        );
                $posting->id = $result->id;
                $posting->created = $result->created;
                $posting_list[] = $posting;
            }
            return $posting_list;
        }
   }
    
    public function put()
    {
        return isset($this->id) ? $this->update() : $this->insert();
    }
    
    public function insert()
    {
        $_board_id = DB::escape($this->board_id);
        $_poster_id = DB::escape($this->poster_id);
        $_summary = DB::escape($this->summary);
        $_details = DB::escape($this->details);
        $_type = DB::escape($this->type);
        $_published = $this->published ? 'yes' : 'no';
        $_open_for_response = $this->open_for_response ? 'yes' : 'no';
        $query = "INSERT INTO `posting_table` (
                `board_id`,
                `poster_id`,
                `summary`,
                `details`,
                `type`,
                `published`,
                `open_for_response`
            ) VALUES (
                '$_board_id',
                '$_poster_id',
                '$_summary',
                '$_details',
                '$_type',
                '$_published',
                '$_open_for_response'
            )";
        
        $result = DB::query($query);
        if (! $result) {
            return FALSE;
        }
        $this->id = DB::insert_id();
        $this->created = Posting::query_by_id($this->id)->created;
        return $this;
    }
    
    public function update()
    {
        $_board_id = DB::escape($this->board_id);
        $_poster_id = DB::escape($this->poster_id);
        $_summary = DB::escape($this->summary);
        $_details = DB::escape($this->details);
        $_type = DB::escape($this->type);
        $_published = $this->published ? 'yes' : 'no';
        $_open_for_response = $this->open_for_response ? 'yes' : 'no';
        $query = "UPDATE `posting_table` SET
                `board_id` = '$_board_id',
                `poster_id` ='$_poster_id',
                `summary` = '$_summary',
                `details` = '$_details',
                `type` = '$_type',
                `published` = '$_published',
                `open_for_response` = '$_open_for_response'
            WHERE `id` = $this->id";
        
        return DB::query($query) ? $this : FALSE;
    }
}