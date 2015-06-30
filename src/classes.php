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
 * This class maintains a record of the last instance of MySQL connection.
 * It is used in all functions where the connection argument is optional.
 */
class MySQL_Compat extends mysqli
{
    protected static $_mysqli_driver = null;
    protected static $_last_instance = null;
    
    public static function initializeDriver()
    {
        self::$_mysqli_driver = new mysqli_driver();
        self::$_mysqli_driver->report_mode = MYSQLI_REPORT_ERROR;
    }
    
    public static function getLastInstance()
    {
        return self::$_last_instance ?: mysql_connect();
    }
    
    public static function setLastInstance(mysqli $conn)
    {
        self::$_last_instance = $conn;
    }
}
