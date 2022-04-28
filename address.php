<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-17-2022
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


// CALCULATE 'NEXT' AND 'PREVIOUS' ADDRESS ENTRIES


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
$output = webheader($TitleHeader, $lang['CHARSET']);


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

if ($options->getdisplayAsPopup() == 1) {
    $body['displayAsPopup'] = "<A HREF=\"#\" onClick=\"window.close();\">". $lang['BTN_CLOSE'] ."</A>";
}
else {
    $body['displayAsPopup'] = "<A HREF=\"".FILE_LIST."\">". $lang[BTN_LIST] ."</A>";
}

$body['$contact']['name'] = $contact->getlastname();
if( $contact->getFirstName()){
    $body['$contact']['name'] .= ", ".$contact->getFirstName();
}
if ($contact->getMiddleName()){
    $body['$contact']['name'] .= " ".$contact->getMiddleName();
}
if ($contact->getnickname()) {
    $body['$contact']['name'] .= " \"$contact->getnickname()\"";
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
if ( $globalSqlLink->GetRowCount() == 0 ) {
    $body['spacer']  = "<IMG SRC=\"spacer.gif\" WIDTH=1 HEIGHT=1 BORDER=0 ALT=\"\">";
}
else{
    $body['spacer'] = '';
}


$body['tableColumnAmt2'] =$body['tableColumnAmt'];
// ** PICTURE BOX **
if ($body['tableColumnAmt'] == 3) {
    $picture = ($contact->getpicture_url())? PATH_MUGSHOTS . $contact->getpicture_url():"images/nopicture.gif";
    $body['tableColumnAmt'] ="                    <TD WIDTH=". $options->getpicWidth() .">
                        <IMG SRC=\"" . $picture ."\" WIDTH=". $options->getpicWidth() ." HEIGHT=". $options->getpicHeight() ." BORDER=1 ALT=\"\">
                </TD>";
}
else{
    $body['tableColumnAmt'] ="";
}

$body['tableColumnWidth'] = $body['tableColumnAmt'];


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
        }
        $body["emails"][$emlcnt] .= "</p>";
        $emlcnt++;
    }
}


// ** OTHER PHONE NUMBERS **
$globalSqlLink->SelectQuery('*', TABLE_OTHERPHONE, "id=".$contact->getid(), NULL);
$r_otherPhone = $globalSqlLink->FetchQueryResult();

if ($globalSqlLink->GetRowCount() > 0) {

    $otherphonecnt = 0;
    foreach ($r_otherPhone as $tbl_otherPhone){
    //while ($tbl_otherPhone = mysql_fetch_array($r_otherPhone)) {
        $otherphone_phone = stripslashes( $tbl_otherPhone['phone'] );

        $body["otherphone"][$otherphonecnt] .="<br />". stripslashes( $tbl_otherPhone['type'] ) .": ".stripslashes( $tbl_otherPhone['phone'] );
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
//$r_messaging = mysql_query("SELECT * FROM " . TABLE_MESSAGING . " AS messaging WHERE messaging.id=$id", $db_link);
//$tbl_messaging = mysql_fetch_array($r_messaging);
$message = $globalSqlLink->GetRowCount();
if ($message) {
    $otherphonecnt = 0;
    foreach($r_messaging as $tbl_messaging){
    //while ($tbl_messaging = mysql_fetch_array($r_messaging)) {
        $messaging_handle = stripslashes( $tbl_messaging['handle'] );
        $messaging_type = stripslashes( $tbl_messaging['type'] );
        $body["message"][$otherphonecnt] = "<br />".stripslashes( $tbl_messaging['type'] ) .": ". stripslashes( $tbl_messaging['handle'] );
        $otherphonecnt++;
    }
}

// ** BIRTHDAY **
if ($contact->getBirthday()) {
    $body["birthday"] ="                 <tr VALIGN=\"top\">\
               <td WIDTH=120 CLASS=\"data\">
                    <b>". $lang['LBL_BIRTHDATE'] ."</b>
                </td>
               <td WIDTH=440 CLASS=\"data\"> ". $contact->getBirthday() ."</td>
             </tr>";
}
else {
    $body["birthday"] = "";
}

// ** ADDITIONAL DATA **
$globalSqlLink->SelectQuery('*', TABLE_ADDITIONALDATA,  'id='.$contact->getid(), NULL);
$r_additionalData =$globalSqlLink->FetchQueryResult();
$additioncnt =0;
if($r_additionalData != -1) {
    foreach ($r_additionalData as $tbl_additionalData) {
        //while ( $tbl_additionalData = mysql_fetch_array($r_additionalData) ) {
        $body["additional"][$additioncnt] = "                 <tr VALIGN=\"top\">
               <td WIDTH=120 CLASS=\"data\">
                    <b>" . stripslashes($tbl_additionalData['type']) . "</b>
               </td>
               <td WIDTH=440 CLASS=\"data\">
                    " . stripslashes($tbl_additionalData['value']) . "
                </td>
             </tr>";
    }
}

// ** WEBSITES **
$globalSqlLink->SelectQuery('*', TABLE_WEBSITES, 'id='.$contact->getid(), NULL);
$r_websites = $globalSqlLink->FetchQueryResult();
//$r_websites = mysql_query("SELECT * FROM " . TABLE_WEBSITES . " AS websites WHERE websites.id=$id", $db_link);
//$tbl_websites = mysql_fetch_array($r_websites);
//$websiteURL = stripslashes( $tbl_websites['webpageURL'] );
//$websiteName = stripslashes( $tbl_websites['webpageName'] );
if ($globalSqlLink->GetRowCount() > 0) {
    $x = 0;
    $body['Websites'][$x] ="                 <TR VALIGN=\"top\">
                       <td WIDTH=120 CLASS=\"data\">
                            <b>". $lang[LBL_WEBSITES] ."</b>
                       </td>
                       <TD WIDTH=440 CLASS=\"data\">\n";


    foreach($r_websites as $r_websites){
    // while ($tbl_websites = mysql_fetch_array($r_websites)) {
        $websiteURL = stripslashes( $tbl_websites['webpageURL'] );
        $websiteName = stripslashes( $tbl_websites['webpageName'] );
        $body['Websites'][$x] .="                      <BR><A HREF=\"$websiteURL\" TARGET=\"out\">
            ". ($websiteName)? websiteName : $websiteURL ."</A>";
    }
    $body['Websites'][$x] .="                   </TD>
                     </TR>";
}



// ** NOTES **
if ($contact->getnotes()) {
    $body['note'] = $contact->getnotes();
    $body['LBL_NOTES'] = $lang['LBL_NOTES'];
}

$body['lastUpdatetxt'] = $lang['LAST_UPDATE'];
$body['lastupdate'] = $contact->getlast_update();

$output .=  $myAddress->addressBodyStart($body, $lang);
display($output);



