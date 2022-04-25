<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-24-2022
 ****************************************************************
 *  upgrade.php
 *  Upgrades installation.  Generalized.  Install template will need to be
 *  updated with appropriate updated information.
 *	
 *************************************************************/

error_reporting  (E_ERROR | E_WARNING | E_PARSE);

// ** GET CONFIGURATION DATA **
require_once('.\Install\Install.Core.php');
require_once (".\Install\Install.Template.php");

$updateTemplate = new Install();
global $lang;
$globalUsers = new users();

$output = $updateTemplate ->CommonBodyStart($lang['upgradeTitle'], $lang['charset']);

switch($updateTemplate->getPost("installStep")){
    case 3:
        $updateTemplate->checkDB();
        $updateTemplate->updateData();
        $output .= $updateTemplate->upgradeStep2();
        break;
    default:
        $output .= $updateTemplate->upgradeStep1();
        break;
}

$output .= $updateTemplate->CommonBodyEnd($lang);
display($output);
