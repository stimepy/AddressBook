<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-24-2022
 ****************************************************************
 *
 *
 ****************************************************************/
class Install
{

    private $SqlLink;
    private $dbPre;
    private $adminPermission;

    function __construct()
    {
        global $lang, $additionalLang;
        require_once ("./Install/Install.lang.php");  // todo: Multi-language install.
        require_once ("./languages/common.php");

        $lang = mergeLanguagearrays($lang,$additionalLang);
        unset($additionalLang);
        $this->adminPermission = 1091;
    }

    function checkDB(){
        global $db_prefix,$db_hostname, $db_name, $db_username, $db_password;
        $this->dbPre = $db_prefix;
        $errorMsg = "<P><b>Installation aborted !!</b><br> config.php has incorrect or missing information !<P>";

        $errorStatus = 0;
        if (empty($db_prefix) || empty(db_hostname) || empty($db_name) || empty($db_username)) {
            $errorMsg .= "- Your config.php file has an empty variable, please check you config.<br>";
            $errorStatus = 1;
        }

// OPEN CONNECTION TO THE DATABASEs
        $this->SqlLink = new Mysql_Connect_I($db_hostname, $db_name, $db_username, $db_password);

        if ($errorStatus == 1) {
            $output = "<center><TABLE  border=\"2\"><TR><TD CLASS=\"headTitle\">";
            $output .= "<center>The Address Book - Installation Error</center></TR></TD><TR><TD CLASS=\"data\">";
            $output .= "<center><font color=\"red\">$errorMsg Then try again.</center></font>";
            $output .= "</TABLE></TD></TR></center>";
            display($output);
            exit();
        }
    }

    function CommonBodyStart($title, $charset ){
        $output = webheader($title, $charset);
        $output .= "<BODY>
    <SCRIPT LANGUAGE=\"JavaScript\">
        function saveEntry() {
            document.Options.submit();
        }
    </SCRIPT>";
        return $output;
    }

    function CommonBodyEnd($lang)
    {
        return "</TABLE>
            <table \"margin-left: auto;margin-right: auto;\">
                ". printFooter() ."
            </table>
        </body>
    </html>";
    }

    function Step1($body, $lang){
        $output ="<FORM NAME=\"Options\" ACTION=\"". $body['FILE_INSTALL']."\" METHOD=\"post\">
        <INPUT TYPE=\"hidden\" NAME=\"installStep\" VALUE=\"2\">
        <CENTER>
        <TABLE BORDER=5 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
            <TR>
                <TD CLASS=\"headTitle\">
                    ". $lang['title'] ." ". $lang['installVersion'] ."
                </TD>
            </TR>
            <TR>
                <TD CLASS=\"infoBox\">
                    <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5 WIDTH=560>
                        <TR VALIGN=\"top\">
                            <TD CLASS=\"data\">
                                ". $lang['mainInstallText'] ."
                            </TD>
                        </TR>
                        <TR VALIGN=\"top\">
                            <TD WIDTH=560 CLASS=\"listDivide\">&nbsp;</TD>
                        </TR>
           <TR VALIGN=\"top\">
              <TD WIDTH=560 CLASS=\"navmenu\">
                <NOSCRIPT>
                <!-- Will display Form Submit buttons for browsers without Javascript -->
                    <INPUT TYPE=\"submit\" VALUE=\"Next\">
                <!-- There is no delete button -->
                <!-- later make it so link versions don't appear -->
                </NOSCRIPT>
                <A HREF=\"#\" onClick=\"saveEntry(); return false;\">next</A>
              </TD>
           </TR>
        </TABLE>
    </TD>
  </TR>

</FORM>";
        return $output;
    }

    function Step2($lang){
        $output = "<CENTER>
            <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
                <TR>
                    <td CLASS=\"headTitle\">
                        ".$lang['complete']."
                    </td>
                </TR>
                <TR>
                    <TD CLASS=\"infoBox\">
                        <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5 WIDTH=560>
                            <TR VALIGN=\"top\">
                                <TD CLASS=\"data\">
	                                ". $lang['removalmessage'] ."
                                </TD>
                            </TR>
                        </TABLE>
                    </TD>
                </TR>";

            return $output;
    }

