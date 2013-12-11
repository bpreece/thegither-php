<?php

require_once('model/db.php');

class SessionInstaller
{
    /**
     * See $db_connection->error for an error status message.
     * @param type $db_connection
     * @return type TRUE if the installation succeeded, or FALSE if it failed
     */
    public static function install_table($db_connection)
    {
        $query = "CREATE TABLE IF NOT EXISTS `session_table` (
                `session_id` varchar(75) NOT NULL,
                `user_id` int(31) NOT NULL,
                `expiration` timestamp NOT NULL,
                PRIMARY KEY (`session_id`)
            )";

        if (! $db_connection->query($query)) {
            echo "<p>$db_connection->error</p>";
            if ($db_connection->error != "Table 'session_table' already exists") {
                return FALSE;
            }
        }
        
        return TRUE;
    }
}


class Session 
{
    public $id;
    public $user_id;
    public $expiration;
    
    public function __construct($id, $user_id, $expiration) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->expiration = $expiration;
    }
    
    /**
     * 
     * @param type $user
     * @return type a session ID if the request succeeded, or FALSE if it failed.
     */
    public static function create($user_id)
    {
        $session_data = $user_id . '-' . date('c');
        $session_id = $user_id . '-' . hash('sha256', $session_data);

        $_user_id = DB::escape($user_id);
        $_session_id = DB::escape($session_id);
        $query = "INSERT INTO `session_table`
            ( `session_id`, `user_id`, `expiration` )
            VALUES
            ( '$_session_id',  '$_user_id',  DATE_ADD(NOW(), INTERVAL 15 MINUTE) )";
        echo "<pre>" . print_r($query, TRUE) . "</pre>";
        return DB::query($query) ? Session::lookup_by_id($session_id) : FALSE;
    }

    /**
     * 
     * @param type $db_connection
     * @param type $session_id
     * @return boolean
     */
    public static function lookup_by_id($session_id)
    {
        $_session_id = DB::escape($session_id);
        $query = "SELECT * FROM `session_table`
            WHERE `session_id` = '$_session_id'
                AND `expiration` > CURRENT_TIMESTAMP()";
        $results = DB::query($query);
        if ($results) {
            $result = $results->fetch_object();
        }
        return $result ?
            new Session($result->session_id, $result->user_id, $result->expiration) :
            FALSE;
    }
    
    public function get_user()
    {
        return User::lookup_by_id($this->user_id);
    }
    
    public static function current_user()
    {
        if (isset($_COOKIE['session-id'])) {
            $session = Session::lookup_by_id($_COOKIE['session-id']);
            if ($session) {
                return $session->get_user();
            }
        }
        return FALSE;
    }
    
    public static function close_by_id($session_id)
    {
        $_id = DB::escape($session_id);
        $query = "UPDATE `session_table` 
            SET `expiration` = CURRENT_TIMESTAMP()
            WHERE `session_id` = '$_id'";
        return DB::query($query);
    }

    /**
     * 
     * @param type $session_id
     * @return type none
     */
    public function close()
    {
        return Session::close_by_id($this->id);
    }
}
