<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-13-2022
 ****************************************************************
 *  class-contactlist.php
 *  functions used along with contact.
 *
 *************************************************************/

// Original code by hannelore
 
class ContactList {
	
	private $group_id;
    private $group_name;
    private $current_letter;
    private $max_entries;
    private $current_page;
    private $total_pages;
    // private $sql;
    private $title;
    private $nav_menu;
	private $myRowCount;


	function __construct($options) {

		// DEPENDENT VARIABLES -- Values for these variables are passed to the object after ContactList is created
		// If no values are provided, then it uses some defaults
		$this->group_id = 0;                       // defaults to 0 upon creation of object
		$this->current_page = 1;                   // defaults to first page
		$this->current_letter = $options->getdefaultLetter();	// defaults to value set in options
		$this->max_entries = $options->getlimitEntries(); 		// defaults to value set in options; 0=no maximum (display all on page 1)

		// RESULTANT VARIABLES -- Values for these variables start out blank and will be filled in by this object's methods
		$this->group_name = "";                    // determined in $this->group_name()
		$this->total_pages = 1;                    // total # of pages, determined in $this->retrieve()
		$this->select = "";                           // determined in $this->retrieve(), useful for debugging purposes
        $this->tables = "";
        $this->where = "";
		$this->title = "";                         // determined in $this->title()
		$this->nav_menu = "";                      // determined in $this->create_nav()
	}
	
	function group_name() {
		global $globalSqlLink;
		global $lang;
		
		// OBTAIN NAME OF GROUP IN DISPLAYED LIST
		// Force $this->group_id to an integer equal to 0 or greater
		$this->group_id = intval($this->group_id);
		if ($this->group_id <= 0) $this->group_id = 0;
		
		// group_id = 0 --> "All Entries"
		if ($this->group_id == 0) $this->group_name = $lang['GROUP_ALL_LABEL'];
		// group_id = 1 --> "Ungrouped Entries"
		elseif ($this->group_id == 1) $this->group_name = $lang['GROUP_UNGROUPED_LABEL'];
		// group_id = 2 --> "Hidden Entries"
		elseif ($this->group_id == 2) {
			// Admin check
			if ($_SESSION['usertype'] != "admin") {
				die("URL tampering detected.");
				exit();
			}
			$this->group_name = $lang['GROUP_HIDDEN_LABEL']; // "Hidden Entries"
		}
		// group_id >= 3 --> Check the database for user-defined group
		else {
		    $globalSqlLink->SelectQuery( '*', TABLE_GROUPLIST, 'groupid='.$this->group_id, NULL);
            $tbl_grouplist = $globalSqlLink->FetchQueryResult();

//                mysql_fetch_array(mysql_query("SELECT * FROM " . TABLE_GROUPLIST . " AS grouplist WHERE groupid=$this->group_id", $db_link));
			$this->group_name = $tbl_grouplist['groupname'];
			// Reassign to "All Entries" if given a groupid that doesn't exist
			if ($this->group_name == "") {
				$this->group_id = 0;
				$this->group_name = "All Entries";
			}
		}
		// Return value
		return $this->group_name;
	}



	function title() {
		$this->title = $this->group_name;
		
		if (!empty($this->current_letter)) $this->title .= " - $this->current_letter";
		if ($this->total_pages > 1) $this->title .= " (page $this->current_page of $this->total_pages)";
		
		return $this->title;
	}


