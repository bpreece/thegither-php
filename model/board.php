<?php

require_once('model/db.php');

class BoardInstaller
{
    public static function install_table($db_connection)
    {
        $query_create = "CREATE TABLE IF NOT EXISTS `board_table` (
            `id` int(31) NOT NULL AUTO_INCREMENT,
            `owner_id` int(31) NOT NULL,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `title` varchar(255) NOT NULL,
            `published` enum('yes','no') NOT NULL DEFAULT 'no',
            `have_label` varchar(31) NOT NULL,
            `want_label` varchar(31) NOT NULL,
            `open_to_posts` enum('yes','no') NOT NULL,
            `description` varchar(4095) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `owner_id` (`owner_id`),
            KEY `title` (`title`)
            )";

        $result = $db_connection->query($query_create);
        if (! $result) {
            echo "<p>$db_connection->error</p>";
            if ($db_connection->error != "Table 'board_table' already exists") {
                return FALSE;
            }
        }
        
        return TRUE;
    }
}

class Board
{
    public $id;
    public $owner_id;
    public $created;
    public $title;
    public $published;
    public $have_label;
    public $want_label;
    public $open_to_posts;
    public $description;
    
    public function __construct($owner_id, $title='<< UNTITLED >>', $published=FALSE, 
            $have_label='HAVE', $want_label='WANT', $open_to_posts=FALSE, $description='') 
    {
        $this->owner_id = $owner_id;
        $this->title = $title;
        $this->published = $published;
        $this->have_label = $have_label;
        $this->want_label = $want_label;
        $this->open_to_posts = $open_to_posts;
        $this->description = $description;
    }

    public function is_readable_by_user($user)
    {
        return $this->published ||
                (is_object($user) && ($user->is_admin || $this->owner_id == $user->id));
    }

    public function is_writable_by_user($user)
    {
        return is_object($user) && 
                ($user->is_admin || $user->id == $this->owner_id);
    }

    public function is_postable_by_user($user)
    {
        return is_object($user) &&
                ($user->is_admin || $this->open_to_posts);
    }
    
    public static function query_all($count, $start=0)
    {
        $_start = intval($start);
        $_count = intval($count);
        $query = "SELECT * FROM `board_table`
            ORDER BY `created`
            LIMIT $_start,$_count";
        $db_results = DB::query($query);
        if (! $db_results) {
            return FALSE;
        } else {
            $board_list = array();
            while ($result = $db_results->fetch_object()) {
                $board = new Board (
                        $result->owner_id,
                        $result->title,
                        $result->published == 'yes',
                        $result->have_label,
                        $result->want_label,
                        $result->open_to_posts == 'yes',
                        $result->description
                        );
                $board->id = $result->id;
                $board->created = $result->created;
                $board_list[] = $board;
            }
            return $board_list;
        }
    }

    public static function query_all_by_owner($owner, $count, $start=0)
    {
        $_start = intval($start);
        $_count = intval($count);
        $_owner_id = intval($owner->id);
        $query = "SELECT * FROM `board_table`
            WHERE `owner_id` = $_owner_id
            ORDER BY `created`
            LIMIT $_start,$_count";
        $db_results = DB::query($query);
        if (! $db_results) {
            return FALSE;
        } else {
            $board_list = array();
            while ($result = $db_results->fetch_object()) {
                $board = new Board (
                        $result->owner_id,
                        $result->title,
                        $result->published == 'yes',
                        $result->have_label,
                        $result->want_label,
                        $result->open_to_posts == 'yes',
                        $result->description
                        );
                $board->id = $result->id;
                $board->created = $result->created;
                $board_list[] = $board;
            }
            return $board_list;
        }
    }
    
    public static function query_by_id($id)
    {
        $_id = intval($id);
        $query = "SELECT * FROM `board_table`
            WHERE `id` = $_id";
        $db_results = DB::query($query);
        if (! ($db_results && $db_results->num_rows > 0)) {
            return FALSE;
        }
        $result = $db_results->fetch_object();
        $board = new Board(
                $result->owner_id,
                $result->title,
                $result->published == 'yes',
                $result->have_label,
                $result->want_label,
                $result->open_to_posts == 'yes',
                $result->description
                );
        $board->id = $result->id;
        $board->created = $result->created;
        return $board;
    }
    
    public function put()
    {
        return isset($this->id) ? $this->update() : $this->insert();
    }
    
    public function insert()
    {
        $_owner_id = intval($this->owner_id);
        $_title = DB::escape($this->title);
        $_have_label = DB::escape($this->have_label);
        $_want_label = DB::escape($this->want_label);
        $_description = DB::escape($this->description);
        $_published = $this->published ? 'yes' : 'no';
        $_open_to_posts = $this->open_to_posts ? 'yes' : 'no';
        $query = "INSERT INTO `board_table` (
                `owner_id`,
                `title`,
                `published`,
                `have_label`,
                `want_label`,
                `open_to_posts`,
                `description`
            ) VALUES (
                $_owner_id,
                '$_title',
                '$_published',
                '$_have_label',
                '$_want_label',
                '$_open_to_posts',
                '$_description'
            )";
        
        $result = DB::query($query);
        if (! $result) {
            return FALSE;
        }
        $this->id = DB::insert_id();
        $this->created = Board::query_by_id($this->id)->created;
        return $this;
    }
    
    public function update()
    {
        $_owner_id = intval($this->owner_id);
        $_title = DB::escape($this->title);
        $_have_label = DB::escape($this->have_label);
        $_want_label = DB::escape($this->want_label);
        $_description = DB::escape($this->description);
        $_published = $this->published ? 'yes' : 'no';
        $_open_to_posts = $this->open_to_posts ? 'yes' : 'no';
        $query = "UPDATE `board_table` SET
                `owner_id` = $_owner_id,
                `title` = '$_title',
                `published` = '$_published',
                `have_label` = '$_have_label',
                `want_label` = '$_want_label',
                `open_to_posts` = '$_open_to_posts',
                `description` = '$_description'
            WHERE `id` = $this->id";
        
        return DB::query($query) ? $this : FALSE;
    }
}