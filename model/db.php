<?php

require_once 'model/user.php';
require_once 'model/session.php';
require_once 'model/board.php';
require_once 'model/posting.php';
require_once 'model/response.php';

/**
 * 
 */

global $db_server, 
        $db_name, 
        $db_signon, 
        $db_connection;

$db_server = "localhost";
$db_name = "thegither_db";
$db_signon = "thegither";
$db_password = "thegither";

class DbInstaller 
{
    /**
     * 
     * @global string $db_server
     * @global string $db_name
     * @global mysqli $db_connection
     * @global string $db_signon
     * @global string $db_password
     * @param type $sql_signon
     * @param type $sql_password
     * @param type $admin_email
     * @param type $admin_password
     * @return string|\mysqli
     */
    public static function create_all($sql_signon, $sql_password, $admin_email, $admin_password) 
    {
        global $db_server;
        $db_connection = new mysqli($db_server, $sql_signon, $sql_password);

        $return = array(
            'connection' => FALSE,
            'status' => "Created database $db_name",
        );


        // Check connection
        if ($db_connection->connect_errno && $db_connection->connect_errno != 'database exists') {
            $return['status'] = "Failed to connect to MySQL: " . $db_connection->error;
            return $return;
        }

        // Create database
        global $db_name;
        $query_create = "CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci";
        if (! $db_connection->query($query_create)) {
            $return['status'] = "Failed creating the database '$db_name': " . $db_connection->error;
            return $return;
        }

        // create administrative login
        global $db_signon, $db_password;
        $_db_password = $db_connection->real_escape_string($db_password);
        $query_user = "CREATE USER '$db_signon'@'localhost' IDENTIFIED BY  '$_db_password'";
        $query_priv = "GRANT ALL PRIVILEGES ON `$db_name`.* TO  '$db_signon'@'localhost'";
        if (! $db_connection->query($query_user) || ! $db_connection->query($query_priv)) {
            $return['status'] = "Failed creating the database administrator '$db_signon': " . $db_connection->error;
            return $return;
        }

        $db_connection->select_db($db_name);

        // create database table
        if (!DbInstaller::install_table($db_connection)) {
            $return['status'] = "Failed creating the database table: " . $db_connection->error;
            return $return;
        }

        // create user table
        if (! UserInstaller::install_table($db_connection, $admin_email, $admin_password)) {
            $return['status'] = "Failed creating the user table: " . $db_connection->error;
            return $return;
        }

        // create user table
        if (! SessionInstaller::install_table($db_connection)) {
            $return['status'] = "Failed creating the session table: " . $db_connection->error;
            return $return;
        }

        // create user table
        if (! BoardInstaller::install_table($db_connection)) {
            $return['status'] = "Failed creating the board table: " . $db_connection->error;
            return $return;
        }

        // create user table
        if (! PostingInstaller::install_table($db_connection)) {
            $return['status'] = "Failed creating the posting table: " . $db_connection->error;
            return $return;
        }

        // create user table
        if (! ResponseInstaller::install_table($db_connection)) {
            $return['status'] = "Failed creating the response table: " . $db_connection->error;
            return $return;
        }

        $return['connection'] = $db_connection;
        return $return;
    }

    /**
     * 
     * @param type $db_connection
     * @return boolean
     */
    private static function install_table($db_connection) 
    {
        // create table
        $query_create = "CREATE TABLE IF NOT EXISTS `db_table` (
                `key` varchar(255) NOT NULL DEFAULT '',
                `value` text,
                PRIMARY KEY (`key`)
            )";
        if (! $db_connection->query($query_create)) {
            echo "<p>$db_connection->error</p>";
            if ($db_connection->error != "Table 'db_table' already exists") {
                return FALSE;
            }
        }

        // initialize table values
        $query_init = "REPLACE INTO `db_table` 
            ( `key`, `value` )
            VALUES 
            ( 'db.version', '0.0.1' ), 
            ( 'db.created', UTC_TIMESTAMP() )";
        return $db_connection->query($query_init);
    }
}


$db_connection = FALSE;

class DB
{
    /**
     * Call DB::error() or DB::errno() to check for errors on the return
     * 
     * @global string $db_server
     * @global string $db_signon
     * @global string $db_password
     * @global mysqli $db_connection
     */
    public static function get_connection()
    {
        global $db_connection;
        global $db_server, $db_signon, $db_password, $db_name;
        if (! $db_connection) {
            $db_connection = mysqli_connect($db_server, $db_signon, $db_password, $db_name);
        }
        return $db_connection;
    }

    /**
     * wrapper for mysqli::query()
     * @param type $query
     * @return type
     */
    public static function query($query)
    {
        $db = DB::get_connection();
        return $db ? $db->query($query) : FALSE;
    }

    /**
     * wrapper for mysqli::real_escape_string()
     * @param type $string
     * @return type
     */
    public static function escape($string)
    {
        $db = DB::get_connection();
        return $db ? $db->real_escape_string($string) : FALSE;
    }

    /**
     * wrapper for mysqli::error
     * @return type
     */
    public static function error()
    {
        $db = DB::get_connection();
        return $db ? $db->error : "No database connection available";
    }

    /**
     * wrapper for mysqli::errno
     * @return type
     */
    public static function errno()
    {
        $db = DB::get_connection();
        return $db ? $db->errno : -1;
    }

    /**
     * wrapper for mysqli::insert_id
     * @return type
     */
    public static function insert_id()
    {
        $db = DB::get_connection();
        return $db ? $db->insert_id : FALSE;
    }

}