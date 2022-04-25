<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-19-2022
 ****************************************************************
 *  Core.php
 *  Core files
 *
 *
 *************************************************************/

error_reporting (E_ALL);

// ** GET CONFIGURATION DATA **
require_once('./lib/Templates/Common.Template.php');
require_once('./lib/Database_Mysql_Connect_I.php');
require_once('./lib/class.users.php');
require_once ("./lib/functions.php");

global $db_prefix,$db_hostname, $db_name, $db_username, $db_password;

// DEFINE TABLE NAMES


session_start();



