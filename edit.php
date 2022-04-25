<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.04e
 *  
 *
 *************************************************************
 *  edit.php
 *  Edit address book entries. 
 *
 *************************************************************/


require_once('Core.php');
require_once ('./lib/Templates/edit.Template.php');

// ** OPEN CONNECTION TO THE DATABASE **
//	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

global $globalSqlLink, $globalUsers, $lang, $country;
$myAddressDetailsTemplate = new editTemplate();

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
$options = new Options();
$globalUsers->checkForLogin('admin', 'user');
// ** CHECK FOR ID **

$body['mode'] = $_GET['mode'];

$body['fileSave'] = FILE_SAVE;
$body['BTN_SAVE'] = $lang['BTN_SAVE'];
$body['TABLE_EMAIL'] = TABLE_EMAIL;
$body['TABLE_OTHERPHONE'] =TABLE_OTHERPHONE;
$body['TABLE_MESSAGING'] = TABLE_MESSAGING;
$body['TABLE_WEBSITES'] = TABLE_WEBSITES;
$body['TABLE_ADDITIONALDATA'] = TABLE_ADDITIONALDATA;

// ** END INITIALIZATION *******************************************************

if ($body['mode'] == 'new') {
    $body['id'] = '0';
    $body['cancelUrl'] = FILE_LIST;
}  // end New address
else {
    $body['mode'] = 'edit';
    $body['id'] = check_id();
    $body['cancelUrl'] = FILE_ADDRESS;


    // NOTE: Groups is determined with a special query that will be run at the bottom of the page.
    $globalSqlLink->SelectQuery('firstname, lastname, middlename, primaryAddress, birthday, nickname, nickname, pictureURL, notes, hidden, whoAdded, DATE_FORMAT(lastUpdate, \"%W, %M %e %Y (%h:%i %p)\") AS lastUpdate',TABLE_CONTACT, " id=".$body['id'], NULL);
    $tbl_contact = $globalSqlLink->FetchQueryResult();

    // Put data into variable holders -- taken from arrays that are created from query results.
    $body['contact_firstname']  = stripslashes( $tbl_contact['firstname'] );
    $body['contact_lastname'] = stripslashes( $tbl_contact['lastname'] );
    $body['contact_middlename'] = stripslashes( $tbl_contact['middlename'] );
    $body['contact_primaryAddress'] = stripslashes( $tbl_contact['primaryAddress'] );
    $body['contact_birthday'] = ($tbl_contact['birthday'])? stripslashes( $tbl_contact['birthday'] ) : "0000-00-00";
    $body['contact_nickname'] = stripslashes( $tbl_contact['nickname'] );
    $body['contact_pictureURL'] = stripslashes( $tbl_contact['pictureURL'] );
    $body['contact_notes'] = stripslashes( $tbl_contact['notes'] );
    $body['contact_lastUpdate'] = stripslashes( $tbl_contact['lastUpdate'] );
    $body['contact_whoAdded'] = stripslashes( $tbl_contact['whoAdded'] );

    $contact_hiddenFlag = $tbl_contact['hidden']; // It's a 1/0 flag so we just need to check here, not else where.

    // Check to see if the person who got to this edit record is the person whoAdded it.
    // Without this code, someone could click on a record they are allowed to edit, then change the id in the URL to any other.
    if ((($body['contact_whoAdded'] != $_SESSION['username']) AND ($_SESSION['usertype'] != 'admin') AND ($body['mode'] != 'new')) OR ($_SESSION['usertype'] == 'guest')) {
        $_SESSION = array();
        session_destroy();
        // if this happens we need to log this betters....  todo for future.
        die("URL tampering detected. You have been logged out.");
    }

	// ADDRESSES
	// A do-while loop is made to ensure that there is 2 blank entries if person has NO address information.
    $globalSqlLink->SelectQuery('*', TABLE_ADDRESS, "id=".$body['id'], NULL);
    $r_address = $globalSqlLink->FetchQueryResult();


    if($r_address != -1) {
        $body['r_address'] = $r_address;
    }

    // E-mail
    $globalSqlLink->SelectQuery('*', TABLE_EMAIL, "id=".$body['id'], NULL);
    $r_email = $globalSqlLink->FetchQueryResult();
    // = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
    if($r_email != -1) {
        foreach ($r_email as $tbl_email) {
            $body['r_email'][] = stripslashes($tbl_email['email'])."|".stripslashes($tbl_email['type'])."\n";
        }
    }

    // Other Phone Numbers
    $globalSqlLink->SelectQuery('*', TABLE_OTHERPHONE, "id=".$body['id'], NULL);
    $r_otherPhone = $globalSqlLink->FetchQueryResult();
    if($r_otherPhone !=-1) {
        foreach ($r_otherPhone as $tbl_otherPhone) {
            $body['r_otherPhone'][] = stripslashes($tbl_otherPhone['phone'])."|".stripslashes($tbl_otherPhone['type'])."\n";
        }
    }

    // Messaging
    $globalSqlLink->SelectQuery('*', TABLE_MESSAGING, "id=".$body['id'], NULL);
    $r_messaging= $globalSqlLink->FetchQueryResult();
    if($r_messaging !=-1) {
        foreach ($r_messaging as $tbl_messaging) {
            $body['r_messaging'][] = stripslashes($tbl_messaging['handle'])."|".stripslashes($tbl_messaging['type'])."\n";
        }
    }

    // Websites
    $globalSqlLink->SelectQuery('*',TABLE_WEBSITES,"id=".$body['id'], null);
    $r_websites = $globalSqlLink->FetchQueryResult();
    if($r_websites !=-1) {
        foreach ($r_websites as $tbl_websites) {
            $body['r_websites'][] =stripslashes($tbl_websites['webpageURL'])."|".stripslashes($tbl_websites['webpageName'])."\n";
        }
    }

    // Display Upload link if allowed by options
    $body['allowPicUpload'] = 0;
    if (($options->getpicAllowUpload() == 1) || ($_SESSION['usertype'] == "admin")) {
        $body['allowPicUpload'] = FILE_UPLOAD;
    }

    //////////////////   start here////////////////////////////

    // AdditionalData
    $globalSqlLink->SelectQuery('*', TABLE_ADDITIONALDATA, "id=".$body['id'], NULL);
    $r_additionalData = $globalSqlLink->FetchQueryResult();
    if($r_additionalData !=-1){
        foreach($r_additionalData as $tbl_additionalData){
            $body['r_additionalData'][] = stripslashes( $tbl_additionalData['type'])."|".stripslashes( $tbl_additionalData['value'] )."\n";
        }
    }

    $tables = TABLE_GROUPLIST . " AS grouplist LEFT JOIN " . TABLE_GROUPS . " AS groups ON grouplist.groupid=groups.groupid AND id=".$body['id'];

    $globalSqlLink->SelectQuery('grouplist.groupid, groupname, id', $tables, "grouplist.groupid >= 3", "ORDER BY groupname" );
    $r_grouplist = $globalSqlLink->FetchQueryResult();

    $body['numGroups'] = round($globalSqlLink->GetRowCount()/2);  // assigns to $numGroups the number of Groups to display in the first column.

    // COLUMN 1
    // $x is checked FIRST because if that fails, $tbl_grouplist will have already been evaluated
    if($r_grouplist != -1) {
        $body['r_grouplist'] = $r_grouplist;
    }

    if ( $contact_hiddenFlag == 1 ) {
        $body['contact_hidden'] = "CHECKED";
    }
} // End edit address

$output = webheader($lang['TITLE_TAB'], $lang['CHARSET'], "edit.script.js");
$output .= $myAddressDetailsTemplate->editbody($body, $lang, $country,$options->getcountryDefault());

Display(($output));


