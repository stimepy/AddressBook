<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-24-2022
 ****************************************************************
 *
 *  install.php
 *  Installs address book.
 *
 *************************************************************/

error_reporting  (E_ERROR | E_WARNING | E_PARSE);
require_once(".\Install\Install.Core.php");
require_once (".\Install\Install.Template.php");

$installtemplate = new Install();
global $lang;

$output = $installtemplate ->CommonBodyStart( $lang['title'], $lang['charset'] );
$post = 1;


switch($installtemplate->getPost('installStep')){
    case 2:
       $installtemplate->checkDB();
       $installtemplate->installData();
       $output .=$installtemplate->Step2($lang);
       break;
    default:
        $body['FILE_INSTALL'] = FILE_INSTALL;
        $output .= $installtemplate->Step1($body, $lang);
        break;
}

$output .= $installtemplate->CommonBodyEnd($lang);
display($output);

