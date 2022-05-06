<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 5-05-2022
 ****************************************************************
 *  list.php
 *  Lists address book entries. This is the main page.
 *
 *************************************************************/

require_once('.\Core.php');
include(FILE_CLASS_BIRTHDAY);
require_once('./lib/Templates/list.Template.php');

global $lang, $country, $globalUsers, $globalSqlLink, $fileUrl;

$globalUsers->checkForLogin();
$myListTemplate = new listTemplate();
// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
$options = new Options();

// ** END INITIALIZATION *******************************************************

// CREATE THE LIST.
$list = new ContactList($options);


// THIS PAGE TAKES SEVERAL GET VARIABLES
// ie. list.php?group_id=6&page=2&letter=c&limit=20
if ($_GET['groupid']) {
    $list->setgroup_id($_GET['groupid']);
}
if ($_GET['page']) {
    $list->setcurrent_page($_GET['page']);
}
if (isset($_GET['letter'])){
    $list->current_letter($_GET['letter']);
}
if (isset($_GET['limit'])) {
    $list->max_entries($_GET['limit']);
}

// Set group name (group_id defaults to 0 if not provided)
$list->group_name();

// ** RETRIEVE CONTACT LIST BY GROUP **
$r_contact = $list->retrieve();


// PRINT WELCOME MESSAGE
if ($options->getWelcomeMessage() != "") {
    $body['msgWelcome'] =$options->getWelcomeMessage();
}

if (($_SESSION['username'] == "@auth_off") || ($_SESSION['usertype'] == "guest")) {
        $body['Login'] = 'guest';
}
else {
    $body['username'] = $_SESSION['username'];
    $body['usertype'] = $_SESSION['usertype'];
}


// **INCLUDE BIRTHDAY LIST**
$body['displayAsPopup'] = $options->getdisplayAsPopup();
$body['birthday'] = '';
if ($options->getbdayDisplay() == 1) {
    $myBirthday = new Birthday();
    $body['birthday'] = $myBirthday->GetBirthday($options, $lang);
}

$body['LBL_GOTO'] = $lang['LBL_GOTO'];


// DISPLAY TOOLBOX according to user type
if ($_SESSION['usertype'] == "admin" || $_SESSION['usertype'] == "user") {
    /**
     * trying to avoid 2 different HTML items in template that are the same but for extremely minor changes.
     */
    $body['usertype'] = 1;
    $body['toolbox'] = $lang['TOOLBOX_ADD'];
    if($_SESSION['usertype'] == "user"){
        $body['tdinside']= -1;
        $body['fileopt'] = $fileUrl['FILE_USERS'];
        $body['toolusersettings'] = $lang['LBL_USR_ACCT_SET'];
        $body['tdinside1']= "";
        $body['FILE_EXPORT'] = $fileUrl['FILE_MAILTO'];
        $body['Toolexprt'] =$lang['TOOLBOX_MAILINGLIST'];
        $body['FILE_SCRATCHPAD'] = $fileUrl['FILE_EXPORT'];
        $body['toolscratchpd'] = $lang['TOOLBOX_EXPORT'];
        $body['tdinside2'] = "";
    }
    else{
        $body['fileopt'] = $fileUrl['FILE_OPTIONS'];
        $body['toolusersettings'] = $lang['TOOLBOX_OPTIONS'];
        $body['FILE_EXPORT'] = $fileUrl['FILE_EXPORT'];
        $body['Toolexprt'] = $lang['TOOLBOX_EXPORT'];
        $body['FILE_SCRATCHPAD'] = $fileUrl['FILE_SCRATCHPAD'];
        $body['toolscratchpd'] = $lang['TOOLBOX_SCRATCHPAD'];
    }
}
else {
    $body['toolusersettings'] = $lang['TOOLBOX_EXPORT'];
}
$body['nav_list'] = $list->create_nav();
$body['titleish'] = $list->title();
$body['groupsel'] = $lang['GROUP_SELECT'];


// -- GENERATE GROUP SELECTION LIST --
// Only admins can view hidden entries.
$options->setupAllGroups($body, $list);

// DISPLAY IF NO ENTRIES UNDER GROUP
$body['useMailScript'] = $options->getuseMailScript();
$body['contacts'] = $r_contact;
if (count($r_contact)<1) {
    $body['noContacts'] = $lang['NO_ENTRIES'];
}
else {
    // DISPLAY ENTRIES
    $body['openPopUp'] = $options->getdisplayAsPopup();

}

$output = webheader($lang['TITLE_TAB'] ." - ". $lang['TITLE_LIST'], $lang['CHARSET']);
$output .=$myListTemplate->listbodystart($body,$list, $fileUrl);

display($output);