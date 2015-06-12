<?php

/*
 * -----------------------------------------------------------------------------
 *        MySQL Compat  :  A MySQL Extension Emulation Layer using MySQLi
 * -----------------------------------------------------------------------------
 *
 * Copyright (c) 2015, Kijin Sung <kijin@kijinsung.com>
 * 
 * All rights reserved.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation
 * the right to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Do not load if MySQL is enabled or MySQLi is missing.
 */
if (function_exists('mysql_connect')) return false;
if (!class_exists('mysqli')) return false;

/**
 * Define some constants.
 */
if (!defined('MYSQL_ASSOC')) define('MYSQL_ASSOC', MYSQLI_ASSOC);
if (!defined('MYSQL_NUM')) define('MYSQL_NUM', MYSQLI_NUM);
if (!defined('MYSQL_BOTH')) define('MYSQL_BOTH', MYSQLI_BOTH);

/**
 * Change the error reporting level to be similar to the MySQL extension.
 */
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR;

/**
 * This class maintains a record of the last instance of MySQL connection.
 * It is used in all functions where the connection argument is optional.
 */
class MySQL_Compat extends mysqli
{
    protected static $_last_instance = null;
    
    public static function getLastInstance()
    {
        return self::$_last_instance ?: mysql_connect();
    }
    
    public static function setLastInstance(mysqli $conn)
    {
        self::$_last_instance = $conn;
    }
}

/**
 * Get the number of rows affected by the previous MySQL operation.
 */
function mysql_affected_rows($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->affected_rows;
}

/**
 * Get the name of the currently selected character set.
 */
function mysql_client_encoding($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->character_set_name();
}

/**
 * Close a MySQL connection.
 */
function mysql_close($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    MySQL_Compat::$last_instance = $conn = null;
    unset($conn);
}

/**
 * Open a connection to a MySQL Server.
 */
function mysql_connect($server = null, $username = null, $password = null, $new_link = false, $client_flags = 0)
{
    $conn = new MySQL_Compat($server, $username, $password);
    MySQL_Compat::setLastInstance($conn);
    return $conn;
}

/**
 * Create a MySQL database.
 */
function mysql_create_db($database_name, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return (bool)($conn->query("CREATE DATABASE `$database_name`"));
}

/**
 * Move the internal result pointer to a specific row.
 */
function mysql_data_seek(mysqli_result $result, $row_number)
{
    return $result->data_seek($row_number);
}

/**
 * Retrieve a database name from the result of mysql_list_dbs().
 * Relying on this function is strongly discouraged.
 */
function mysql_db_name(mysqli_result $result, $row, $field = null)
{
    if ($field === null) $field = 'Database';
    $result->data_seek($row);
    $table = $result->fetch_assoc();
    if (isset($table[$field]))
    {
        return $table[$field];
    }
    else
    {
        return false;
    }
}

/**
 * Select a database and executes a query on it.
 * Like the original version, it DOES NOT return you to the original database.
 */
function mysql_db_query($database_name, $query, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    $conn->select_db($database_name);
    return $conn->query($query);
}

/**
 * Drop (delete) a MySQL database.
 */
function mysql_drop_db($database_name, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return (bool)($conn->query("DROP DATABASE `$database_name`"));
}

/**
 * Returns the numerical value of the error message from previous MySQL operation.
 */
function mysql_errno($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->errno;
}

/**
 * Returns the text of the error message from previous MySQL operation.
 */
function mysql_error($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->error;
}

/**
 * Escapes a string for use in a MySQL query.
 * This version is an alias to mysql_real_escape_string().
 */
function mysql_escape_string($string, $conn = null)
{
    return mysql_real_escape_string($string, $conn);
}

/**
 * Fetch a result row as an associative array, a numeric array, or both.
 */
function mysql_fetch_array(mysqli_result $result, $result_type = MYSQL_BOTH)
{
    return $result->fetch_array($result_type);
}

/**
 * Fetch a result row as an associative array.
 */
function mysql_fetch_assoc(mysqli_result $result)
{
    return $result->fetch_assoc();
}

/**
 * Get column information from a result and return as an object.
 * If the field offset is not given, the current field is used.
 */
function mysql_fetch_field(mysqli_result $result, $field_offset = null)
{
    if ($field_offset !== null) $result->field_seek($field_offset);
    return $result->fetch_field();
}

/**
 * Get the length of each output in a result.
 */
function mysql_fetch_lengths(mysqli_result $result)
{
    return $result->lengths;
}

/**
 * Fetch a result row as an object.
 */
function mysql_fetch_object(mysqli_result $result, $class_name = 'stdClass', $params = array())
{
    return $result->fetch_object($class_name, $params);
}

/**
 * Fetch a result row as a numeric array.
 */
function mysql_fetch_row(mysqli_result $result)
{
    return $result->fetch_row();
}

/**
 * Get the flags associated with the specified field in a result.
 */
