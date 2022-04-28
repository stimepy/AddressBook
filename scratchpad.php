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

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
$options = new Options();

$output = webheader($lang[TITLE_TAB]." - ".$lang[TITLE_SCRATCH], $lang['CHARSET']);
?>



<?php
// CHECK TO SEE IF A FORM HAS BEEN SUBMITTED, AND SAVE THE SCRATCHPAD.
    if ($_POST['saveNotes'] == "YES") {

	    $notes = addslashes( trim($_POST['notes']) );
	    
        // UPDATES THE SCRATCHPAD TABLE
        //$sql = "UPDATE ". TABLE_SCRATCHPAD ." SET notes='$notes'";
        $globalSqlLink->UpdateQuery(array('notes'=> "'".$notes."'" ), TABLE_SCRATCHPAD, NULL);
       //$update = mysql_query($sql, $db_link)
		//	or die(reportSQLError($sql));

        echo($lang[SCRATCH_SAVED]."\n");
/*

*/
    }

// DISPLAY CONTENTS OF SCRATCHPAD.

    // Retrieve data
    $globalSqlLink->SelectQuery('notes',TABLE_SCRATCHPAD, "id = 1", "limit 1" );
    $notes = $globalSqlLink->fetchQueryResult();
    //$notes = mysql_query("SELECT notes FROM " . TABLE_SCRATCHPAD . " LIMIT 1", $db_link);
    //$notes = mysql_fetch_array($notes);
    $notes = stripslashes( $notes["notes"] );

    // Split $notes into an array by newline character
    $displayArray = explode("\n",$notes);

    // Determine the number of lines in the array
    //$z = 0;
    //while (each($displayArray)) {
    $z=sizeof($displayArray);
    //}

    // Grab each line of the array and display it
    for ($a = 0; $a < $z; $a++) {
        echo("<BR>$displayArray[$a]");
    }

?>
              </TD>
           </TR>
           <TR VALIGN="top">
              <TD WIDTH=550 CLASS="listDivide">&nbsp;</TD>
           </TR>
                      
           <TR VALIGN="top">
              <TD WIDTH=550 CLASS="listHeader"><A NAME="edit"></A><?php echo ucfirst($lang['BTN_EDIT'])?></TD>
           </TR>
           <TR VALIGN="top">
              <TD WIDTH=550 CLASS="data">
<TEXTAREA STYLE="width:530px;" ROWS=30 CLASS="formTextarea" NAME="notes" WRAP=off>
<?php
  echo("$notes");
?>
</TEXTAREA>           
              </TD>
           </TR>

           <TR VALIGN="top">
              <TD WIDTH=550 CLASS="listDivide">&nbsp;</TD>
           </TR>

           <TR VALIGN="top">
              <TD WIDTH=550 CLASS="navmenu">
      <NOSCRIPT>
        <!-- Will display Form Submit buttons for browsers without Javascript -->
        <INPUT TYPE="submit" VALUE="Save">
        <!-- There is no delete button -->
        <!-- later make it so link versions don't appear -->
      </NOSCRIPT>
      <A HREF="#" onClick="saveEntry(); return false;"><?php echo $lang['BTN_SAVE']?></A>
      <A HREF="<?php echo(FILE_LIST); ?>"><?php echo $lang['BTN_RETURN']?></A>
              </TD>
           </TR>


        </TABLE>

    </TD>
  </TR>
</TABLE>
</CENTER>

</FORM>


</BODY>
</HTML>
