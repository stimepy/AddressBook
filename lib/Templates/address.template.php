<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-27-2022
 ****************************************************************
 *  address.Template.php
 *  Address HTML template
 *
 *************************************************************/
class addressTemplate{

    function __construct()
    {
    }

    /**
     * @param $body arrray
     * @param $lang array
     * @return string
     */
    function addressBodyStart($body, $lang) {
        $output = "
       <BODY>
           <div style='text-align: center'>
            <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
                <TR>
                    <TD CLASS=\"navMenu\"> ";

        if ($body['sessuser'] == 1) {
            $output .= "
            <a href=\"javascript:window.print()\"> " . $body['sessuser']['BTN_PRINT'] . " </a>
            <A HREF=\"" . $body['sessuser']['FILE_EDIT'] . "?id=" . $body['sessuser']['id'] . "\"> " . $body['sessuser']['BTN_EDIT'] . " </A>;
            ";
        }

        $output .= "<A HREF=\"" . $body['FILE_ADDRESS'] . "?id= " . $body['prev'] . "\"> " . $body['BTN_PREVIOUS'] . "</A>
        <A HREF=\"" . $body['FILE_ADDRESS'] . "?id=" . $body['next'] . "\"> " . $body['BTN_NEXT'] . "</A>
    
        " . $body['displayAsPopup'] . "
        
        
                </TD>
            </TR>
            <TR>
                <TD>
                    <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=570>
                        <TR VALIGN=bottom
                            <TD CLASS=\"headTitle\">
                            " . $body['$contact']['name'] . "
                            </TD>
                            <TD CLASS=\"headText\" ALIGN=right>
        " . $body['HIDDENENTRY'] . "
                                " . $body['spacer'] . "";
        if ($body['r_groups'] != -1) {
            foreach ($body['r_groups'] as $tbl_groups) {

                $output .= "                            , <A HREF=\"" . FILE_LIST . "?groupid=" . $tbl_groups['groupid'] . "\" CLASS=\"group\">" . stripslashes($tbl_groups['groupname']) . "</A>";
            }
        }
        $output .= "                        </TD>
                          </TR>
                     </TABLE>
                </TD>
            </TR>
            <TR>
                <TD CLASS=\"infoBox\">
                    <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=10 WIDTH=540>
                        <TR VALIGN=\"top\">";
        $output .= $body['tableColumnAmt'];
        $output .= "	                    <TD WIDTH=" . $body['tableColumnWidth'] . " CLASS=\"data\">";
        //$output .= outputloop($body['address']);
        $output .= $body['address'];


        $output .= "</p>
    `              </TD>
                   <td WIDTH=" . $body['tableColumnWidth'] . " CLASS=\"data\">
                    <P>\n<B>" . $lang['LBL_EMAIL'] . "</B>\n";
        $output .= outputloop($body['addreemailsses']);
        $output .= outputloop($body["otherphonecnt"]);
        $output .= outputloop($body['message']);
        $output .= "		  </TD>
            </TR>
            <TR>
                <TD COLSPAN=" . $body['tableColumnAmt2'] . "  CLASS=\"data\">
                     <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=540>
                       " . $body["birthday"];
        $output .= outputloop($body['additional']);
        $output .= outputloop($body['Websites']);

        $output .= "		   </TABLE>
                 </TD>
            </TR>";

        if ($body['note']) {
            $output .= "         <TR>
                      <TD COLSPAN=" . $body['tableColumnAmt2'] . " CLASS=\"data\">
                         <b>" . $body['LBL_NOTES'] . "</b>
                         <br />
                         " . $body['note'] . "
                      </TD>
                    </TR>";
        }

        $output .= "</TABLE>
                <br />
                </td>
                </tr>
                <tr>
                    <td CLASS=\"update\"> " . $body['lastUpdatetxt'] . " " . $body['lastupdate'] . ".</td>
                </tr>
            </TABLE>
            </CENTER>
            </BODY>
            </HTML>";

        return $output;
    }

    /**
     * @param $body
     * @param $lang
     * @return string
     */
    function returnSingleSearch($body, $lang){
        $output = "    <BODY>
        <SCRIPT LANGUAGE=\"JavaScript\">
            window.open('". $body['address'] ."',null,'width=600,height=450,scrollbars,resizable,menubar,status'); history.back();
        </SCRIPT>
        <CENTER>
        <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
            <TR>
                <TD CLASS=\"headTitle\">".$lang['SEARCH_RESULTS']."</TD>
            </TR>
            <TR>
                <TD CLASS=\"infoBox\">
                    <TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 WIDTH=500>
                        <TR VALIGN=\"top\">
                            <TD CLASS=\"data\">
                                One entry found. It will appear in a new window. If no window appears, <A HREF=\"#\" onClick=\"window.open('". $body['address'] ."',addressWindow,'width=600,height=450,scrollbars,resizable,menubar,status'); return false;\">click here</A>.
                            </TD>
                        </TR>
                    </TABLE>
                </TD>
            </TR>
        </TABLE>
        </CENTER>";

        return $output;
    }

    /**
     * @return string
     */
    function searchFooter(){
       return "       <TABLE>
            printfooter();
        </TABLE>
    </BODY>
    </HTML>";
    }

    /**
     * @param $body
     * @param $lang
     * @return string
     */
    function searchMultiResults($body, $lang){
        $output ="<BODY>
            <CENTER>
            <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
                <TR>
                    <TD CLASS=\"headTitle\">". $lang['SEARCH_LBL']."</TD>
                </TR>
                <TR>
                    <TD CLASS=\"infoBox\">
                    <TABLE BORDER=0 CELLPADDING=10 CELLSPACING=0 WIDTH=500>
                        <TR VALIGN=\"top\">
                            <TD CLASS=\"data\">
                                <B>".$lang['SEARCH_MATCH'] ." ". $body['search'] ." ". $lang['SEARCH_IN_NAME'] ."</B>";
        if(count($body['r_goto']) == 0){
            $output .="             <p> ".$lang['SEARCH_NONE'] ."</p>
                                <p><b><a href=\"" . FILE_LIST . "\">". $lang['BTN_RETURN'] ."</a></b></p>";
        }
        else{
            $output .= "             <p>".$lang['SEARCH_MULTIPLE']."</p>";
            foreach($body['r_goto'] as $contact) {
                $output .= $this->CreateAddresssLink($contact, $body);
            }
            $output .="        <p><b><a href=\"" . $body['FILE_LIST']. "\">".$lang['BTN_RETURN']."</a></b></p>";
        }
        $output .="         </TD>
                        </TR>
                    </TABLE>
                </TD>
            </TR>
        </TABLE>";

        return $output;
    }

    /**
     * @param $contact
     * @param $body
     * @return string
     */
    private function CreateAddresssLink($contact, $body){
        if ($body['displayAsPopup'] == 1) {
            $popupLink = " onClick=\"window.open('" . $body['FILE_ADDRESS'] . "?id=". $contact['id'] ."','addressWindow','width=600,height=450,scrollbars,resizable,menubar,status'); return false;\"";
        }
        return "<BR><A HREF=\"" . $body['FILE_ADDRESS'] . "?id=". $contact['id'] ."\"$popupLink>". $contact['fullname'] ."</A>\n";
    }


}
