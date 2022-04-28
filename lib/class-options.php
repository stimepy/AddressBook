<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-13-2022
 ****************************************************************
 *  lib/class-options.php
 *  Object: retrieve and set global or user options
 *
 *************************************************************/

 
class Options {

	// DECLARE OPTION VARIABLES
	private $bdayInterval;
	private $bdayDisplay;
	private $displayAsPopup;
	private $useMailScript;
	private $picAlwaysDisplay;
	private $picWidth;
	private $picHeight;
	private $picDupeMode;
	private $picAllowUpload;
	private $modifyTime; // not currently in use; reserved for future use
	private $msgLogin;
	private $msgWelcome;
	private $countryDefault;
	private $allowUserReg;
	private $eMailAdmin;
	private $requireLogin;
	private $language;
	private $defaultLetter; // test
	private $limitEntries; // test
	
	// DECLARE OTHER VARIABLES
	private $global_options;
	private $user_options;
	private $message;

	
	// CONSTRUCTOR FUNCTION
	function __construct() {
		$this->get();
	}
	
	function get() {
		// This function retrieves global options first. Then, it retrieves user options
		// if a user name is available, which will overwrite certain global options.
		$this->set_global();
		if ((isset($_SESSION['username'])) && ($_SESSION['username'] != '@auth_off')) {
			$this->set_user();
		}
	}



	
	function set_global() {
		// This function restores all options to the administrator-specified global settings.
		// Call this function when you need to ignore the user-specified settings.
		// Note: If you do not call this function, you can still obtain global settings
		// directly using the $this->global_options variable.
		global $globalSqlLink;

		$globalSqlLink->SelectQuery('*',TABLE_OPTIONS, '',   " LIMIT 1" );
		$this->global_options = $globalSqlLink->FetchQueryResult();
		 // =mysql_fetch_array(mysql_query("SELECT * FROM " . TABLE_OPTIONS . " LIMIT 1", $db_link))
		 //		or die(reportScriptError("Unable to retrieve global options."));

		$this->bdayInterval     = $this->global_options[0]['bdayInterval'];
		$this->bdayDisplay      = $this->global_options[0]['bdayDisplay'];
		$this->displayAsPopup   = $this->global_options[0]['displayAsPopup'];
		$this->useMailScript    = $this->global_options[0]['useMailScript'];
		$this->picAlwaysDisplay = $this->global_options[0]['picAlwaysDisplay'];
		$this->picWidth         = $this->global_options[0]['picWidth'];
		$this->picHeight        = $this->global_options[0]['picHeight'];
		$this->picDupeMode      = $this->global_options[0]['picDupeMode'];
		$this->picAllowUpload   = $this->global_options[0]['picAllowUpload'];
		$this->modifyTime       = $this->global_options[0]['modifyTime'];
		$this->msgLogin         = stripslashes( $this->global_options[0]['msgLogin'] );
		$this->msgWelcome       = stripslashes( $this->global_options[0]['msgWelcome'] );
		$this->countryDefault   = $this->global_options[0]['countryDefault'];
		$this->allowUserReg     = $this->global_options[0]['allowUserReg'];
		$this->eMailAdmin       = $this->global_options[0]['eMailAdmin'];
		$this->requireLogin     = $this->global_options[0]['requireLogin'];
		$this->language         = $this->load_lang($this->global_options[0]['language']);
		$this->defaultLetter    = $this->global_options[0]['defaultLetter'];
		$this->limitEntries     = $this->global_options[0]['limitEntries'];
	}
	
	function set_user() {
		// This function overrides admin-specified options with user options.
		// Call this function if you need to restore the user settings after resetting
		// to global settings.
		// Note: If you do not call this function, you can still obtain the user settings
		// directly using the $this->user_options variable.
		global $globalSqlLink;

		$globalSqlLink->SelectQuery('*', TABLE_USERS, "username='" . $_SESSION['username'] . "' LIMIT 1", '');
		$this->user_options =$globalSqlLink->FetchQueryResult();
		//$this->user_options = mysql_fetch_array(mysql_query("SELECT * FROM " . TABLE_USERS . " WHERE username='" . $_SESSION['username'] . "' LIMIT 1", $db_link))
			//	or die(reportScriptError("Unable to retrieve user options."));

		if (!is_null($this->user_options['bdayInterval']))   $this->bdayInterval = $this->user_options['bdayInterval'];
		if (!is_null($this->user_options['bdayDisplay']))    $this->bdayDisplay = $this->user_options['bdayDisplay'];
		if (!is_null($this->user_options['displayAsPopup'])) $this->displayAsPopup = $this->user_options['displayAsPopup'];
		if (!is_null($this->user_options['useMailScript']))  $this->useMailScript = $this->user_options['useMailScript'];
		if (!is_null($this->user_options['language']))       $this->language = $this->load_lang($this->user_options['language']);
		if (!is_null($this->user_options['defaultLetter']))  $this->defaultLetter = $this->user_options['defaultLetter'];
		if (!is_null($this->user_options['limitEntries']))   $this->limitEntries = $this->user_options['limitEntries'];
	}
	
