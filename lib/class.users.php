<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-17-2022
 ****************************************************************
 *  class.user.php
 *  Stuff to do with users
 *
 *************************************************************/

class users
{
//
// CHECK FOR LOGIN - checkForLogin(usertype, ...);
// This function takes a variable number of arguments which defines what user types are allowed.
//
// There are a lot of issues with the security on this check, please check forums for more details....
// Security code currently does not work and has been commented out.
    public function checkForLogin() {
        global $lang, $globalSqlLink;
        // session_start();


        // IF AUTHENTICATION IS TURNED OFF (requires database connection)

        $globalSqlLink->SelectQuery( 'requireLogin', TABLE_OPTIONS, NULL, 'LIMIT 1');
        $requireLogin = $globalSqlLink->FetchQueryResult();
        if($requireLogin == -1) {
            die("Unable to retrieve options in authorization check.");
        }

        //No login required I guess
        if ($requireLogin['requireLogin'] != 1) {
            // If there is no current user logged in, set the user to @auth_off.
            // If there is a user logged in, it will proceed normally.
            if (!isset($_SESSION['username'])) {
                $_SESSION['username'] = "@auth_off";
                $_SESSION['usertype'] = "guest";
            }
        }

        // Redirect user to the login page if correct session variables are not defined.
        if ( !isset($_SESSION['username']) || !isset($_SESSION['usertype']) || (isset($_SESSION['abspath']) && $_SESSION['abspath'] != dirname($_SERVER['SCRIPT_FILENAME'])) ) {
            session_destroy();
            header("Location: " . FILE_INDEX);
            exit();
        }

        // Refuse access to restricted users
        // allowed users must be specified by name in the function argument list.
        $numargs = func_num_args();
        if ($numargs >= 1) {
            $arg_list = func_get_args();
            for ($i = 0; $i < $numargs; $i++) {
                if ($_SESSION['usertype'] == $arg_list[$i]) {
                    $userAllowed = 1;
                }
            }
            if ($userAllowed != 1) {
                $output=webheader('Address Book - Access Denied', $lang['CHARSET']);
                $output .=errorPleaseclicktoTeturn('You do not have permission to conduct this operation.');
                display($output);
                exit();
            }
        }
    }

    function Logout($options){
        global $lang;
        session_destroy();
        require_once('languages/' . $options->getlanguage() . '.php');
        // PRINT MESSAGE
        header("Location: " . FILE_INDEX); //required to force site language to override user language at sign in screen
        return  $lang['MSG_LOGGED_OUT'];
    }

    function Authorization(){
        global $globalSqlLink, $lang;
        // LOOK FOR USERNAME AND PASSWORD IN THE DATABASE.
        $globalSqlLink->SelectQuery('username, usertype, is_confirmed', TABLE_USERS, "username='" . $_POST['username'] . "' AND password=MD5('" . $_POST['password'] . "')", NULL);
        $t_getUser = $globalSqlLink->FetchQueryResult();

        // THE USERNAME IS FOUND AND ACCOUNT IS CONFIRMED
        if (($globalSqlLink->GetRowCount() != 0) && ($t_getUser[0]['is_confirmed'] == 1)) {
            // REGISTER SESSION VARIABLES
            $_SESSION['username'] = $t_getUser[0]['username'];
            $_SESSION['usertype'] = $t_getUser[0]['usertype'];
            if (!isset($_SESSION['abspath'])) {
                $_SESSION['abspath'] = dirname($_SERVER['SCRIPT_FILENAME']);
            }
            // REDIRECT TO LIST
            header("Location: " . FILE_LIST);
            exit();
        }

        // ACCOUNT MUST BE CONFIRMED
        elseif (($globalSqlLink->GetRowCount() != 0) && ($t_getUser[0]['is_confirmed'] != 1)) {
            // END SESSION
            session_destroy();
            // PRINT ERROR MESSAGE AND LOGIN SCREEN
            $errorMsg = $lang['ERR_USER_CONFIRMED_NOT'];
        }

        // WRONG USERNAME
        else {
            // END SESSION
            session_destroy();
            // PRINT ERROR MESSAGE AND LOGIN SCREEN
            $errorMsg = $lang['MSG_LOGIN_INCORRECT'];
        }
        return $errorMsg;
    }

    function getUserInfoById($id = null, $orderBy = null){
        global $globalSqlLink;
        $globalSqlLink->SelectQuery('*', TABLE_USERS, $id, $orderBy);
        return $globalSqlLink->FetchQueryResult();
    }

    function getUserInfoByUsername($username = null, $orderBy = null){
        global $globalSqlLink;
        $globalSqlLink->SelectQuery('*', TABLE_USERS, '\'$username\'', $orderBy);
        return $globalSqlLink->FetchQueryResult();
    }

    function insertUserPage()
    {
        global $globalSqlLink, $lang;
        $newuserName = "";
        if ((!empty($_POST['newuserName'])) && (isAlphaNumeric($_POST['newuserName']))) {
            $insert[username] = $_POST['newuserName'];
            if ($_POST['newuserPass'] == $_POST['newuserConfirmPass']) {
                $insert[password] = MD5($_POST['newuserPass']);
                $insert[usertype] = $_POST['newuserType'];
                $insert[email] = $_POST['newuserEmail'];   // NOT VALIDATED
                $insert[is_confirmed] = 1;
                $globalSqlLink->InsertQuery(TABLE_USERS, $insert);
                // $sql = "INSERT INTO ". TABLE_USERS ." (username, usertype, password, email, is_confirmed) VALUES ('$newuserName', '$newuserType', MD5('$newuserPass'), '$newuserEmail', 1)";

                /*$opps = mysql_errno();
                if($opps ==1062) {
                    $actionMsg = $lang['ERR_USERNAME_DUPL'];
                    break;
                }elseif ($opps != 0){
                    die(ReportSQLError($sql));
                }*/
                $message = $newuserName . ' ' . $lang['USR_ADDED'];
            } else {
                $message = $lang['ERR_USER_PASSWORD_SHORT'];
            }
        } else {
            $message= $lang['ERR_USERNAME_ILLEGAL_CHARS'];
        }
    }

