<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-13-2022
 ****************************************************************
 *  mail.template.php
 *  mail html
 *
 *************************************************************/


class mailTemplate{
    private $sendMail;

    public function __construct()
    {
        require_once("./lib/Mail.php");
        $this->sendMail = new Correspondence();
    }

    public function sendMail($MailPost, $option){
        // If we want
        if(empty($_POST[$MailPost])){
            return;  // Nothing to do, go back;
        }
        return $this->sendMail->CreateSendPost();

    }

    public function createMailToTemplate($body, $lang, $list){
        $output ="        <BODY>
        <CENTER>
        <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
		    <TR>
		        <TD CLASS=\"navMenu\"><A HREF=\"javascript:history.go(-1)\">". $lang['BTN_RETURN'] ."</A></TD>
            </TR>
            <TR>
                <TD>
                    <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=570>
                        <TR VALIGN=\"bottom\">
                            <TD CLASS=\"headTitle\">
                                ".$body['MailToTitle'] ."
                           </TD>";

        if (empty($body['mail_to'])) {
            $output .= "					<TD CLASS=\"headText\" ALIGN=\"right\">
                <FORM NAME=\"selectGroup\" METHOD=\"get\" ACTION=\"" . $body['FILE_MAILTO'] . "\">";
            $output .= createGroupOptions($body, $lang);
            $output .= "                </FORM>
            </TD>";
        }

        $output.="        </TR>
			</TABLE>
		</TD>
	</TR>
	<TR>
		<TD CLASS=\"infoBox\">
			<br>
			<CENTER>
			<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=10 WIDTH=560>
			    <FORM NAME=\"mail_form\" METHOD=\"post\" ACTION=\"". $body['FILE_MAILTO']."\">";

        if (empty($body['mail_to'])) {
            $output .= $this->contactsEmail($body,$list,$lang);
        }
        else{
            $output .="<TR>
					<TD  width=\"200\" class=\"data\"><H4>To Email:</H4></TD>
					<TD  width=\"300\" class=\"data\">
					<INPUT TYPE=\"text\" CLASS=\"formMailbox\" VALUE=\"".$body['mail_to']."\" NAME=\"mail_to\" ><br><br>
                    </TD>
                </TR>";
        }

        $output .="<TR>
					<TD WIDTH=200 CLASS=\"data\"><H4>CC:</H4></TD>
					<TD WIDTH=300 CLASS=\"data\">
					<INPUT TYPE=\"text\" CLASS=\"formMailbox\" VALUE=\"\" NAME=\"mail_cc\" SIZE=80><br><br>
					</TD>
				</TR>
				<TR>
					<TD WIDTH=200 CLASS=\"data\"><H4>BCC:</H4></TD>
					<TD WIDTH=300 CLASS=\"data\">
					<INPUT TYPE=\"text\" CLASS=\"formMailbox\" VALUE=\"\" NAME=\"mail_bcc\" SIZE=80><br><br>
					</TD>
				</TR>

				<TR><TD WIDTH=200 CLASS=\"data\"><H4>From:</H4></TD>
					<TD WIDTH=300 CLASS=\"data\">". $body['userName']."
					<INPUT TYPE=\"hidden\"  VALUE=\"". $body['userName']."\" NAME=\"mail_from_name\" ><br><br>
				</TD></TR>
				<TR><TD WIDTH=200 CLASS=\"data\"><H4>From Email:</H4></TD>
					<TD  width=\"300\" class=\"data\">".$body['mail_from']."</TD></TR>
				<TR><TD WIDTH=200 CLASS=\"data\"><H4>".$lang['MAIL_SUBJ'].":</H4></TD>
					<TD WIDTH=300 CLASS=\"data\">
					<INPUT TYPE=\"text\" CLASS=\"formTextbox\" VALUE=\"\" NAME=\"mail_subject\" SIZE=80><br><br>
				</TD></TR>
				<TR><TD WIDTH=200 CLASS=\"data\"><H4>". $lang['MAIL_MSG'].":</H4></TD>
					<TD WIDTH=300 CLASS=\"data\">
					<TEXTAREA CLASS=\"formTextarea\" ROWS=\"20\" COLS=\"75\" NAME=\"mail_body\"></TEXTAREA><br><br>
				</TD></TR>
				<TR><TD WIDTH=200 CLASS=\"data\"></TD>
					<TD WIDTH=300 CLASS=\"data\">";

        if($body['SendMailButton']!=0) {
            $output .= " 					<INPUT TYPE=\"submit\" VALUE=\"" . $lang['BTN_SEND'] . "\" NAME=\"sendEmail\" CLASS=\"formButton\"><BR>
					<INPUT TYPE=\"hidden\"  VALUE=\"".$body['mail_from']."\" NAME=\"mail_from\" ><BR><BR>";
        }

        $output .="				</TD></TR>
				</FORM>
			</TABLE>
			</CENTER>
			<BR>
	    </TD>
	</TR>
	".printFooter()."
    </TABLE>
    </CENTER>

    </BODY>
    </HTML>";
        return $output;
    }


    private function ispopup($body,$contact){
        if($body['isPopUp'] == 1){
            $popupLink = " onClick=\"window.open('" . $body['FILE_ADDRESS'] . "?id=".$contact['id']."','addressWindow','width=600,height=450,scrollbars,resizable,location,menubar,status'); return false;\"";
        }
        $output = "<TD WIDTH=150 CLASS=\"listEntry\"><B><A HREF=\"" . $body['FILE_ADDRESS'] . "?id=".$contact['id']."\"$popupLink>";
        if(hasValueOrBlank($contact['lastname']) != ""){
            $output .= $contact['lastname'];
        }
        else{
            $output .= $contact['fullname'];
        }
        $output .= "</A></B></TD>\n";
        return $output;
    }

    function contactsEmail($body,$list,$lang){
        $output ="                 <TR VALIGN=\"top\">
                   <TD WIDTH=560 COLSPAN=4 CLASS=\"listHeader\">".$list->getgroup_name()."</TD>
                 </TR>";
        // DISPLAY IF NO ENTRIES UNDER GROUP
        if ($list->rowcount()<1) {
            $output .="                 <TR VALIGN=\"top\">
                   <TD WIDTH=560 COLSPAN=4 CLASS=\"listEntry\">No entries.</TD>
                 </TR>";
        }
        foreach($body['r_contact'] as $tbl_contact){
            // DISPLAY NAME -- links are shown either as regular link or popup window
            $output .= "<TR VALIGN=\"top\">\n".$this->ispopup($body,$tbl_contact)."
                <TD WIDTH=410 CLASS=\"listEntry\">";

            $r_email = $list->getEmailsByContactId($tbl_contact['id']);
            if($r_email != -1) {
                foreach ($r_email as $tbl_email) {
                    $output .= "<br><INPUT TYPE=\"checkbox\" NAME=\"mail_to[]\" VALUE=\"" . $tbl_email['email'] . "\" checked>
                    " . $list->createEmail($body['useMailScript'], stripslashes($tbl_email['email']));
                }
            }
            $output .="&nbsp;</TD>\n                 </TR>\n";
        }
        $output .="            <TR>
                <TD  width=\"150\" class=\"data\"></TD>
                <TD WIDTH=410 CLASS=\"data\">
                    <A HREF=\"#\" onClick\"restart();return false;\">". $lang['GROUP_NONE']."</A>
                    <br><br><br>
                </TD>
            </TR>";
        return $output;
    }

}