	function save_global() {
		// This function saves global settings to the database, in the options table.
		// It assumes that the options have already been placed in the $_POST superglobal.
		global $globalSqlLink;
		global $lang;

		// CHECK NUMERICAL INPUT
		// This is DIFFERENT from the previous implemenation (TAB 1.03 and earlier)
		// where empty or faulty information resulted in resetting the value to a
		// hard-coded default value. Here, it will check if the $_POST value is valid,
		// and if so, it will overwrite the existing setting. Otherwise the original
		// value (whatever it is) is retained.
		if (($_POST['bdayInterval'] > 0) && is_numeric($_POST['bdayInterval']))     $this->bdayInterval = $_POST['bdayInterval'];
		if (($_POST['picWidth'] > 0) && is_numeric($_POST['picWidth']))             $this->picWidth = $_POST['picWidth'];
		if (($_POST['picHeight'] > 0) && is_numeric($_POST['picHeight']))           $this->picHeight = $_POST['picHeight'];
		if (($_POST['picDupeMode'] == 1) || ($_POST['picDupeMode'] == 2) || ($_POST['picDupeMode'] == 3))  $this->picDupeMode = $_POST['picDupeMode'];
		if (($_POST['countryDefault']))                                             $this->countryDefault = $_POST['countryDefault'];
		if (($_POST['limitEntries'] >= 0) && is_numeric($_POST['limitEntries']))    $this->limitEntries = $_POST['limitEntries'];
		
		if ($_POST['language']) $this->language = $_POST['language'];	// not numerical, but the same principle applies
		$this->defaultLetter = (empty($_POST['defaultLetter'])) ? "" : $_POST['defaultLetter']; // if no value is sent, then turn defaultLetter off (note: off must be empty string, NOT 0 value)

		// CLEAN UP STRING INPUT
		// These are allowed to be blank. We will take these "as is" -- no checking is done.
		$this->msgLogin   = addslashes(strip_tags(trim($_POST['msgLogin']),'<a><b><i><u><p><br>'));
		$this->msgWelcome = addslashes(strip_tags(trim($_POST['msgWelcome']),'<a><b><i><u><p><br>'));
		
		// CHECKBOXES
		// If the variable does not exist in $_POST, that means the checkbox is turned off!
		// Give it a value of 0 so we know what to enter into the database.
		// Everything else results in 1 (which should be the contents of the $_POST variable anyway, but let's be sure
		$this->bdayDisplay      = (empty($_POST['bdayDisplay'])) ? 0 : 1;
		$this->displayAsPopup   = (empty($_POST['displayAsPopup'])) ? 0 : 1;
		$this->useMailScript    = (empty($_POST['useMailScript'])) ? 0 : 1;
		$this->picAlwaysDisplay = (empty($_POST['picAlwaysDisplay'])) ? 0 : 1;
		$this->picAllowUpload   = (empty($_POST['picAllowUpload'])) ? 0 : 1;
		$this->allowUserReg     = (empty($_POST['allowUserReg'])) ? 0 : 1;
		$this->eMailAdmin       = (empty($_POST['eMailAdmin'])) ? 0 : 1;
		$this->requireLogin     = (empty($_POST['requireLogin'])) ? 0 : 1;

		// CREATES THE QUERY AND UPDATES THE OPTIONS TABLE
		//$sql = "UPDATE " . TABLE_OPTIONS . " SET
		$updates['bdayInterval']     = $this->bdayInterval;
		$updates['bdayDisplay']       = $this->bdayDisplay;
		$updates['displayAsPopup']    = $this->displayAsPopup;
		$updates['useMailScript']     = $this->useMailScript;
		$updates['picAlwaysDisplay']  = $this->picAlwaysDisplay;
		$updates['picWidth']          = $this->picWidth;
		$updates['picHeight']        = $this->picHeight;
		$updates['picDupeMode']       = $this->picDupeMode;
		$updates['picAllowUpload']    = $this->picAllowUpload;
		$updates['modifyTime']        = $this->modifyTime;
		$updates['msgLogin']          = '$this->msgLogin';
		$updates['msgWelcome']        = '$this->msgWelcome';
		$updates['countryDefault']    = '$this->countryDefault';
		$updates['allowUserReg']     = $this->allowUserReg;
		$updates['requireLogin']      = $this->requireLogin;
		$updates['eMailAdmin']        = $this->eMailAdmin;
		$updates['language']          = '$this->language';
		$updates['defaultLetter']     = '$this->defaultLetter';
		$updates['limitEntries']      = $this->limitEntries;

		$globalSqlLink->UpdateQuery( $updates , TABLE_OPTIONS, '');
		if($globalSqlLink->GetRowCount() == 0){
			die($lang['ERR_OPTIONS_NO_SAVE']);
		}
		//mysql_query($sql, $db_link)
			//or die(reportSQLError($lang['ERR_OPTIONS_NO_SAVE']));

		$this->get();
		$this->message = $lang['OPT_SAVED'];

		return true;
	}
	
	
	function save_user() {
		// This function saves user settings to the database, in the users table.
		// This is largely similar in function to save_global() except that there are much fewer
		// options to deal with. It may be better to condense the two functions into 
		// one function so as to avoid repetition of code but we can worry about that later.
		global $globalSqlLink;
		global $lang;
		
		// CHECK INPUT
		// Condensed version of events from save_global().
		if (($_POST['bdayInterval'] > 0) && is_numeric($_POST['bdayInterval']))     $this->bdayInterval = $_POST['bdayInterval'];
		if (($_POST['limitEntries'] >= 0) && is_numeric($_POST['limitEntries']))    $this->limitEntries = $_POST['limitEntries'];
		if ($_POST['language']) $this->language = $_POST['language'];
		$this->defaultLetter    = (empty($_POST['defaultLetter'])) ? "" : $_POST['defaultLetter'];
		$this->bdayDisplay      = (empty($_POST['bdayDisplay'])) ? 0 : 1;
		$this->displayAsPopup   = (empty($_POST['displayAsPopup'])) ? 0 : 1;
		$this->useMailScript    = (empty($_POST['useMailScript'])) ? 0 : 1;

		// CREATES THE QUERY AND UPDATES THE OPTIONS TABLE
		//$sql = "UPDATE " . TABLE_USERS . " SET
		$updates['bdayInterval']      = $this->bdayInterval;
		$updates['bdayDisplay']       = $this->bdayDisplay;
		$updates['displayAsPopup']    = $this->displayAsPopup;
		$updates['useMailScript']     = $this->useMailScript;
		$updates['language']          = $this->language;
		$updates['defaultLetter']     = $this->defaultLetter;
		$updates['limitEntries']      = $this->limitEntries;

		$globalSqlLink->UpdateQuery($updates, TABLE_USERS,  "username='" . $_SESSION['username']."'");
		if($globalSqlLink->GetRowCount() == 0){
			die($lang['ERR_OPTIONS_NO_SAVE']);
		}
		//mysql_query($sql, $db_link)
		//	or die(reportSQLError($lang['ERR_OPTIONS_NO_SAVE']));
		
		$this->get();
		$this->message = $lang['OPT_SAVED_USER'];

		return true;
	}
	
