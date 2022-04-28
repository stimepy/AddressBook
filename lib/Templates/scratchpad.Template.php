<?php

class scratchpadTemplate
{
    function __constructor(){
    }

    function scratchpadMain($body, $lang, $fileURL){
        $output = "<body>
            <form name=\"Scratchpad\" action=\"". $fileURL['FILE_SCRATCHPAD']."\" method=\"post\">
                <input type=\"hidden\" name=\"saveNotes\" value=\"YES\">
                <table class =\"width570\">
                    <tr>
                        <td CLASS=\"navMenu\">
                            <a href=\"#edit\"> ". $lang['BTN_EDIT']."</a>
                            <a href=\"".$fileURL['FILE_LIST'] ."\">". $lang['BTN_LIST'] ."</A>
                        </td>
                    </tr>
                    <tr>
                        <td class=\"headTitle\">
                            ". $lang['TITLE_SCRATCH'] ."
                        </td>
                    </tr>
                    <tr>
                        <td class=\"infoBox\">
                            <table style='width:568px'>
                                <tr style=\"vertical-align: top\">
                                    <td CLASS=\"data\">
                                        ". $lang['SCRATCH_HELP'] ."
                                    </td>
                                </tr>
                                <TR style=\"vertical-align: top\">
                                    <TD CLASS=\"listDivide500\">&nbsp;</TD>
                                </TR>
                                <TR style=\"vertical-align: top\">
                                    <TD WIDTH=550 CLASS=\"data550\">";
    }

}