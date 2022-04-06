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

// ** OPEN CONNECTION TO THE DATABASE **
//	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

global $globalSqlLink, $globalUsers, $lang;


// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
    $globalUsers->checkForLogin('admin', 'user');
// ** CHECK FOR ID **
	$body['mode'] = $_GET['mode'];
    $body['id'] = '0';
    $body['cancelUrl'] = FILE_LIST;
    $body['fileSave'] = FILE_SAVE;
    $body['BTN_SAVE'] = BTN_SAVE;
    $body['TABLE_EMAIL'] = TABLE_EMAIL;
    $body['TABLE_OTHERPHONE'] =TABLE_OTHERPHONE;
    $body['TABLE_MESSAGING'] = TABLE_MESSAGING;
    $body['TABLE_WEBSITES'] = TABLE_WEBSITES;
    $output = webheader($lang['TITLE_TAB'], $lang['CHARSET'], "edit.script.js")

	// E-mail
    $globalSqlLink->SelectQuery('*', TABLE_EMAIL, "id=".$body['id'], NULL);
    $r_email = $globalSqlLink->FetchQueryResult();
    // = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
    if($r_email != -1) {
        foreach ($r_email as $tbl_email) {
            $bpdy['r_email'][] = stripslashes($tbl_email['email'])."|".stripslashes($tbl_email['type'])."\n";
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
	if (($options->picAllowUpload == 1) || ($_SESSION['usertype'] == "admin")) {
        $body['allowPicUpload'] = FILE_UPLOAD;
	}
?>
				</TD>
				<TD WIDTH=185 CLASS="data">
					<B><?php echo $lang['LBL_NICKNAME']?></B>
					<BR><INPUT TYPE="text" SIZE=20 CLASS="formTextbox" NAME="nickname" VALUE="<?php echo($contact_nickname); ?>">
				</TD>
			</TR>

		   <TR VALIGN="top">
			  <TD WIDTH=375 CLASS="data" COLSPAN=2>
<TEXTAREA STYLE="width:340px;" ROWS=9 CLASS="formTextarea" NAME="<?php echo(TABLE_ADDITIONALDATA); ?>" WRAP=off>
<?php
	// AdditionalData
    $globalSqlLink->SelectQuery('*', TABLE_ADDITIONALDATA, "id=".$id, NULL);
   $r_additionalData = $globalSqlLink->FetchQueryResult();
    if($r_additionalData !=-1){
        foreach($r_additionalData as $tbl_additionalData){
        //while ( $tbl_additionalData = mysql_fetch_array($r_additionalData) ) {
            $additionaldata_type = stripslashes( $tbl_additionalData['type'] );
            $additionaldata_value = stripslashes( $tbl_additionalData['value'] );
            echo("$additionaldata_type|$additionaldata_value\n");
        }
    }
?>
</TEXTAREA>
			  </TD>
			  <TD WIDTH=185 CLASS="data">
					 <?php echo $lang['EDIT_HELP_OTHERINFO']?>
			  </TD>
		   </TR>


		   <TR VALIGN="top">
			  <TD WIDTH=560 COLSPAN=3 CLASS="listHeader"><?php echo $lang['LBL_NOTES']?></TD>
		   </TR>

		   <TR VALIGN="top">
			  <TD WIDTH=560 CLASS="data" COLSPAN=3>
<?php echo $lang['EDIT_HELP_NOTES']?><BR>

<TEXTAREA STYLE="width:530px;" ROWS=6 CLASS="formTextarea" NAME="notes" WRAP=virtual>
<?php
	// Notes
	echo("$contact_notes");
?>
</TEXTAREA>
			  </TD>
		   </TR>


		   <TR VALIGN="top">
			  <TD WIDTH=560 COLSPAN=3 CLASS="listHeader"><?php echo $lang['LBL_GROUPS']?></TD>
		   </TR>
		   <TR VALIGN="top">
			  <TD WIDTH=190 CLASS="data">
<?php

	// Display Group Checkboxes.
	//$groupsql = "SELECT grouplist.groupid, groupname, id
	//			 FROM " . TABLE_GROUPLIST . " AS grouplist
	//			 LEFT JOIN " . TABLE_GROUPS . " AS groups
	//			 ON grouplist.groupid=groups.groupid AND id=$id
	//			 WHERE grouplist.groupid >= 3
	//			 ORDER BY groupname";
	$tables = TABLE_GROUPLIST . " AS grouplist LEFT JOIN " . TABLE_GROUPS . " AS groups ON grouplist.groupid=groups.groupid AND id=".$id;

	$globalSqlLink->SelectQuery('grouplist.groupid, groupname, id', $tables, "grouplist.groupid >= 3", "ORDER BY groupname" );
    $r_grouplist = $globalSqlLink->FetchQueryResult();

	$numGroups = round($globalSqlLink->GetRowCount()/2);  // assigns to $numGroups the number of Groups to display in the first column.
	$x = 0;
	$groupCheck = ""; 

	// COLUMN 1
	// $x is checked FIRST because if that fails, $tbl_grouplist will have already been evaluated
    if($r_grouplist != -1) {
        foreach ($r_grouplist as $tbl_grouplist) {
            //while ( ($x < $numGroups) && ($tbl_grouplist = mysql_fetch_array($r_grouplist)) ) {
            $group_id = $tbl_grouplist['groupid'];
            $group_name = $tbl_grouplist['groupname'];
            if ($tbl_grouplist['id'] == $id) {
                $groupCheck = " CHECKED";
            }
            if ($x == $numGroups) {
                echo " 			  </TD>			  <TD WIDTH=185 CLASS=\"data\">";
            }

            echo("<INPUT TYPE=\"checkbox\" NAME=\"groups[]\" VALUE=\"$group_id\"$groupCheck><B>$group_name</B>\n<BR>");
            //reset $groupCheck so that it doesn't stay set if the next ID does not equal $id.
            $groupCheck = "";
            $x++;

        }
    }


?>
			  </TD>
			  <TD WIDTH=185 CLASS="data">
				   <INPUT TYPE="checkbox" NAME="groupAddNew" VALUE="addNew"><B><?php echo $lang['EDIT_ADD_NEW_GROUP']?></B>
				   <BR><INPUT TYPE="text" SIZE=20 CLASS="formTextbox" NAME="groupAddName" VALUE="" MAXLENGTH=60>
			  </TD>
		   </TR>


		   <TR VALIGN="top">
			  <TD WIDTH=560 COLSPAN=3 CLASS="listDivide">&nbsp;</TD>
		   </TR>

		   <TR VALIGN="top">
			  <TD WIDTH=560 CLASS="data" COLSPAN=3>
<?php
	echo("<INPUT TYPE=\"checkbox\" NAME=\"hidden\" VALUE=\"1\"");
	if ( $contact_hidden == 1 ) {
			echo(" CHECKED");
	}
	echo("><B>".$lang['EDIT_HIDE_ENTRY']."</B>");
?>
			  </TD>
		   </TR>

		   <TR VALIGN="top">
			  <TD WIDTH=560 COLSPAN=3 CLASS="listDivide">&nbsp;</TD>
		   </TR>

		   <TR VALIGN="top">
			  <TD WIDTH=560 COLSPAN=3 CLASS="navmenu">
	  <A HREF="#" onClick="saveEntry(); return false;"><?php echo $lang['BTN_SAVE']?></A>
<?php
// PRINT CANCEL AND/OR DELETE BUTTONS
	if ($mode == 'new') {
		echo("      <A HREF=\"" . FILE_LIST . "\">".$lang['BTN_CANCEL']."</A>\n");
	}
	else { 
		echo("      <A HREF=\"#\" onClick=\"deleteEntry(); return false;\">".$lang['BTN_DELETE']."</A>\n");
		echo("      <A HREF=\"" . FILE_ADDRESS . "?id=$id\">".$lang['BTN_CANCEL']."</A>\n");
	}
?>
			  </TD>
		   </TR>


		</TABLE>

			   
	</TD>
  </TR>
  <TR>
	<TD CLASS="update">
<?php
	if ($mode == 'new') {
		echo("&nbsp;");
	}
	else { 
		echo "<br>".$lang['LAST_UPDATE']." ". $contact_lastUpdate;
	}
?>
	</TD>
  </TR>
</TABLE>
</CENTER>

</FORM>

</BODY>
</HTML>
<?php

	// DECLARE CALLBACK FUNCTION
	// The callback function looks for the text VAR_ADDNUM and replaces it with a number
	// equal to the number of address entries present in the document. This number is
	// not determined until the address code is processed, and it is necessary at the
	// top of the edit script for the "save" link. Therefore an output buffer is created
	// which allows address code to be processed, and then the callback function goes
	// through the buffer and replaces the VAR_ADDNUM before displaying the buffer.
	function callback($buffer) 	{
		global $addnum;
		return (str_replace("VAR_ADDNUM", $addnum, $buffer));
	}

	// OUTPUT BUFFER
	ob_end_flush();
?>
