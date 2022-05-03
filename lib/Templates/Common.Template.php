<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.2.01
 *
 * Author: stimepy@aodhome.com
 * Last Modified: 4-28-2022
 ****************************************************************
 *  Common.Template.php
 *  Common use HTML template
 *
 *************************************************************/

/**
 * @param $title
 * @param $language
 * @param $javascriptfile
 * @return string
 */
function webheader($title, $language, $javascriptfile = -1){

    $output ="<html>
        <head>
            <title> $title </title>
            <link rel=\"stylesheet\" href=\"./lib/Stylesheet/styles.css\">            
            <meta http-equiv=\"content-type\" content=\"text/html; charset=$language\">";
    if($javascriptfile != -1){
        $output .="            <script src=\"./lib/Javascript/".$javascriptfile."\"></script>";
    }
    $output .="    </head>";

    return $output;
}



//
// PRINT FOOTER - printFooter();
// Prints a table row containing version, copyright, and links.
//
/**
 * @return string
 */
function printFooter() {
    global $lang;

    return "  <tr>
        <td CLASS=\"data\" align =\"center\">
            <br /><br /><b>". $lang['TITLE_TAB'] . "</b> " . $lang['FOOTER_VERSION'] ." ". VERSION_NO. " | <a href=\"" . URL_HOMEPAGE . "\" target=\"_blank\">" . $lang['FOOTER_HOMEPAGE_LINK'] . "</a> | <a href=\"" . URL_SOURCEFORGE . "\" target=\"_blank\">". $lang['FOOTER_SOURCEFORGE_LINK'] ."</a>
            <br />" . $lang['FOOTER_COPYRIGHT'] . "<br />
        </td>
    </tr>";
}

/**
 * @param $body
 * @return void
 */
function birthdaylist($body){
    $output = "                <table WIDTH=\"100%\" BORDER=0 CELLPADDING=0 CELLSPACING=0>
                      <tr>
                        <td class=\"headTextcspan3\"> 
                            ". $body['langbirth'] ."
                        </td>
                      </tr>";
    $output .=outputloop($body['bithinfo']);
    $output .="                </table>";


}

/**
 * @param $item
 * @return string
 */
function outputloop($item, $addbreak = false){
    if(empty($item)){return "";}
    $maxx = count($item);
    $x = 0;
    $text = '';
    while($maxx < $x ){
        if($addbreak){
            $text .= "<br>";
        }
        $text .=$item[$x];
        $x++;
    }
    return $text;
}

/**
 * @param $input
 * @return void
 */
function Display($input){
    echo $input;
}


/**
 * @param $width
 * @param $rows
 * @param $title
 * @param $data
 * @param $wrap
 * @return string
 */
function createTextArea($width, $rows, $title, $data, $wrap = 'off'){
    $output = "<textarea style=\"width:".$width."px;\" rows=".$rows." class=\"formTextarea\" name=\"".$title."\" wrap=".$wrap.">";
    if(is_array($data)){
        $output.= outputloop($data);
    }
    else{
        $output.= $data;
    }
    $output .= "</textarea>";
    return $output;
}

/**
 * @param $value
 * @return string
 */
function hasValueOrBlank($value){
    return ((!empty($value)) ? stripslashes($value) : '');
}

/**
 * @param $country
 * @return mixed
 */
function sortandSetCountry($country){
    foreach ($country as $country_id=>$val) {
        $countrySorted[$country_id] = strtr($val,"��������ʀ������������������������������������������", "AAAAAAAEEEEIIIINOOOOOUUUUYaaaaaaeeeeiiiinooooouuuuyy");
    }
    asort($countrySorted);
    return $countrySorted;
}

/**
 * @param $errorMessage
 * @return string
 */
function errorPleaseclicktoTeturn($errorMessage){
        return "<body>
                <p><b>".$errorMessage."<a href=\"".FILE_LIST."\">Click here to return.</b></a></p>
                </body>
                </html>";
}

/**
 * @param $body
 * @param $lang
 * @return string
 */
function createGroupOptions($body, $lang){
    $output = $lang['GROUP_SELECT'] ."<select name=\"groupid\" class=\"formSelect\" onChange=\"document.selectGroup.submit();\">";
    for ($groupcount = 0; $groupcount < $body['G_count']; $groupcount++) {
        $sel = "";
        $group = $body['G_' . $groupcount];

        if ($body['G_selected'] == $group['groupid']) {
           $sel = "Selected";
        }
        $output .= "    <option value=" . $group['groupid'] . " " . $sel . ">" . $group['groupname'] . "</option>\n";
    }
    $output .= "</select>";
    return $output;
}

/**
 * @param $label
 * @param $useURL
 * @param $url
 * @return string
 */
function ButtonUrl($label,$useURL = false, $url = null){
    $type = "submit";
    $theURL = "";
    if($useURL){
        $theURL = " onclick=\"location.href='".$url."'\"";
        $type = "button";
    }
    return "<button class=\"urlButton\" type=\"". $type ."\"$theURL>". $label ."</button>";
}



