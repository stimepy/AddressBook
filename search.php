<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-27-2022
 ****************************************************************
 *  search.php
 *  Searches address book entries. 
 *
 *************************************************************/


require_once('./Core.php');
require_once ("./lib/Templates/address.template.php");

global $globalSqlLink, $globalUsers,$lang;

$globalUsers->checkForLogin();

// RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE
$options = new Options();
$mySearchAddress = new addressTemplate();


// See if search terms have been passed to this page.
$goTo = $_POST['goTo'];
if (!$goTo) { //  AND !$search) {
    die($lang['SEARCH_TERMS']);  // todo: return back to list with an error message.
}

// goTo functionality
if ($goTo) {
    $select = "id, CONCAT(lastname,', ',firstname) AS fullname, lastname, firstname";
    $where ="CONCAT(firstname,' ', lastname) LIKE '%$goTo%' OR CONCAT(firstname,' ', middlename,' ', lastname) LIKE '%$goTo%' OR nickname LIKE '%$goTo%'";
    $globalSqlLink->SelectQuery($select,TABLE_CONTACT, $where, "ORDER BY fullname" );
    $r_goto = $globalSqlLink->FetchQueryResult();

}

// print results
$output = webheader($lang[TITLE_TAB]." - ".$lang[SEARCH_LBL], $lang['CHARSET']);

if ($globalSqlLink->GetRowCount() == 1) {
    if ($options->getdisplayAsPopup() == 1) {
        $body['address'] = FILE_ADDRESS . "?id=" . $r_goto[0]['id'];

        $output .= $mySearchAddress->returnSingleSearch($body, $lang);
    }
    else {
        header("Location: " . FILE_ADDRESS . "?id=" . $r_goto[0]['id']);
        exit();
    }
}
else{
    $body['search'] = $goTo;
    $body['r_goto'] = $r_goto;
    $body['FILE_ADDRESS'] = FILE_ADDRESS;
    $body['displayAsPopup'] = $options->getdisplayAsPopup();
    $body['FILE_LIST'] = FILE_LIST;

    $output .= $mySearchAddress->searchMultiResults($body, $lang);
}

$output .= $mySearchAddress->searchFooter();
display($output);
