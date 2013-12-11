<?php

require_once('model/db.php');

class UserInstaller
{
    /**
     * 
     * @param type $db_connection
     * @param type $admin_email
     * @param type $admin_password
     * @return boolean
     */
    public static function install_table($db_connection, $admin_email, $admin_password)
    {
        $query_create = "CREATE TABLE IF NOT EXISTS `user_table` (
                `user_id` int(31) NOT NULL AUTO_INCREMENT,
                `user_password` varchar(42) NOT NULL,
                `user_email` varchar(255) NOT NULL,
                `user_admin` tinyint(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`user_id`),
                UNIQUE KEY `user_email` (`user_email`)
            )";

        if (! $db_connection->query($query_create)) {
            echo "<p>$db_connection->error</p>";
            if ($db_connection->error != "Table 'user_table' already exists") {
                return FALSE;
            }
        }

        $_admin_email = $db_connection->real_escape_string($admin_email);
        $query_admin = "REPLACE INTO `user_table` 
            ( `user_password`, `user_email`, `user_admin` )
            VALUES
            ( PASSWORD('$admin_password'), '$_admin_email', 1)";
        return $db_connection->query($query_admin);
    }
}

class User
{
    public $id;
    public $email;
    public $is_admin;
    
    public function __construct($id, $email, $is_admin) {
        $this->id = $id;
        $this->email = $email;
        $this->is_admin = $is_admin;
    }
    
    public static function create($password, $email)
    {
        $Rs = DB::query("SELECT PASSWORD('$password') AS `password`");
        $R = $Rs->fetch_object();
        echo "<pre> password -> " . $R->password . "</pre>";
        
        $_email = DB::escape($email);
        $_password= DB::escape($password);
        $query_admin = "INSERT INTO `user_table` 
            ( `user_password`, `user_email` )
            VALUES
            ( PASSWORD('$_password'), '$_email')";
        return DB::query($query_admin) ? User::lookup_by_id(DB::insert_id()) : FALSE;
        return FALSE;
    }
    
    public static function lookup_by_id($id)
    {
        $_id = intval($id);
        $query = "SELECT * FROM `user_table`
            WHERE `user_id` = '$_id'";
        $results = DB::query($query);
        if ($results) {
            $result = $results->fetch_object();
        }
        return $result ? 
            new User($result->user_id, $result->user_email, $result->user_admin) : 
            FALSE;
    }

    public static function lookup_by_signon($email, $password)
    {
        $_email = DB::escape($email);
        $_password = DB::escape($password);
        $query = "SELECT * FROM `user_table`
            WHERE `user_email` = '$_email'
                AND `user_password` = PASSWORD('$_password')";
        $results = DB::query($query);
        if ($results) {
            $result = $results->fetch_object();
        }
        return $result ? 
            new User($result->user_id, $result->user_email, $result->user_admin) : 
            FALSE;
    }
    
    public function update()
    {
        $_email = DB::escape($this->email);
        $_password = DB::escape($this->password);
        $query = "UPDATE `user_table`
            SET `user_email` = '$_email',
                `user_password` = PASSWORD('$_password')
            WHERE `user_id` = $this->id";
        return DB::query($query);
    }
}