    function deleteUser(){
        global $glob1alSqlLink, $lang;

        if (empty($_GET['id'])) {
           die($lang['ERR_USERNAME_NONE']);
        }
        // Check to see if user exists in the database
        // $sql = "SELECT username, usertype FROM ". TABLE_USERS ." WHERE id=". $_GET['id'] ." LIMIT 1";
        $glob1alSqlLink->SelectQuery('username, usertype', TABLE_USERS, "id=". $_GET['id'], " LIMIT 1");
        $deluser = FetchQueryResult();
        //	or die(ReportSQLError($sql));
        if ($glob1alSqlLink->GetRowCount()<1) {
            die($lang['ERR_USERNAME_NON_EXIST']);
            return '';
        }
        // Get the username and type
        // $deluser = mysql_fetch_array($deluser);
        $deluserType = $deluser['usertype'];
        $deluserName = $deluser['username'];
        // Check to see if user is last remaining admin

        if($_GET['id'] != 1) {
            $glob1alSqlLink->DeleteQuery("id=" . $_GET['id'], TABLE_USERS);
            return $deluserName . ' ' . $lang['USR_DELETED'];
        }
    }

    function confirm(){
        global $globalSqlLink, $lang;
        $update['is_confirmed'] = 1;
        $globalSqlLink->UpdateQuery($update, TABLE_USERS, "id =". $_GET['id']);
        $holder = explode(".",$lang['ERR_USER_HASH_CONFIRMED']); //rather than make new $lang[var], chop of first sentence of this thing
        return  $holder[0];
    }

    function changePass(){
        global $globalSqlLink, $lang;
        if ($_POST['passwordNew'] == $_POST['passwordNewRetype']) {
            // SQL query checks to make sure username and old password is corrrect.
            $update['password']=MD5("'". $_POST['passwordNew']."'");
            $globalSqlLink->UpdateQuery($update, TABLE_USERS, "username='". $_SESSION['username'] ."' AND password= '".MD5( $_POST['passwordOld'])."'");
            //$sql = "UPDATE ". TABLE_USERS ." SET password=MD5('". $_POST['passwordNew'] ."') WHERE username='". $_SESSION['username'] ."' AND password=MD5('". $_POST['passwordOld'] ."') LIMIT 1";
            //$updatePassword = mysql_query($sql, $db_link)
            //	or die(ReportSQLError($sql));
            if ($globalSqlLink->GetRowCount()<1) {
                $body['actionMsg'] = $lang['ERR_USER_PASSWORD_WRONG'];
            }
            else {
                $body['actionMsg'] = $lang['ERR_USER_PASSWORD_CHANGED'];
            }
        }
        else {
            $body['actionMsg'] = $lang['ERR_USER_PASSWORD_SHORT'];
        }
    }

    function changeEmail(){
        global $globalSqlLink, $lang;

        $feedback= "";
        $username = $_SESSION['username'];
        $new_email = $_POST['emailNew'];
        if (validate_email($new_email)) {
            $hash = md5($new_email."WTFISTHIS");
            //change the confirm hash in the db but not the email -
            //send out a new confirm email with a new hash to complete the process
            $update['confirm_hash'] ="'".$hash."'";
            $update['is_confirmed'] = 0;
            $globalSqlLink->UpdateQuery($update, TABLE_USERS, "username='".$username."'" );
            //$sql = "UPDATE " .TABLE_USERS. " SET confirm_hash='$hash' , is_confirmed = 0 WHERE username='$username' LIMIT 1";
            // $result = mysql_query($sql, $db_link);
            if ($globalSqlLink->GetRowCount()<1) {
                //if (!$result || mysql_affected_rows($result) < 1) {
                $feedback .= ' There was a problem updating your e-mail address. ';
                // This used to double check for incorrect username and password, but these
                // are things that should already hopefully be taken care of in a login screen.
                // However, entering the same e-mail address as before will also cause
                // mysql_affected_rows to equal 0, so the error message has changed.
            } else {
                $mail = new PHPMailer();
                $mail->SetLanguage(LANGUAGE_CODE, "lib/phpmailer/language/");
                $mail->From = 'noreply@'.$_SERVER['SERVER_NAME'];
                $mail->FromName = 'noreply@'.$_SERVER['SERVER_NAME'];
                $message = $lang['SALUTATION']." $username,\n".
                    $lang['EMAIL_CHANGE'].
                    "\n\n  http://" .$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']). "/register.php?mode=confirm&hash=$hash&email=$new_email";
                $mail->Subject = $lang[TAB].' - '.$lang['EMAIL_CHANGE_SUBJ'];
                $mail->Body  = $message ;
                $mail->AddAddress($new_email);
                if (!$mail->Send()) {
                    die($lang['ERR_MAIL_NOT_SENT'] . $mail->ErrorInfo);
                }else{
                    $feedback = $lang['MSG_EMAIL_CHANGED'];
                }
            }
        } else {
            $feedback.= $lang['ERR_USER_EMAIL_INVALID'];
        }
        return $feedback;
    }
}