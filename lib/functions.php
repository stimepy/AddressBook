<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-24-2022
 ****************************************************************
 *  functions.php
 *  Defines functions to be used within other scripts.
 *
 *************************************************************/


# Following are registration/mail functions formerly found in /lib/userfunctions
## ########////////////*********            programming note - all values for feedback eventually need to be names of $lang[] array NAMES
// USED @ confirm page, accessed via confirmation e-mail
//todo: move back to users
function user_confirm($hash,$email) { 
	global $feedback, $hidden_hash_var, $globalSqlLink;
	//verify that they didn't tamper with the email address - David temporarily put != where = was due to error troubleshooting.
	$new_hash=md5($email.$hidden_hash_var);
	if ($new_hash && ($new_hash==$hash)) {
		//find this record in the db
        $globalSqlLink->SelectQuery('*', TABLE_USERS, "confirm_hash LIKE '$hash'", NULL );
        $result = $globalSqlLink->FetchQueryResult();
		//$sql="SELECT * FROM ".TABLE_USERS." WHERE confirm_hash LIKE '$hash'";
		//$result=mysqli_query($db_link,$sql);
		if ($globalSqlLink->GetRowCount() < 1) {
			$feedback = "ERR_USER_HASH_NOT_FOUND";
			return false;
		} else {
			//confirm the email and set account to active
			$feedback ="REG_CONFIRMED";
			//$sql="UPDATE ".TABLE_USERS."  SET email='$email',is_confirmed='1' WHERE confirm_hash='$hash'";
            $select['email'] = $email;
            $select['is_confirmed']=1;
            $globalSqlLink->UpdateQuery($select, TABLE_USERS, "confirm_hash=".$hash );
			//$result=mysql_query($sql, $db_link);
			return true;
		}
	} else {
		$feedback = "ERR_USER_HASH_INVALID";
		return false;
	}
}

//Will double check may need to just move into a registration class or something
// will need to make more robust
function account_pwvalid($pw) {
	global $feedback;
	if (strlen($pw) < 4) {
		$feedback .= "ERR_PSWD_SORT";
		return false;
	}
	return true;
}

//Will double check may need to just move into a registration class or something
function account_namevalid($name) {
	global $feedback;
	// no spaces
	if (strrpos($name,' ') > 0) {
		$feedback .= "ERR_LOGIN_SPACE";
		return false;
	}
	// must have at least one character
	if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") == 0) {
		$feedback .= "ERR_ALPHA";
		return false;
	}
	// must contain all legal characters
	if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_")
		!= strlen($name)) {
		$feedback .= "ERR_CHAR_ILLEGAL";
		return false;
	}
	// min and max length
	if (strlen($name) < 1) {
		$feedback .= "ERR_NAME_SHORT";
		return false;
	}
	if (strlen($name) > 15) {
		$feedback .= "ERR_NAME_LONG";
		return false;
	}
	// illegal names
	if (preg_match("/^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)/i"
		. "|(uucp)|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)"
		. "|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download))$",$name)) {
		$feedback .= "ERR_RSRVD";
		return 0;
	}
	if (preg_match("/^(anoncvs_)/i",$name)) {
		$feedback .= "ERR_RSRVD_CVS";
		return false;
	}

	return true;
}


// Email validation is super hard...  may need to drop this.  we shall see.
//Will double check may need to just move into a registration class or something?  or expand usage
function validate_email ($address) {
	return (preg_match('/^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'. '@'. '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$/', $address));
}
## end registration/mail functions


// Just an array merge to merge in common properties so they don't need to always be updated.
function mergeLanguagearrays($array1, $array2){
    return array_merge($array1, $array2);
}


//
// CHECK ID - check_id();
// Checks to see if an variable 'id' has been passed to the document, via GET or POST.
// In addition, it checks to see if the 'id' corresponds to an entry already in the database, or else returns an error.
//todo: move back to users
function check_id() {
	global $globalSqlLink;
	global $lang;

	// Get 'id' if passed through GET/POST
	$id = (integer) $_REQUEST['id'];
	// Check if anything was given for ID
	if (empty($id)) {
		die('<b>invalid entry ID</b>');
	}
	// Return id
	return $id;

}
// end


// 
// IS ALPHANUMERIC - isAlphaNumeric();
// Checks a string to see if it contains letters a-z, A-z, numbers 0-9, or the
// underscore _ character. If it does not, it returns false.
//
function isAlphaNumeric($string) {
	return ctype_alnum($string);
}

// END OF FILE

