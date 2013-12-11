<?php

class ResponseInstaller 
{
    public static function install_table($db_connection)
    {
        $query_create = "CREATE TABLE IF NOT EXISTS `response_table` (
            `id` int(31) NOT NULL AUTO_INCREMENT,
            `post_id` int(31) NOT NULL,
            `user_id` int(31) NOT NULL,
            `user_email` varchar(255) NOT NULL,
            `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `contents` varchar(1023) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `post_id` (`post_id`,`user_id`)
            )";

        $result = $db_connection->query($query_create);
        if (! $result) {
            echo "<p>$db_connection->error</p>";
            if ($db_connection->error != "Table 'response_table' already exists") {
                return FALSE;
            }
        }
        
        return TRUE;
    }
}

class Response
{
    public $id;
    public $post_id;
    public $user_id;
    public $created;
    public $user_email;
    public $contents;
    
    public function __construct($post_id, $user_id, $user_email, $contents) 
    {
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->user_email = $user_email;
        $this->contents = $contents;
    }
    
    public static function query_by_post($post_id, $count, $start=0)
    {
        $_post_id = intval($post_id);
        $_start = intval($start);
        $_count = intval($count);
        $query = "SELECT * FROM `response_table`
            WHERE `post_id` = $_post_id
            ORDER BY `created`
            LIMIT $_start,$_count";
        $db_results = DB::query($query);
        if (! $db_results) {
            return FALSE;
        } else {
            $response_list = array();
            while ($result = $db_results->fetch_object()) {
                $response = new Response (
                        $result->post_id,
                        $result->user_id,
                        $result->user_email,
                        $result->contents
                        );
                $response->id = $result->id;
                $response->created = $result->created;
                $response_list[] = $response;
            }
            return $response_list;
        }
    }
    
    public static function query_by_id($id)
    {
        $_id = intval($id);
        $query = "SELECT * FROM `response_table`
            WHERE `id` = $_id";
        $db_results = DB::query($query);
        if (! ($db_results && $db_results->num_rows > 0)) {
            return FALSE;
        }
        $result = $db_results->fetch_object();
        $response = new Response(
                        $result->post_id,
                        $result->user_id,
                        $result->user_email,
                        $result->contents
                        );
        $response->id = $result->id;
        $response->created = $result->created;
        return $response;
    }
    
    public function put()
    {
        return isset($this->id) ? $this->update() : $this->insert();
    }
    
    public function insert()
    {
        $_post_id = intval($this->post_id);
        $_user_id = intval($this->user_id);
        $_user_email = DB::escape($this->user_email);
        $_contents = DB::escape($this->contents);
        $query = "INSERT INTO `response_table` (
                `post_id`,
                `user_id`,
                `user_email`,
                `contents`
            ) VALUES (
                $_post_id,
                $_user_id,
                '$_user_email',
                '$_contents'
            )";
        echo "<pre>$query</pre>";
        
        $result = DB::query($query);
        if (! $result) {
            return FALSE;
        }
        $this->id = DB::insert_id();
        $this->created = Response::query_by_id($this->id)->created;
        return $this;
    }
    
    public function update()
    {
        $_post_id = intval($this->post_id);
        $_user_id = intval($this->user_id);
        $_user_email = DB::escape($this->user_email);
        $_contents = DB::escape($this->contents);
        $query = "UPDATE `response_table` SET
                `post_id` = $_post_id,
                `user_id` = '$_user_id',
                `user_email` = '$_user_email',
                `contents` = '$_contents'
            WHERE `id` = $this->id";
        echo "<pre>$query</pre>";
        
        return DB::query($query) ? $this : FALSE;
    }
}