	function reset_user() {
		// This function is designed to clear the user's settings and have all option variables
		// set to NULL in the database. NULL means neither yes or no, and will force the
		// script to look to the global options table for information.
		global $globalSqlLink;
		global $lang;

		// QUERY
		//$sql = "UPDATE " . TABLE_USERS . " SET
		$updates['bdayInterval']      = 'NULL';
		$updates['bdayDisplay']       = 'NULL';
		$updates['displayAsPopup']    = 'NULL';
		$updates['useMailScript']     = 'NULL';
		$updates['language']          = 'NULL';
		$updates['defaultLetter']     = 'NULL';
		$updates['limitEntries']      = 'NULL';
		//		WHERE username='" . $_SESSION['username'] . "'";
		$globalSqlLink->UpdateQuery($updates, TABLE_USERS,  "username='" . $_SESSION['username']."'");
		if($globalSqlLink->GetRowCount() == 0){
			die($lang['ERR_OPTIONS_NO_SAVE']);
		}
		// mysql_query($sql, $db_link)
		//	or die(reportSQLError($lang['ERR_OPTIONS_NO_SAVE']));
		
		// RESET MEMBER VARIABLES
		$this->set_global();	
		
		$this->message = $lang['OPT_RESET_USER'];
		return true;
	}
		
