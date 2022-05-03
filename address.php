<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 5-02-2022
 ****************************************************************
 *  address.php
 *  Displays address book entries.
 *
 *************************************************************/


require_once('.\Core.php');
require_once(".\lib\ContactInformation.php");

global $globalSqlLink, $globalUsers, $lang;

$globalUsers->checkForLogin();

$myAddress = new addressTemplate();

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
$options = new Options();
$list = new ContactList($options);
$contact = new ContactInformation(check_id());


$body['next'] = $contact->determinePreviousAddress();



// PICTURE STUFF.
// do we have a picture?
if ($contact->getpicture_url() || $options->getpicAlwaysDisplay() == 1) {
    $body['tableColumnAmt'] = 3;
    $body['tableColumnWidth'] = (540 - $options->getpicWidth()) / 2;
}
else {
    $body['tableColumnAmt'] = 2;
    $body['tableColumnWidth'] = (540 / 2);
}

$TitleHeader = $lang['TAB'].' - '.$lang['TITLE_ADDRESS']. ' '.$contact->getfullname();

if (($_SESSION['usertype'] == "admin") || ($_SESSION['username'] == $contact->getwho_added())) {
    $body['sessuser']['is'] = 1;
    $body['sessuser']['BTN_PRINT'] = $lang['BTN_PRINT'];
    $body['sessuser']['FILE_EDIT'] = FILE_EDIT;
    $body['sessuser']['id'] = $contact->getid();
    $body['sessuser']['BTN_EDIT'] = $lang['BTN_EDIT'];
}
else{
    $body['sessuser'] = 0;
}
$body['FILE_ADDRESS'] = FILE_ADDRESS;
$body['BTN_PREVIOUS'] = $lang['BTN_PREVIOUS'];
$body['BTN_NEXT'] = $lang['BTN_NEXT'];
$body['displayAsPopup'] = $options->getdisplayAsPopup();
$body['FILE_LIST'] = FILE_LIST;


$body['$contact']['name'] = $contact->getlastname();
if( $contact->getFirstName()){
    $body['$contact']['name'] .= ", ".$contact->getFirstName();
}
if ($contact->getMiddleName()){
    $body['$contact']['name'] .= " ".$contact->getMiddleName();
}
if ($contact->getnickname()) {
    $body['$contact']['name'] .= $contact->getnickname();
}

if ($contact->gethidden() == 1) {
    $body['HIDDENENTRY'] = "[HIDDEN ENTRY] ";
}
else{
    $body['HIDDENENTRY'] = '';
}


// LIST GROUPS
$globalSqlLink->SelectQuery('grouplist.groupid, groupname', TABLE_GROUPS . " AS groups LEFT JOIN " . TABLE_GROUPLIST. " AS grouplist ON groups.groupid=grouplist.groupid", 'id='.$contact->getid(), NULL );
$body['r_groups'] = $globalSqlLink->FetchQueryResult();
 // check if no groups
$body['spacer'] = $globalSqlLink->GetRowCount();

// ** PICTURE BOX **
if ($body['tableColumnAmt'] == 3) {
    $body['picture'] = ($contact->getpicture_url())? PATH_MUGSHOTS . $contact->getpicture_url():"images/nopicture.gif";
    $body['picwidth'] = $options->getpicWidth();
    $body['picheight'] = $options->getpicHeight();
}

//why????  commenting out for now
// $body['tableColumnWidth'] = $body['tableColumnAmt'];


$forcnt=0;
foreach( $contact->getAlladdress() as $tbl_address){
    $body['address'] = $list->buildcontact($tbl_address);
}


// ** E-MAIL **
// First check to see that the result set is filled. If so, create E-mail section header.
// Then start pulling data out of the result set and displaying them.
$r_email = $list->getEmailsByContactId($contact->getid());
$emlcnt = 0;
if ($r_email != -1) {

    $body["emails"][$emlcnt] ="";
    foreach( $r_email as $tbl_email){
        $body["emails"][$emlcnt] .= $list->createEmail($options->getuseMailScript(), hasValueOrBlank($tbl_email['email'] ));
        if ($tbl_email['type']) {
            $body["emails"][$emlcnt] .=" (".hasValueOrBlank( $tbl_email['type']).")";
        }  // has a </p> added in
        $emlcnt++;
    }
}


// ** OTHER PHONE NUMBERS **
$globalSqlLink->SelectQuery('*', TABLE_OTHERPHONE, "id=".$contact->getid(), NULL);
$r_otherPhone = $globalSqlLink->FetchQueryResult();

if ($globalSqlLink->GetRowCount() > 0) {

    $otherphonecnt = 0;
    foreach ($r_otherPhone as $tbl_otherPhone){
        $body["otherphone"][$otherphonecnt] .= stripslashes( $tbl_otherPhone['type'] ) .": ".stripslashes( $tbl_otherPhone['phone'] );
        $otherphonecnt++;
    }
}

// ** MESSAGING **
// A primitive version that does not output in desired format yet.
// Would like it to be:
//         <BR>AIM: name1, name2
//         <BR>ICQ: something
$globalSqlLink->SelectQuery( '*', TABLE_MESSAGING, 'id='.$contact->getid(), NULL);
$r_messaging = $globalSqlLink->FetchQueryResult();
$message = $globalSqlLink->GetRowCount();
if ($message) {
    $otherphonecnt = 0;
    foreach($r_messaging as $tbl_messaging){
        $body["message"][$otherphonecnt] = stripslashes( $tbl_messaging['type'] ) .": ". stripslashes( $tbl_messaging['handle'] );
        $otherphonecnt++;
    }
}

// ** BIRTHDAY **
$body["birthday"] = $contact->getBirthday();


// ** ADDITIONAL DATA **
$globalSqlLink->SelectQuery('*', TABLE_ADDITIONALDATA,  'id='.$contact->getid(), NULL);
$body['r_additionalData'] =$globalSqlLink->FetchQueryResult();
$additioncnt =0;

// ** WEBSITES **
$globalSqlLink->SelectQuery('*', TABLE_WEBSITES, 'id='.$contact->getid(), NULL);
$body['r_websites '] = $globalSqlLink->FetchQueryResult();

// ** NOTES **
if ($contact->getnotes()) {
    $body['note'] = $contact->getnotes();
    $body['LBL_NOTES'] = $lang['LBL_NOTES'];
}

$body['lastUpdatetxt'] = $lang['LAST_UPDATE'];
$body['lastupdate'] = $contact->getlast_update();

$output = webheader($TitleHeader, $lang['CHARSET']);
$output .= $myAddress->addressBodyStart($body, $lang);
display($output);