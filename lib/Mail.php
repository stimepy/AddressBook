<?php

use PHPMailer\PHPMailer\PHPMailer;

require '../vendor/autoload.php';


class AddressEmail{
    private $mail;

    function __constuctor(){
        global $lang;

        $this->mail = new PHPMailer();
        $this->mail->isSendmail();
        $this->mail->CharSet = $lang['CHARSET'];
        // $this->mail->SetLanguage(LANGUAGE_CODE, "lib/phpmailer/language/");  default language is english.
        $this->mail->WordWrap = 78;

    }

    /**
     * @return string
     */
    function CreateSendPost(){
        $this->mail->setFrom($_POST['mail_from'],$_POST['mail_from_name']);

        $this->setAddresses($_POST['mail_to'],$_POST['mail_cc'], $_POST['mail_bcc']);
        $this->mail->Subject = stripslashes($_POST['mail_subject']);
        $this->mail->Body = stripslashes($_POST['mail_body']);

//      ** SEND! **
        if (!$this->mail->Send()) {
            $msg = 'Mailer Error: ' . $this->mail->ErrorInfo;
        }
        else{
            $msg = 'Success';
        }
        return $msg;
    }

    /**
     * @param $to
     * @param $cc
     * @param $bcc
     * @return void
     * // GET EMAIL ADDRESSES
     * There are two ways that mailto.php can send e-mail addresses, based on the two ways
     * of sending. The first is the mailing list and addresses are stored in $_POST['mail_to']
     * as an array. The second method allows the user to write in e-mail addresses and they will
     * be stored in $_POST['mail_to'] as a string. In the event that it is a string (with
     * commas separating each address) we must break up that string into an array.
     * Note: We can split on commas only. any resulting whitespace is trimmed automatically by PHPMailer.
     */
    private function setAddresses($to,$cc,$bcc){
        $to = $this->makeArrayFromString($to);
        $cc = $this->makeArrayFromString($cc);
        $bcc = $this->makeArrayFromString($bcc);
        for ($a=0; $a < count($to); $a++) {
            $this->mail->AddAddress($to[$a]);
        }
        for ($a=0; $a < count($cc); $a++) {
            $this->mail->AddCC($cc[$a]);
        }
        for ($a=0; $a < count($bcc); $a++) {
            $this->mail->AddBCC($bcc[$a]);
        }
    }

    /**
     * @param $values
     * @return string[]
     *
     */
    private function makeArrayFromString($values){
        if (is_string($values)) {
            return explode(",", $values);
        }
        return $values;
    }

    /**
     * @return void
     */
    private function defaultFrom(){
        $this->mail->setFrom('noreply@'.$_SERVER['SERVER_NAME'], 'noreply@'.$_SERVER['SERVER_NAME']);
    }
}