function mysql_field_flags(mysqli_result $result, $field_offset = 0)
{
    $field_info = $result->fetch_field_direct($field_offset);
    $flags = array();
    $checks = array(
        'MYSQLI_NOT_NULL_FLAG',
        'MYSQLI_PRI_KEY_FLAG',
        'MYSQLI_UNIQUE_KEY_FLAG',
        'MYSQLI_MULTIPLE_KEY_FLAG',
        'MYSQLI_BLOB_FLAG',
        'MYSQLI_UNSIGNED_FLAG',
        'MYSQLI_ZEROFILL_FLAG',
        'MYSQLI_AUTO_INCREMENT_FLAG',
        'MYSQLI_TIMESTAMP_FLAG',
        'MYSQLI_SET_FLAG',
        'MYSQLI_NUM_FLAG',
        'MYSQLI_PART_KEY_FLAG',
        'MYSQLI_GROUP_FLAG',
    );
    foreach ($checks as $check)
    {
        if ($field_info->flags & constant($check))
        {
            $flag = str_replace('pri_key', 'primary_key', strtolower(substr($check, 7, strlen($check) - 12)));
            $flags[] = $flag;
        }
    }
    return implode(' ', $flags);
}

/**
 * Returns the length of the specified field.
 */
function mysql_field_len(mysqli_result $result, $field_offset = 0)
{
    $field_info = $result->fetch_field_direct($field_offset);
    return $field_info->length;
}

/**
 * Get the name of the specified field in a result.
 */
function mysql_field_name(mysqli_result $result, $field_offset = 0)
{
    $field_info = $result->fetch_field_direct($field_offset);
    return $field_info->name;
}

/**
 * Set result pointer to a specified field offset.
 */
function mysql_field_seek(mysqli_result $result, $field_offset = 0)
{
    return $result->field_seek($field_offset);
}

/**
 * Get the name of the table the specified field is in.
 */
function mysql_field_table(mysqli_result $result, $field_offset = 0)
{
    $field_info = $result->fetch_field_direct($field_offset);
    return $field_info->table;
}

/**
 * Get the type of the specified field in a result.
 */
function mysql_field_type(mysqli_result $result, $field_offset = 0)
{
    $field_info = $result->fetch_field_direct($field_offset);
    return $field_info->type;
}

/**
 * Free result memory.
 */
function mysql_free_result(mysqli_result $result)
{
    return $result->free();
}

/**
 * Get the MySQL client library version.
 */
function mysql_get_client_info($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->client_info;
}

/**
 * Get the MySQL host info (type of connection and server hostname).
 */
function mysql_get_host_info($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->host_info;
}

/**
 * Get the MySQL protocol version.
 */
function mysql_get_proto_info($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->protocol_version;
}

/**
 * Get the MySQL server version.
 */
function mysql_get_server_info($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->server_info;
}

/**
 * Get information about the most recent query.
 */
function mysql_info($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->info;
}

/**
 * Get the ID generated in the last query.
 */
function mysql_insert_id($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->insert_id;
}

/**
 * List databases available on a MySQL server.
 */
function mysql_list_dbs($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->query("SHOW DATABASES");
}

/**
 * List MySQL table fields.
 */
function mysql_list_fields($database_name, $table_name, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->query("SHOW COLUMNS FROM `$table_name` FROM `$database_name`");
}

/**
 * List MySQL processes.
 */
function mysql_list_processes($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->query("SHOW PROCESSLIST");
}

/**
 * List tables in a MySQL database.
 */
function mysql_list_tables($database_name, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->query("SHOW TABLES FROM `$database_name`");
}

/**
 * Get number of fields in result.
 */
function mysql_num_fields(mysqli_result $result)
{
    return $result->field_count;
}

/**
 * Get number of rows in result.
 */
function mysql_num_rows(mysqli_result $result)
{
    return $result->num_rows;
}

/**
 * Open a persistent connection to a MySQL server.
 */
function mysql_pconnect($server = null, $username = null, $password = null, $client_flags = 0)
{
    return mysql_connect("p:$server", $username, $password, false, $client_flags);
}

/**
 * Ping a server connection or reconnect if there is no connection.
 */
function mysql_ping($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->ping();
}

/**
 * Send a MySQL query.
 */
function mysql_query($query, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->query($query, MYSQLI_STORE_RESULT);
}

/**
 * Escapes special characters in a string for use in an SQL statement.
 */
function mysql_real_escape_string($string, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->real_escape_string($string);
}

/**
 * Get result data.
 */
function mysql_result(mysqli_result $result, $row = 0, $field = 0)
{
    $result->data_seek($row);
    $result->field_seek($field);
    return $result->current_field;
}

/**
 * Select a MySQL database.
 */
function mysql_select_db($database_name, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->select_db($database_name);
}

/**
 * Set the client character set.
 */
function mysql_set_charset($charset, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->set_charset($charset);
}

/**
 *  Get current system status.
 */
function mysql_stat($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->stat();
}

/**
 * Retrieve a table name from the result of mysql_list_tables().
 * Relying on this function is strongly discouraged.
 */
function mysql_tablename(mysqli_result $result, $index = 0)
{
    $result->data_seek($index);
    $table = $result->fetch_row();
    if ($table)
    {
        return current($table);
    }
    else
    {
        return false;
    }
}

/**
 * Return the current thread ID.
 */
function mysql_thread_id($conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->thread_id;
}

/**
 * Send an SQL query to MySQL without fetching and buffering the result rows.
 * The result must be freed before any other query can be made.
 */
function mysql_unbuffered_query($query, $conn = null)
{
    $conn = $conn ?: MySQL_Compat::getLastInstance();
    return $conn->query($query, MYSQLI_USE_RESULT);
}
