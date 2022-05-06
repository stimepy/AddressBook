<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 5-05-2022
 ****************************************************************
 *  list.Template.php
 *  list html
 *
 *************************************************************/

class listTemplate{

    function listbodystart($body, $list, $fileUrl)
    {
        global $lang;
        $listFunctions = new listTemplateFunctions();
        $list_LastLetter = "";

        $output = "<BODY onLoad = \"document.goToEntry.goTo.focus();\" >
        <A NAME = \"top\" ></A >
        <p>
        <TABLE align=\"center\" BORDER = 0 CELLPADDING = 0 CELLSPACING = 0 WIDTH = 570 >
		    <tr>
		        <td>
		            <img src = \"images/title.gif\" WIDTH = 570 HEIGHT = 90 ALT = \"\" BORDER = 0 >
                </td>
            </tr>	
            <tr>
                <td>
                    <TABLE BORDER = 0 CELLSPACING = 0 CELLPADDING = 0 WIDTH = 570 >
                        <tr VALIGN = \"top\" >
                            <td WIDTH = 285 CLASS=\"data\">";
        if ($body['msgWelcome'] != "") {
            $output .= "<b>" . $body['msgWelcome'] . "</b>";
        }
        if ( $body['Login'] == 'guest') {
            $output .="<br />". $lang['MSG_LOGIN_NOT'] ." <a href=\" ".$fileUrl['FILE_INDEX']."?mode=login\"> ".$lang['WELCOME_LOGIN']."</a>";
        }
        else {
            $output .="<br />".$lang['WELCOME_CURRENT_LOGIN']." <b>". $body['username'] ."</b>";
            switch($body['usertype']) {
                case  "admin":
                    $output .= "<br />" . $lang['WELCOME_ADMIN_ACCESS'];
                    break;
                case "user":
                    $output .= "<br />" . $lang['WELCOME_USER_ACCESS'];
                    break;
            }
            $output .= "<br /><a href=\" ".$fileUrl['FILE_INDEX']."?mode=logout\"> ".$lang['WELCOME_LOGOUT']."</a>";
        }
        $output .= "<br /><br /><br />
              </td>
              <TD WIDTH=285 CLASS=\"data\" ROWSPAN=3>
                    " . fillInBirthday($body['birthday'], $body['displayAsPopup'], $fileUrl['FILE_ADDRESS']) . "
              </TD>
         </TR>
         <TR VALIGN=\"top\">
            <TD WIDTH=285 CLASS=\"data\">
                <FORM NAME=\"goToEntry\" METHOD=\"post\" ACTION=\"" . $fileUrl['FILE_SEARCH'] . "\">
                   <b>" . $body['LBL_GOTO'] . "</b>
                    <br />
                    <INPUT TYPE=\"text\" WIDTH=50 CLASS=\"formTextbox\" NAME=\"goTo\">
                </FORM>
            </td>
         </TR>
         <TR VALIGN=\"bottom\">
             <TD WIDTH=285 CLASS=\"data\">";
        if ($body['usertype'] == 1) {
            if($body['tdinside']!=-1){
                $body['tdinside1'] ="<A HREF=\"". $fileUrl['FILE_MAILTO'] ."?groupid=".$list->getgroup_id()."\"><IMG SRC=\"images/b-mail.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><br /> ".$lang['TOOLBOX_MAILINGLIST']."</A>";
                $body['tdinside2'] = "<A HREF=\"". $fileUrl['FILE_USERS'] ."\"><IMG SRC=\"images/b-users.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><BR>". $lang['TOOLBOX_MANAGEUSERS'] ."</A>";
            }
            $output .= "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=270 COLS=3>
				<TR VALIGN=\"middle\" text-align=\"center\" HEIGHT=90>
					<td CLASS=\"data\" WIDTH=90>" . $listFunctions->editLink($body, $fileUrl) . "<IMG SRC=\"images/b-add.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><BR>" . $body['toolbox'] . " </A></td>
					<td CLASS=\"data\" WIDTH=90><A HREF=\"" . $body['FILE_USERS'] . "\"><IMG SRC=\"images/b-options.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><BR>" . $body['toolusersettings'] . "</A></CENTER></td>
					<td CLASS=\"data\" WIDTH=90>" . $body['tdinside1'] . "</td>
				</TR>
				<TR VALIGN=\"middle\" HEIGHT=90>
					<TD CLASS=\"data\" WIDTH=90><CENTER><A HREF=\"" . $body['FILE_EXPORT'] . "\"><IMG SRC=\"images/b-export.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><BR>" . $body['Toolexprt'] . "</A></CENTER></TD>
					<TD CLASS=\"data\" WIDTH=90><CENTER><A HREF=\"" . $body['FILE_SCRATCHPAD'] . "\"><IMG SRC=\"images/b-scratchpad.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><BR>" . $body['toolscratchpd'] . "</A></CENTER></TD>
					<TD CLASS=\"data\" WIDTH=90>" . $body['tdinside2'] . "</TD>
				</TR>
				</TABLE>";
        }
        else {
            $output .= "                <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=270 COLS=3>
				<TR VALIGN=\"middle\" text-align=\"center\" HEIGHT=90>
					<TD CLASS=\"data\" WIDTH=90><A HREF=\"" . $fileUrl['FILE_EXPORT'] . "\"><IMG SRC=\"images/b-export.gif\" WIDTH=50 HEIGHT=50 ALT=\"\" BORDER=0><br />" . $body['toolusersettings'] . "</A></CENTER></TD>
                    <TD CLASS=\"data\" WIDTH=90>&nbsp;</TD>
                    <TD CLASS=\"data\" WIDTH=90>&nbsp;</TD>
                </TR>
        </TABLE>";
        }

        $output .= "             </TD>
           </TR>
        </TABLE>


    <BR>
    </TD>
  </TR>
  <TR>
      <TD CLASS=\"navMenu\">" . $body['nav_list'] . "</TD>
</TR>
<TR>
    <TD>
        <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=570>
            <TR VALIGN=\"bottom\">
                <TD CLASS=\"headTitle\">" . $body['titleish'] . "</TD>
                <TD CLASS=\"headText\" ALIGN=right>
                    <FORM NAME=\"selectGroup\" METHOD=\"get\" ACTION=\"" . $fileUrl['action'] . "\">";
        $output .= createGroupOptions($body, $lang);
        $output .= "                 </FORM>
              </TD>
           </TR>
        </TABLE>
    </TD>
  </TR>
  <TR>
    <TD CLASS=\"infoBox\">
           <BR>
              <CENTER>
              <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=560>";

        if (count($body['contacts']) < 1) {
            $output .= "                 <TR VALIGN=\"top\">\n
                   <TD WIDTH=560 COLSPAN=4 CLASS=\"listEntry\">" . $body['noContacts'] . "</TD>\n
                 </TR>\n";
        } else {
            foreach ($body['contacts'] as $tbl_contact) {

                $tbl_contact['fullname'] = stripslashes($tbl_contact['fullname']);
                $tbl_contact['line1'] = stripslashes($tbl_contact['line1']);
                $tbl_contact['line2'] = stripslashes($tbl_contact['line2']);
                $tbl_contact['city'] = stripslashes($tbl_contact['city']);
                $tbl_contact['state'] = stripslashes($tbl_contact['state']);
                $tbl_contact['zip'] = stripslashes($tbl_contact['zip']);
                $tbl_contact['phone1'] = stripslashes($tbl_contact['phone1']);
                $tbl_contact['phone2'] = stripslashes($tbl_contact['phone2']);

                $list_NewLetter = strtoupper(substr($tbl_contact['fullname'], 0, 1));
                if ($list_NewLetter != $list_LastLetter) {
                    $output .= "                 <TR VALIGN=\"top\">\n
                   <TD WIDTH=410 COLSPAN=3 CLASS=\"listHeader\">$list_NewLetter<A NAME=\"$list_NewLetter\"></A></TD>\n
                   <TD WIDTH=150 COLSPAN=1 CLASS=\"listHeader\" ALIGN=\"right\" VALIGN=\"bottom\"><A HREF=\"#top\"><IMG SRC=\"images/uparrow.gif\" WIDTH=10 HEIGHT=10 BORDER=0 ALT=\"[top]\"></A></TD>\n
                 </TR>\n";
                    $list_LastLetter = $list_NewLetter;
                }

                $output .= "                 <TR VALIGN=\"top\">\n";

                if (!$tbl_contact['fullname']) {
                    $output .= $listFunctions->ContactLink($body['openPopUp'], $tbl_contact['id'], stripslashes($tbl_contact['lastname']));
                } else {
                    $output .= $listFunctions->ContactLink($body['openPopUp'], $tbl_contact['id'], $tbl_contact['fullname']);
                }
                // DISPLAY PHONE NUMBER OF PRIMARY ADDRESS
                $output .= "<TD WIDTH=100 CLASS=\"listEntry\">";

                if ($tbl_contact['phone1'] || $tbl_contact['phone1']) {
                    if ($tbl_contact['phone1'] && $tbl_contact['phone2']) {
                        $output .= $tbl_contact['phone1'] . "<br />" . $tbl_contact['phone2'];
                    } else if ($tbl_contact['phone1']) {
                        $output .= $tbl_contact['phone1'];
                    } else if ($tbl_contact['phone2']) {
                        $output .= $tbl_contact['phone2'];
                    }
                }
                $output .= "&nbsp;</TD>\n
                               <TD WIDTH=160 CLASS=\"listEntry\">";
                if ($tbl_contact['line1']) {
                    $output .= $list->buildcontact($tbl_contact);
                }
                $output .= "&nbsp;</TD>\n";
                // DISPLAY E-MAILS
                $output .= "<TD WIDTH=150 CLASS=\"listEntry\">";

                $tbl_email = $list->getEmailsByContactId($tbl_contact['id']);
                if (is_array($tbl_email)) {
                    foreach ($tbl_email as $rbl_email) {
                        $output .= $list->createEmail($body['useMailScript'], $rbl_email['email']);
                    }
                }
                $output .= "&nbsp;</TD>\n </TR>\n";

            }
        }

        $output .= "                   </TABLE>
               </CENTER>
<BR>
    </TD>
  </TR>
	" . printFooter() . "
</TABLE>
</CENTER>
</BODY>
</HTML>";


        return $output;
    }

}

class listTemplateFunctions{

    /**
     * @param $isPop
     * @param $id
     * @param $name
     * @return string
     */
    function ContactLink($isPop, $id, $name){
        $output = "<TD WIDTH=150 CLASS=\"listEntry\"><B><A HREF=\"" . FILE_ADDRESS . "?id=".$id."\"";
        if ($isPop == 1) {
            $output .= "onClick=\"window.open('" . FILE_ADDRESS . "?id= ".$id."','addressWindow','width=600,height=450,scrollbars,resizable,menubar,status'); return false;\"";
        }
        $output .= "\">$name</A></B></TD>\n";
        return $output;
    }

    /**
     * @param $body
     * @param $fileUrl
     * @return string
     */
    function editLink($body, $fileUrl){
        if ($body['displayAsPopup']) {
            return "<a href=\"#\" onClick=\"window.open('" . $fileUrl['FILE_EDIT'] . "?mode=new','addressWindow','width=600,height=450,scrollbars,resizable,menubar,status'); return false;\">";
        }
        return "<a href=\"" . $fileUrl['FILE_EDIT'] . "?mode=new\">";
    }




}