	function retrieve() {
		global $globalSqlLink;

        $sql_limit = '';
		
	 	// The following needs to be set to retrieve correctly
	 	// $this->group_id
	 	// $this->current_letter
	 	// $this->max_entries
	 	// $this->current_page
	 	
	 	// CREATE INITIAL SQL FRAGMENT
		$this->select = "contact.id, CONCAT(lastname,', ',firstname) AS fullname, lastname, firstname, refid, line1, line2, city, state, zip, phone1, phone2, country, whoAdded";
		$this->tables = TABLE_CONTACT . " AS contact";

	    // CREATE SQL FRAGMENTS TO FILTER BY GROUP
		// group_id = 0 --> "All Entries"  // group_id = 2 --> "Hidden Entries"
		if ($this->group_id == 0 || $this->group_id == 2) {
            $this->tables = $this->tables." LEFT JOIN " . TABLE_ADDRESS . " AS address ON contact.id=address.id AND contact.primaryAddress=address.refid";

            // group_id = 0 --> "All Entries"
            if($this->group_id == 0) {
                $this->where = "contact.hidden != 1";
            }
            // group_id = 2 --> "Hidden Entries"
            else{
                $this->where = "contact.hidden = 1";
            }
    	}
		// group_id = 1 --> "Ungrouped Entries"
	    elseif ($this->group_id == 1) {
            $this->tables = $this->tables." LEFT JOIN " . TABLE_ADDRESS . " AS address ON contact.id=address.id AND contact.primaryAddress=address.refid 
                            LEFT JOIN " . TABLE_GROUPS . " AS groups ON groups.id=contact.id";
            $this->where = "groups.id IS NULL AND contact.hidden != 1";
	    }
	    // group_id >= 3 --> Specified user-defined group
	    else {
            $this->tables  = $this->tables.", " . TABLE_GROUPS . " AS groups
						LEFT JOIN ". TABLE_ADDRESS ." AS address ON contact.id=address.id AND contact.primaryAddress=address.refid";
			$this->where =  "contact.id=groups.id AND groups.groupid=$this->group_id AND contact.hidden != 1";
	
	    }
		// CREATE SQL FRAGMENTS TO FILTER BY LETTER
		switch ($this->current_letter) {
			case "":	// No letter filter
				break;
			case "1":	// If selecting non-alphabetical characters
                $this->where = $this->where." AND lastname REGEXP  '^[^[:alpha:]]'";
				break;	
			default:	// If a letter is set
                $this->where = $this->where." AND lastname LIKE '$this->current_letter%'";
				break;
		}

		// CREATE SQL FRAGMENTS TO LIMIT NUMBER OF ENTRIES PER PAGE
		if ($this->max_entries > 0) { //if this option is set, limit the number of entries shown per page
			// Count number of rows (this uses group and letter sql fragments, determined previously)

			$globalSqlLink->SelectQuery( 'COUNT(*)', $this->tables,  $this->where, NULL);
            $count = $globalSqlLink->FetchQueryResult();

                //mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM " . TABLE_CONTACT . " AS contact" . $sql_group . $sql_letter, $db_link));
			$this->total_pages = intval(ceil((float)$count[0]/$this->max_entries)); //divide the total entries by the limit per page. Round up to an integer
		
			// Users like to start counting from 1 in stead of 0
			$lowerLimit = $this->current_page - 1; //deduct 1 from the result page number in the URL, use this to calculate the lower limit of the range
			$lowerLimit = $lowerLimit*$this->max_entries; //lower limit of the range
			$sql_limit = " LIMIT $lowerLimit, $this->max_entries";
		}

		// ASSEMBLE THE SQL QUERY
		//$this->sql .= $sql_group . $sql_letter . " ORDER BY fullname" . $sql_limit;
		
		// EXECUTE THE SQL QUERY
        $globalSqlLink->SelectQuery($this->select, $this->tables, $this->where,  " ORDER BY fullname" . $sql_limit );
		$r_contact = $globalSqlLink->FetchQueryResult();

		$this->myRowCount = $globalSqlLink->GetRowCount();
           //  = mysql_query($this->sql, $db_link)
			//or die(reportSQLError($this->sql));
			
		// RETURN RESULTS OF QUERY
		return $r_contact;
	}
	
	
	function nav_abc($link) {
		$abc = array('A' => 'A',
					'B' => 'B',
					'C' => 'C',
					'D' => 'D',
					'E' => 'E',
					'F' => 'F',
					'G' => 'G',
					'H' => 'H',
					'I' => 'I',
					'J' => 'J',
					'K' => 'K',
					'L' => 'L',
					'M' => 'M',
					'N' => 'N',
					'O' => 'O',
					'P' => 'P',
					'Q' => 'Q',
					'R' => 'R',
					'S' => 'S',
					'T' => 'T',
					'U' => 'U',
					'V' => 'V',
					'W' => 'W',
					'X' => 'X',
					'Y' => 'Y',
					'Z' => 'Z',
					'1' => '[0-9]',
					'' => '[all]');
		foreach ($abc as $key => $letter) {
			$this->nav_menu .= "<a href='$link$key'>$letter</a>\n";
		}
	}
	