    function upgradeStep1(){
        global $lang;
        $output = " <body>
            <form name=\"upgrade\" action=\"upgrade.php\" method=\"post\">
            <INPUT TYPE=\"hidden\" NAME=\"installStep\" VALUE=\"3\">
            <CENTER>
            <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
                <TR>
                    <TD CLASS=\"headTitle\">
                        ".$lang['upgradeTitle'] ." ". $lang['installVersion'] ."
                    </TD>
                </TR>
                <TR>
                    <TD CLASS=\"infoBox\">
                        <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5 WIDTH=560>
                            <TR VALIGN=\"top\">
                                <TD CLASS=\"data\">
			                        ". $lang['upgradeMessage'] ."	                
                                </TD>
                            </TR>
                            <TR VALIGN=\"top\">
                                <TD WIDTH=560 CLASS=\"listDivide\">&nbsp;</TD>
                            </TR>
                            <TR VALIGN=\"top\">
                                <TD CLASS=\"data\">". $lang['readChangelog'] ."</TD>
                            </TR>
                            <TR VALIGN=\"top\">
                                <TD WIDTH=560 CLASS=\"navmenu\">
                                    <button type='submit'>". $lang['step1button'] ."</button>
                                </TD>
                           </TR>
                        </TABLE>
                    </TD>
               </TR>
            </TABLE>
            </FORM>";

        return $output;
    }

    function upgradeStep2(){
        global $lang;
        $output = " <body>
                    <CENTER>
                    <TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
                        <TR>
                            <TD CLASS=\"headTitle\">
                               ". $lang['upgradeComplete'] ."
                            </TD>
                        </TR>
                        <TR>
                            <TD CLASS=\"infoBox\">
                                <TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5 WIDTH=560>
                                   <TR VALIGN=\"top\">
                                      <TD CLASS=\"data\">
                                         <p>". $lang['success'] ."</p>
                                         <p>". $lang['returnToIndex'] ."</p>
                                         <p>". $lang['removalmessageUpgrade'] ."</p>
                                      </TD>
                                  </TR>
                               </TABLE>
                           </TD>
                        </TR>
                    </TABLE>
                    </CENTER>";

        return $output;
    }

    function getPost($expectedPost){
        if(!empty($_POST[$expectedPost])){
            return $_POST[$expectedPost];
        }
        return -1;
    }


    function installData(){
        global $tables, $columns;
        foreach($tables as $table){
            $this->SqlLink->FreeFormQueryNoErrorchecking("DROP TABLE IF EXISTS ". $this->dbPre . $table, 1091);
            $this->SqlLink->FreeFormQueryNoErrorchecking("CREATE TABLE " . $this->dbPre . $table ." (". $columns[$table] .") TYPE=MyISAM", $this->adminPermission);
        }

        // POPULATE SUNDRY DATABASE ENTRIES
        $this->SqlLink->FreeFormQueryNoErrorchecking("INSERT INTO ". $this->dbPre . $tables['TABLE_SCRATCHPAD'] ." VALUES('')", $this->adminPermission);
        // SET DEFAULT OPTIONS

        $this->SqlLink->FreeFormQueryNoErrorchecking("INSERT INTO " . $this->dbPre . $tables['TABLE_OPTIONS'] . " VALUES(21,1,0,1,0,140,140,1,1,0,'<P>Please log in to access the Address Book.','<B>welcome to the Address Book!</B>','',0,0,1,'english','',0)", $this->adminPermission);
        // CREATE TEMPORARY USERS
        $this->SqlLink->FreeFormQueryNoErrorchecking("INSERT INTO " . $this->dbPre . $tables['TABLE_USERS'] . " (id, username, usertype, password, email, confirm_hash, is_confirmed) VALUES (1, 'admin', 'admin', MD5( 'admin' ), '', '', 1),
        (2, 'guest', 'user', MD5( 'guest' ), '', '', 1)", $this->adminPermission);

        $this->langaugedataInstall($tables['TABLE_LANGUAGE']);

        $_SESSION['username'] = 'admin';
        $_SESSION['usertype'] = 'admin';
        $_SESSION['abspath'] = dirname($_SERVER['SCRIPT_FILENAME']);
    }

    function updateData(){
        global $tables, $columns;
        // install Langauge table
        $this->SqlLink->FreeFormQueryNoErrorchecking("DROP TABLE IF EXISTS ". $this->dbPre . $tables['TABLE_LANGUAGE'], $this->adminPermission);
        $this->SqlLink->FreeFormQueryNoErrorchecking("CREATE TABLE " . $this->dbPre . $tables['TABLE_LANGUAGE'] ." (". $columns[$tables['TABLE_LANGUAGE']] .") TYPE=MyISAM", $this->adminPermission);

        $this->langaugedataInstall($tables['TABLE_LANGUAGE']);
    }


    private function langaugedataInstall($table){
        $inserts = "('dutch','Nederlands','0'),";
        $inserts .= "('english','English','1'),";
        $inserts .= "('french','Français','0'),";
        $inserts .= "('german','Deutsch','0'),";
        $inserts .= "('greek','Greek','0'),";
        $inserts .= "('hungarian','Magyar','0'),";
        $inserts .= "('italian','Italian','0'),";
        $inserts .= "('swedish','Swedish','0')";

        $this->SqlLink->iFreeFormQueryNoErrorchecking("insert into " . $this->dbPre . $table ." ('filename', 'fileLanguage', 'defaultLang') values ".$inserts, $this->adminPermission);
    }

}