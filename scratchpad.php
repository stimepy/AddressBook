<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-27-2022
 ****************************************************************
 *  scratchpad.php
 *  Temporary placeholder for notes and such.
 *
 *************************************************************/


// ** GET CONFIGURATION DATA **
require_once('.\Core.php');
require_once("./lib/Templates/scratchpad.Template.php");

global $globalSqlLink, $globalUsers, $lang, $fileUrl, $paths;

$globalUsers->checkForLogin();
$scratch = new scratchpadTemplate();

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
$options = new Options();



// CHECK TO SEE IF A FORM HAS BEEN SUBMITTED, AND SAVE THE SCRATCHPAD.   IN BODY NEAR TOP
$body["recentSave"] = false;
if ($_POST['saveNotes'] == "YES") {

    $notes = addslashes( trim($_POST['notes']) );

    // UPDATES THE SCRATCHPAD TABLE
    //$sql = "UPDATE ". TABLE_SCRATCHPAD ." SET notes='$notes'";
    $globalSqlLink->UpdateQuery(array('notes'=> "'".$notes."'" ), TABLE_SCRATCHPAD, NULL);
   //$update = mysql_query($sql, $db_link)
    //	or die(reportSQLError($sql));
    $body["recentSave"] = true;
}

// DISPLAY CONTENTS OF SCRATCHPAD.
$body['notes'] = "";
// Retrieve data
$globalSqlLink->SelectQuery('notes',TABLE_SCRATCHPAD, "id = 1", "limit 1" );
$notes = $globalSqlLink->fetchQueryResult();

if($notes != -1) {
    $body['notes'] = stripslashes($notes[0]["notes"]);
}
    /*// Split $notes into an array by newline character
    $displayArray = explode("\n",$notes);

    // Determine the number of lines in the array
    $z=sizeof($displayArray);

    // Grab each line of the array and display it
    for ($a = 0; $a < $z; $a++) {
        echo("<BR>$displayArray[$a]");
    }*/

$output = webheader($lang['TITLE_TAB']." - ".$lang['TITLE_SCRATCH'], $lang['CHARSET']);
$output .=$scratch->scratchpadMain($body, $lang, $fileUrl);
display($output);