	function nav_pages($link) {
		if ($this->total_pages > 1) { //check whether there are multiple result pages for the request
			$this->nav_menu .= "Pages: ";
			for ($i=1; $i <= $this->total_pages; $i++) { //create an array of links to the result pages
				if ($this->current_page == $i) { //indicate the current page in the navigation 
					$this->nav_menu .= "<b>[$i]</b>\n";	
				}
				else { //create links to all other result pages
					$this->nav_menu .= "<a href='$link$this->current_letter&amp;page=$i'>$i</a> \n";
				}
			}
			$this->nav_menu .= "<a href='$link$this->current_letter&amp;limit=0'>[all]</a>\n";
		}
	}
	
	function create_nav() {
		/*
		Here's the logic behind the navigation links:
			If we have...			Then the links will have....
			group	letter	page	
		1.	x						(always occurs) - use letter links w/ #
		2.	x		x				use letter links only (no page links)
		3.	x		x		x		use page links within current letter, and letter links to page 1
		4.	x				x		use page links only (no letter)
		*/
		// Base link
		$link = $_SERVER['PHP_SELF'] . "?groupid=$this->group_id";
		
		// Case 2. Group and letter (no page)
		if ((!empty($this->current_letter)) && ($this->total_pages <= 1)) { 
			$link .= "&amp;letter=";
			$this->nav_abc($link);
		} 
		// Case 3. Group, letter, and page.
		elseif ((!empty($this->current_letter)) && ($this->total_pages > 1)) {
			$link .= "&amp;limit=$this->max_entries&amp;letter=";
			$this->nav_abc($link);
			$this->nav_pages($link);
		}
		// Case 4. Group and page (no letter)
		elseif ((empty($this->current_letter)) && ($this->total_pages > 1)) {
			$link .= "&amp;limit=$this->max_entries";
			$this->nav_pages($link);
		}
		// Case 1. (default) Group only.
		else { 
			//$link .= "#";
			$link = "#";
			$this->nav_abc($link);
		}
		
		return $this->nav_menu;
	}

	function rowcount(){
	    return $this->myRowCount;
    }

    function getEmailsByContactId( $contactId){
        global $globalSqlLink;
        $globalSqlLink->SelectQuery('id, email, type', TABLE_EMAIL, "id=" . $contactId, NULL);
        $tbl_email = $globalSqlLink->FetchQueryResult();
        return $tbl_email;
    }
    function getgroup_id(){return $this->group_id;}
    function setgroup_id($value){ $this->group_id = $value;}

    function getgroup_name(){return $this->group_name;}
    function setgroup_name($value){ $this->group_name = $value;}

    function getcurrent_letter(){return $this->currentletter;}
    function setcurrent_letter($value){ $this->current_letter = $value;}

    function getmax_entries(){return $this->max_entries;}
    function setmax_entries($value){ $this->max_entries = $value;}

    function getcurrent_page(){return $this->current_page;}
    function setcurrent_page($value){ $this->current_page = $value;}

    function gettotal_pages(){return $this->total_pages;}
    function settotal_pages($value){ $this->total_pages = $value;}

    function gettitle(){return $this->title;}
    function settitle($value){ $this->title = $value;}

    function getnav_menu(){return $this->nav_menu;}
    function setnav_menu($value){ $this->nav_menu = $value;}

    function createEmail($useMailScript, $email){
        if ($useMailScript == 1) {
            return "<br/><A HREF=\"" . FILE_MAILTO . "?to=" . $email . "\">" . $email . "</A>";
        }
        return "<br/><A HREF=\"mailto:" . $email . "\">" . $email . "</A>";;

    }

    //deprecated going to list.... ?
    function buildcontact($tbl_contact){
        global $country;
        $output = $tbl_contact['line1']."<br />";
        if ($tbl_contact['line2']) {
            $output .= $tbl_contact['line2']."<br />";
        }
        if ($tbl_contact['city'] || $tbl_contact['state']) {
            if($tbl_contact['city'] && $tbl_contact['state']) {
                $output .= $tbl_contact['city'] . ", " . $tbl_contact['state'];
            }
            else if($tbl_contact['city']){
                $output .=  $tbl_contact['city'];
            }
            else if($tbl_contact['state']){
                $output .= $tbl_contact['state'];
            }
        }
        if ($tbl_contact['zip']) {
            $output .= " ".$tbl_contact['zip'];
        }
        if ($tbl_contact['country']) {
            $output .= "\n<br />" . $country[$tbl_contact['country']];
        }
        return $output;

    }


}
// END ContactList
?>