	function load_lang($file) {
		global $additionalLang, $lang, $fileUrl, $paths;
        require_once ("./languages/common.php");

		$fullpath = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $paths['PATH_LANGUAGES'] . $file . '.php';
		// This function takes the value returned by the 'language' column in global or user options table,
		// and checks to make sure that the file exists in the /language directory. If it exists, it loads
		// the language into memory. If it does not exist, it attempts to loads 'english' (the default language).

		if (file_exists($fullpath)) {
			require_once($fullpath);
            $lang = mergeLanguagearrays($lang,$additionalLang);
            unset($additionalLang);
			return $file;
		} else {
			require_once(dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $paths['PATH_LANGUAGES'] . 'english.php');
			$this->message = $lang['OPT_LANGUAGE_MISSING'];
            $lang = mergeLanguagearrays($lang,$additionalLang);
            unset($additionalLang);
			return 'english';
		}
	}

	function getMessage(){
		return $this->message;
	}
	function getWelcomeMessage(){
		return $this->msgWelcome;
	}
	function bdayInterval(){
		return $this->bdayInterval;
	}
	function getdisplayAsPopup(){
		return $this->displayAsPopup;
	}
	function getuseMailScript(){
		return $this->useMailScript;
	}
	function getpicAlwaysDisplay(){
		return $this->picAlwaysDisplay;
	}
	function getpicWidth(){
		return $this->picWidth;
	}
	function getpicHeight(){
		return $this->picHeight;
	}
	function getpicDupeMode(){
		return $this->picDupeMode;
	}
	function getpicAllowUpload(){
		return $this->picAllowUpload;
	}
	function getmodifyTime(){
		return $this->modifyTime;
	}
	function getmsgLogin(){
		return $this->msgLogin;
	}
	function getcountryDefault(){
		return $this->countryDefault;
	}
	function getallowUserReg(){
		return $this->allowUserReg;
	}
	function geteMailAdmin(){
		return $this->eMailAdmin;
	}
	function getrequireLogin(){
		return $this->requireLogin;
	}
	function getlanguage(){
		return $this->language;
	}
	function getdefaultLetter(){
		return $this->defaultLetter;
	}
	function getlimitEntries(){
		return $this->limitEntries;
	}
	function getglobal_options(){
		return $this->global_options;
	}
	function getuser_options(){
		return $this->user_options;
	}
	function getbdayDisplay(){
		return $this->bdayDisplay;
	}

    function setupAllGroups(&$body,$list){
        GLOBAL $globalSqlLink, $lang;

        $body['G_0'] = array( 'groupid' => 0, 'groupname' => $lang['GROUP_ALL_SELECT'] );
        $body['G_1'] = array( 'groupid' => 1, 'groupname' => $lang['GROUP_UNGROUPED_SELECT'] );

        if ($_SESSION['usertype'] == "admin") {
            $body['G_2'] = array( 'groupid' => 2, 'groupname' => $lang['GROUP_HIDDEN_SELECT'] );
            $x=3;
        }
        else {
            $x=2;
        }
        $where = "groupid >= 3";
        $body['G_selected'] = $list->getgroup_id();

        $globalSqlLink->SelectQuery( 'groupid, groupname',  TABLE_GROUPLIST ,  $where,  'order by groupname', NULL);
        $r_grouplist = $globalSqlLink->FetchQueryResult();
        if($r_grouplist != -1) {
            foreach ($r_grouplist as $rbl_grouplist) {
                $body['G_' . $x] = array('groupid' => $rbl_grouplist['groupid'], 'groupname' => $rbl_grouplist['groupname']);
                $x++;
            }
        }
        $body['G_count'] = $x;
    }

    private function AlhpabetArray(){
        Return array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
    }

    function createLanguage($language){
        $output ="";
        foreach ($language as $langpick){
            $output .="<option value=\"" . $langpick['filename'] . "\"". (($langpick['defaultLang'] == 1 ) ? " selected" : "") .">". $langpick['fileLanguage'] ."</option>\n";
        }
        return $output;
    }

    function alphabetSoup($defaultLetter){
        $abc=$this->AlhpabetArray();
        $checked = "";
        if(!isset($defaultLetter)){
            $checked = "selected";
        }
        $output = "<OPTION VALUE=\"0\" ". $checked .">(off)</OPTION>";
        foreach ($abc as $letter){
            $checked = "";
            if ($letter == $defaultLetter) {
                $checked = "SELECTED";
            }
            $output .= "<OPTION VALUE=\"$letter\" ".$checked.">$letter</OPTION>\n";
        }
        return $output;
    }

}

