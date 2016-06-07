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
 * Load all classes, constants, and functions.
 */
require_once __DIR__ . '/classes.php';
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/functions.php';

/**
 * Change the error reporting level to be similar to the MySQL extension.
 */
MySQL_Compat::initializeDriver();
